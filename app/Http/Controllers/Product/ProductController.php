<?php
/**
 * Created by PhpStorm.
 * User: HSF
 * Date: 2016/10/10
 * Time: 12:08
 */

namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Http\ERR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }


    /**查看产品列表：所有角色都可以查看
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAllProducts()
    {
        $permission = session('user_permission');

        if($permission > 20){   //非管理员的产品列表
            $products = DB::table('product')->orderBy('created_at','desc')->paginate(1);
            return view('product.commonProductList',['product'=>$products]);
        }else{  //管理员页面

            $products = DB::table('product as p')
                        ->leftJoin('supplier as s','p.supplier','=','s.id')
                        ->leftJoin('category as c','p.category','=','c.id')
                        ->select("p.*","s.supplier","c.category")
                        ->orderBy('p.created_at','desc')
                        ->paginate(20);

            $supply = DB::table('supplier')->get();
            $cate   = DB::table('category')->get();

            return view('product.adminProductList',['product'=>$products,'supply'=>$supply,'category'=>$cate]);
        }
    }

    /**非管理员权限：查看产品详情
     * @param $product_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getProductDesc($product_id)
    {
        $exist = DB::table('product')->where('id',$product_id)->first();
        if(!$exist){
            return response()->json(ERR::GetError('ERR_PRODUCT_NOT_EXIST'));
        }

        $role = session('user_role');
        $product = DB::table('product as p')
                    ->leftJoin('supplier as s','p.supplier','=','s.id')
                    ->leftJoin('category as c','p.category','=','c.id')
                    ->select("p.*","s.supplier","c.category")
                    ->where('p.id',$product_id)
                    ->first();
        $customer = DB::table('user')->where('role',50)->select('name','telephone')->get();

        return view('product.description',['product'=>$product,'role'=>$role,'customer'=>$customer]);
    }


    /**业务员下单：获取目标客户列表
     * @param $role
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCustomerByType($role)
    {
        if(!in_array($role,[40,50])){
            return response()->json(ERR::GetError('ERR_UNKNOWN'));
        }

        $customer = DB::table('user')->where('role',$role)->select('name','telephone')->get();

        $ret = ERR::GetError('OK');
        $ret['customer'] = $customer;
        return response()->json($ret);
    }



    /**管理员：搜索产品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchProduct(Request $request)
    {
        $data = array_map('trim',$request->all());
        $data = array_map('htmlentities',$data);

        $product_name   = isset($data['product'])  ? $data['product'] : '';
        $supplier       = isset($data['supplier']) ? $data['supplier']:'';
        $cate           = isset($data['cate'])     ? $data['cate'] :   '';
        $pageIndex      = isset($data['page']) ? $data['page'] : 1 ;

        $sql = "select p.*,s.supplier,c.category from xjss_product p
                left join xjss_supplier s on p.supplier = s.id
                left JOIN xjss_category c on p.category = c.id
                where 1";

        $param = [];
        if($product_name){
            $sql .= ' and p.product = :name';
            $param['name'] = $product_name;
        }
        if($supplier && $supplier != -1){
            $sql .= ' and p.supplier = :supplier';
            $param['supplier'] = $supplier;
        }
        if($cate && $cate != -1){
            $sql .= ' and p.category = :cate';
            $param['cate'] = $cate;
        }

        $count = count(DB::select($sql,$param));
        $pageCount = ceil($count / 20);
        $offset = ($pageIndex-1)*20;
        $length = 20;

        $sql .= " order by p.created_at desc limit $offset,$length";

        $product = DB::select($sql,$param);
        $product=json_decode(json_encode($product),true);

        $arr=['product'=>$product,'pageCount'=>$pageCount,'countProduct'=>$count];
        return response()->json($arr);
    }


    /**编辑产品
     * @param $product_id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showEditPage($product_id)
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }

        $exist = DB::table('product')->where('id',$product_id)->first();
        if(!$exist){
            return view('product.Notice',['flag'=>'notExist']);
        }

        $product = DB::table('product')->where('id',$product_id)->first();
        $supply = DB::table('supplier')->get();
        $category = DB::table('category')->get();

        $return = ['supply'=>$supply,'category'=>$category,'product'=>$product];
        return view('product.editProduct',$return);
    }


    /**编辑产品信息
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function editProduct(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = array_map('htmlentities',$request->all());
        $ret = self::validProductParam($data);
        if($ret == '1'){
            return response()->json(ERR::GetError('ERR_PRODUCT_PARAM'));
        }
        $id = isset($data['id'])    ?  $data['id']  :  '';
        $exist = DB::table('product')->where('id',$id)->count();
        if(!$exist){
            return response()->json(ERR::GetError('ERR_PRODUCT_NOT_EXIST'));
        }

        $count = DB::table('product')->where('id','<>',$id)->where('product',$data['name'])->count();
        if($count){
            return response()->json(ERR::GetError('ERR_PRODUCT_EXIST'));
        }

        $product = [
            'product'   => $data['name'],
            'price'     => $data['price'],
            'supplier'  => $data['supply'],
            'category'  => $data['cate'],
            'component' => $data['makeup'],
            'major_function'=>$data['function'],
            'usage'     => $data['usage'],
            'specification'=> $data['spec'],
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $res = DB::table('product')->where('id',$id)->update($product);
        if(!$res){
            return response()->json(ERR::GetError('ERR_PRODUCT_EDIT'));
        }
        return response()->json(ERR::GetError('OK'));
    }


    /**添加产品
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function showAddProduct()
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }
        $supply = DB::table('supplier')->get();
        $cate   = DB::table('category')->get();
        return view('product.add',['supply'=>$supply,'category'=>$cate]);
    }


    /**添加产品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addProduct(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = array_map('htmlentities',$request->all());
        $ret = self::validProductParam($data);

        if($ret == '1'){
            return response()->json(ERR::GetError('ERR_PRODUCT_PARAM'));
        }

        $exist = DB::table('product')->where('product',$data['name'])->first();
        if($exist){
            return response()->json(ERR::GetError('ERR_PRODUCT_EXIST'));
        }

        $product = [
            'product'   => $data['name'],
            'price'     => $data['price'],
            'supplier'  => $data['supply'],
            'category'  => $data['cate'],
            'component' => $data['makeup'],
            'major_function'=>$data['function'],
            'usage'         => $data['usage'],
            'specification' => $data['spec'],
            'created_at'=> date('Y-m-d')
        ];

        $res = DB::table('product')->insert($product);
        if(!$res){
            return response()->json(ERR::GetError('ERR_PRODUCT_ADD'));
        }
        return response()->json(ERR::GetError('OK'));
    }


    /** 删除产品
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delProduct(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $id = isset($data['id'])  ? $data['id'] : 0;

        $res = DB::table('product')->where('id',$id)->first();
        if(!$res){
            return response()->json(ERR::GetError('ERR_PRODUCT_NOT_EXIST'));
        }

        $res = DB::table('product')->where('id',$id)->delete();
        if(!$res){
            return response()->json(ERR::GetError('ERR_PRODUCT_DEL'));
        }
        return response()->json(ERR::GetError('OK'));
    }


    /**验证产品参数
     * @param $data
     * @return \Illuminate\Http\JsonResponse
     */
    private static function validProductParam($data)
    {
        $name   = isset($data['name'])      ?   $data['name']       : '';
        $supply = isset($data['supply'])    ?   $data['supply']     : '';
        $cate   = isset($data['cate'])      ?   $data['cate']       : '';
        $price  = isset($data['price'])     ?   $data['price']      : '';
        $makeup = isset($data['makeup'])    ?   $data['makeup']     : '';
        $function = isset($data['function'])?   $data['function']   : '';
        $usage  = isset($data['usage'])     ?   $data['usage']      : '';
        $spec   = isset($data['spec'])      ?   $data['spec']       : '';

        if(!$name || !$supply || !$cate || !$price || !$makeup || !$function || !$usage || !$spec){
            return '1';
        }
        return '0';
    }
}