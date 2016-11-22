<?php
/**
 * Created by PhpStorm.
 * User: HSF
 * Date: 2016/10/14
 * Time: 15:10
 */
namespace App\Http\Controllers\Order;
use App\Http\Controllers\Controller;
use App\Http\ERR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller{

    /**
     * Order status
     * 为管理员、开票员权限的订单列表中  按状态查询服务
     */
    private $_status = [
        //'10'    => '生成订单，等待审核',
        '20'    => '订单无效，待修改',
        '30'    => '订单已取消',
        '40'    => '审核通过，待付款',
        '50'    => '付款中',
        '60'    => '支付成功'
    ];


    public function __construct()
    {
        $this->middleware('guest');
    }


    /** 生成订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateOrder(Request $request)
    {
        $role = session('user_role');


        $data = $request->all();
        $address = isset($data['address']) ? $data['address'] : '';
        $product = isset($data['product']) ? $data['product'] : [];

        if($address == '' || $product == ''){
            return response()->json(ERR::GetError('ERR_ORDER_PARAM'));
        }

        $customer = '';
        if($role == 30){
            $customer_type = isset($data['type']) ? $data['type'] : '';
            $customer_id = isset($data['customer']) ? $data['customer'] : '';

            if($customer_id == '' || !in_array($customer_type,['客户','供应商'])){
                return response()->json(ERR::GetError('ERR_ORDER_PARAM'));
            }

            //判断客户与地址信息是否匹配
            $user_info = DB::table('address')->where('id',$address)->where('user_id',$customer_id)->count();
            if(!$user_info){
                return response()->json(ERR::GetError('ERR_ORDER_PARAM'));
            }
            $customer_name = DB::table('user')->where('id',$customer_id)->value('name');

            $customer = $customer_type .'/'. $customer_name;
        }

        $orderNo = self::gen_order_no();

        $product_data = [];
        $total_price = 0;
        foreach($product as $v){

            $pid = isset($v['pId']) ? $v['pId'] : '';
            $qty = isset($v['qty']) ? $v['qty'] : '';

            if($pid == "" || $qty ==""){
                return response()->json(ERR::GetError('ERR_ORDER_PARAM'));
            }

            $p = DB::table('product')->where('id',$pid)->first();
            if(!$p){
                return response()->json(ERR::GetError('ERR_ORDER_PRODUCT'));
            }

            $product_data[]=[
                'order_id'      => $orderNo,
                'product_id'    => $pid,
                'quantity'      => $qty,
                'price'         => $p->price,
                'created_at'    => date('Y-m-d')
            ];

            $total_price = $total_price + ($qty * $p->price);
        }

        $order_data = [
            'order_id'      => $orderNo,
            'price'         => $total_price,
            'creator'       => session('user_tel'),
            'target_customer'=> $customer,
            'address'       => $address,
            'status'        => 10,
            'created_at'    => date('Y-m-d')
        ];

        DB::beginTransaction();

        $insert_order = DB::table('order')->insert($order_data);
        if(!$insert_order){
            DB::rollBack();
            return response()->json(ERR::GetError('ERR_ORDER_INSERT'));
        }

        $inst_order_product = DB::table('order_product')->insert($product_data);
        if(!$inst_order_product){
            DB::rollBack();
            return response()->json(ERR::GetError('ERR_ORDER_PRODUCT_INSERT'));
        }
        DB::commit();

        return response()->json(ERR::GetError('OK'));
    }

    /**
     * 生成订单号20位
     * @return string
     */
    private static function gen_order_no(){
        return date('ymd').substr(implode(NULL, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8).mt_rand(1000, 9999);
    }

    /**获取未审核订单：管理员、开票员权限
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUncheckedOrder()
    {
        $permission = session('user_permission');
        if($permission > 30){
            abort('403');
        }

        $order = DB::table('order as o')
            ->leftJoin('user as u','o.creator','=','u.telephone')
            ->where('o.status',10)
            ->orderBy('o.created_at','desc')
            ->select('o.order_id','o.target_customer as target','u.name')
            ->paginate(20);

        $count = DB::table('order')->where('status',10)->count();
        return view('order.uncheckedOrder',['order'=>$order,'count'=>$count]);
    }


    /**订单审核：查看订单详细信息
     * @param $orderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getUncheckedOrderDesc($orderId)
    {
        $permission = session('user_permission');
        if($permission > 30){
            abort('403');
        }

        $orderId = htmlentities($orderId);

        $order = DB::table('order')->where('order_id',$orderId)->count();
        if($order < 1){
            abort('404');
        }

        $order = DB::table('order as o')
            ->leftJoin('user as u','o.creator','=','u.telephone')
            ->leftJoin('address as a','o.address','=','a.id')
            ->where('o.order_id',$orderId)
            ->select('o.*','a.address','u.name')
            ->first();

        $product = DB::table('order_product as op')
                   ->leftJoin('product as p','op.product_id','=','p.id')
                   ->where('op.order_id',$orderId)
                   ->select('op.quantity as qty','op.price','p.product')
                   ->get();

        return view('order.uncheckedOrderDesc',['order'=>$order,'product'=>$product,'role'=>session('user_role')]);
    }


    /**订单审核：更新订单状态
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateUncheckedOrder(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 30){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $orderId    = isset($data['id'])   ? $data['id'] : '';
        $status     = isset($data['state'])? $data['state'] : '';

        if(!in_array($status,[20,30,40])){
            return response()->json(ERR::GetError('ERR_CHECK_STATUS'));
        }

        $order = DB::table('order')->where('order_id',"$orderId")->first();
        if(!$order || $order->status != 10){
            return response()->json(ERR::GetError('ERR_ORDER_EXCEPTION'));
        }

        $upd = [
            'status'    => $status,
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $res = DB::table('order')->where('order_id',$orderId)->update($upd);
        if(!$res){
            return response()->json(ERR::GetError('ERR_CHECK_ORDER'));
        }

        return response()->json(ERR::GetError('OK'));
    }



    /**订单列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getOrderList()
    {
        $permission = session('user_permission');
        if($permission > 30){
            //普通用户订单列表
            $return = $this->commonOrderList();
            return view('order.commonOrderList',['order'=>$return['order'],'totalCount'=>$return['count']]);
        }

        //管理员、开票员权限订单列表
        $order = DB::table('order as o')
            ->leftJoin('user as u','o.creator','=','u.telephone')
            ->where('o.status','<>','10')
            ->orderBy('created_at','desc')
            ->select('o.*','u.name')
            ->paginate(20);

        return view('order.manageOrderList',['order'=>$order,'status'=>$this->_status]);
    }


    /**
     * 获取普通用户订单列表
     */
    private function commonOrderList()
    {
        $tel = session('user_tel');

        $order = DB::table('order')
            ->where('creator',$tel)
            ->orderBy('created_at','desc')
            ->paginate(1);

        $count = DB::table('order')->where('creator',$tel)->count();
        return ['order'=>$order,'count'=>$count];
    }


    /**普通权限角色订单列表详情
     * @param $orderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function commonOrderListDesc($orderId)
    {
        $order = DB::table('order as o')
            ->leftJoin('order_status as os','o.order_id','=','os.order_id')
            ->leftJoin('address as a','o.address','=','a.id')
            ->where('o.order_id',"$orderId")
            ->select('o.*','a.address','os.pay_comment','os.shipping')
            ->first();

        $product = DB::table('order_product as op')
            ->leftJoin('product as p','op.product_id','=','p.id')
            ->where('op.order_id',$orderId)
            ->select('op.product_id as pId','op.quantity as qty','op.price','p.product')
            ->get();

        return view('order.commonOrderListDesc',['order'=>$order,'product'=>$product,'role'=>session('user_role')]);
    }


    /** 业务员 修改 被打回的订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editOrderById(Request $request)
    {
        $role = session('user_role');
        //是否为业务员
        if($role != 30){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $orderId = isset($data['id'])       ? $data['id'] : '';
        $product = isset($data['product'])  ? $data['product'] : [];

        DB::beginTransaction();
        $order_price = 0;
        foreach($product as $k=>$value){
            $pid = isset($value['pid']) ? $value['pid'] : '';
            $qty = isset($value['qty']) ? $value['qty'] : -1;

            if($qty < 0){
                return response()->json(ERR::GetError('ERR_ORDER_PRODUCT_QTY'));
            }
            $order = DB::table('order_product')->where('order_id',$orderId)->where('product_id',$pid)->first();
            if(!$order){
                DB::rollBack();
                return response()->json(ERR::GetError('ERR_ORDER_EXCEPTION'));
            }
            $order_price += $qty*$order->price;
            $upd_data = [
                'quantity'  => $qty,
                'updated_at'=> date('Y-m-d H:i:s')
            ];
            $upd = DB::table('order_product')->where('order_id',$orderId)->where('product_id',$pid)->update($upd_data);
            if(!$upd){
                DB::rollBack();
                return response()->json(ERR::GetError('ERR_ORDER_EDIT'));
            }
        }
        $upd_order = [
            'price'     =>  $order_price,
            'status'    =>  10,
            'updated_at'=>  date('Y-m-d H:i:s')
        ];
        $res = DB::table('order')->where('order_id',$orderId)->update($upd_order);
        if(!$res){
            DB::rollBack();
            return response()->json(ERR::GetError('ERR_ORDER_EDIT'));
        }
        DB::commit();

        return response()->json(ERR::GetError('OK'));
    }


    /**下单用户 取消订单
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelOrder(Request $request)
    {
        $role = session('user_role');
        if(!in_array($role,[30,40,50])){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }
        $data = $request->all();
        $orderId = isset($data['id']) ? $data['id'] : '';

        $order = DB::table('order')->where('order_id',$orderId)->count();
        if(!$order){
            return response()->json(ERR::GetError('ERR_ORDER_EXCEPTION'));
        }

        $upd = [
            'status'    => 30,
            'updated_at'=>date('Y-m-d H:i:s')
        ];
        $res = DB::table('order')->where('order_id',$orderId)->update($upd);
        if(!$res){
            return response()->json(ERR::GetError('ERR_ORDER_CANCEL'));
        }
        return response()->json(ERR::GetError('OK'));
    }

    /**管理员、开票员权限订单列表：更新订单（支付状态，支付备注，物流信息）
     * @param $orderId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function updateOrderById($orderId)
    {
        $permission = session('user_permission');
        if($permission > 30){
            abort('403');
        }

        $order = DB::table('order')->where('order_id',$orderId)->first();
        if(!$order){
            abort('404');
        }

        $order = DB::table('order as o')
                ->leftJoin('order_status as os','o.order_id','=','os.order_id')
                ->where('o.order_id',"$orderId")
                ->select('o.order_id','o.status','os.pay_comment','os.shipping')
                ->first();

        return view('order.manageUpdateOrder',['order'=>$order]);
    }


    /**管理员、开票员权限：更新订单
     * 更新订单状态为：50,60 或保持不变
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postUpdateOrderById(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 30){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = array_map('htmlentities',$request->all());

        $orderId    = isset($data['id'])        ? $data['id']       : '';
        $state      = isset($data['state'])     ? $data['state']    : '';
        $payment    = isset($data['payment'])   ? trim($data['payment'])  : '';
        $shipping   = isset($data['shipping'])  ? trim($data['shipping']) : '';

        $order = DB::table('order')->where('order_id',"$orderId")->first();
        if(!$order){
            return response()->json(ERR::GetError('ERR_ORDER_EXCEPTION'));
        }

        //判断更新的订单状态 是否 合法
        if(!in_array($state,[$order->status,50,60])){
            return response()->json(ERR::GetError('ERR_ORDER_STATUS'));
        }

        DB::beginTransaction();
        $upd_order = [
            'status'    => $state,
            'updated_at'=> date('Y-m-d H:i:s')
        ];
        $res_order = DB::table('order')->where('order_id',$orderId)->update($upd_order);
        if(!$res_order){
            DB::rollBack();
            return response()->json(ERR::GetError('ERR_ORDER_UPDATE'));
        }
        $upd_order_state = [
            'pay_comment'   => $payment,
            'shipping'      => $shipping,
            'updated_at'    => date('Y-m-d H:i:s')
        ];
        $res_order_state = DB::table('order_status')->where('order_id',$orderId)->update($upd_order_state);
        if(!$res_order_state){
            DB::rollBack();
            return response()->json(ERR::GetError('ERR_ORDER_UPDATE'));
        }
        DB::commit();
        return response()->json(ERR::GetError('OK'));
    }


    /**管理员、开票员权限 订单查询
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageSearch(Request $request)
    {
        $data = $request->all();
        $orderId = isset($data['id'])    ? $data['id']      : '';
        $state   = isset($data['state']) ? $data['state']   : '';
        $pageIndex = isset($data['pageIndex'])  ? $data['pageIndex'] : 1;

        $sql = "select o.*,u.name from xjss_order o
                left join xjss_user u on o.creator = u.telephone
                where 1";

        $param = [];
        if($orderId){
            $sql .= ' and o.order_id = :orderId';
            $param['orderId'] = $orderId;
        }
        if($state != -1){
            $sql .= ' and o.status = :state';
            $param['state'] = $state;
        }

        $count = count(DB::select($sql,$param));
        $pageCount = ceil($count / 20);
        $offset = ($pageIndex-1)*20;
        $length = 20;

        $sql .= " order by o.created_at desc limit $offset,$length";

        $order = DB::select($sql,$param);
        $order=json_decode(json_encode($order),true);

        $arr=['order'=>$order,'pageCount'=>$pageCount,'searchOrder'=>$count];
        return response()->json($arr);
    }


}