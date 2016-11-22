<?php
/**
 * Created by PhpStorm.
 * User: HSF
 * Date: 2016/7/11
 * Time: 17:22
 */
namespace App\Http\Controllers\Cart;
use App\Http\Controllers\Controller;
use App\Http\ERR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CartController extends Controller{

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * 查看购物车
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCart(){

        $permission = session('user_permission');
        if($permission < 30){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $user = session('user_tel');
        $cart = DB::table('cart as c')
                ->leftJoin('product as p','c.product_id','=','p.id')
                ->where('c.user',$user)
                ->select('c.*','p.price')
                ->get();

        return view('cart.cart',['cart'=>$cart]);
    }


    /**
     * 添加商品到购物车
     * @param Request $request [pid,qty,attr_id]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCart(Request $request)
    {
        $permission = session('user_permission');
        if($permission < 30){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $id  = isset($data['id'])    ? $data['id']   : '';
        $qty = isset($data['qty'])   ? $data['qty']  : '';

        if($id == "" || $qty == "" || !is_numeric($qty) || $qty < 0){
            return response()->json(ERR::GetError('ERR_PRODUCT_PARAM'));
        }

        $exist = DB::table('product')->where('id',$id)->first();
        if(!$exist){
            return response()->json(ERR::GetError('ERR_PRODUCT_NOT_EXIST'));
        }

        $user = session('user_tel');
        $cart = DB::table('cart')->where('product_id',$id)->where('user',$user)->count();
        if($cart){
            $cart_data = [
                'quantity'  => $qty,
                'updated_at'=> date('Y-m-d H:i:s')
            ];
            $ret = DB::table('cart')->where('product_id',$id)->where('user',$user)->update($cart_data);
        }else{
            $cart_data = [
                'product_id'    => $id,
                'quantity'      => $qty,
                'user'          => session('user_tel'),
                'created_at'    => date('Y-m-d')
            ];
            $ret = DB::table('cart')->insert($cart_data);
        }
        if(!$ret){
            return response()->json(ERR::GetError('ERR_CART_ADD'));
        }
        return response()->json(ERR::GetError('OK'));
    }


    /**更新购物车商品数量
     * @param Request $request [id,qty]
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateCart(Request $request){

        $data=$request->all();

        $cart_id=isset($data['id']) ? $data['id']  : '';
        $qty    =isset($data['qty'])? $data['qty'] : '';

        if($cart_id =='' || $qty=='' || !is_numeric($cart_id) || !is_numeric($qty)){
            return response()->json(ERR::GetError('ERR_CART_UPDATE'));
        }

        return response()->json(ERR::GetError('OK'));

    }


    /**删除购物车商品
     * @param $id  产品ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteCart($id){

        $user = session('user_tel');
        $exist = DB::table('cart')->where('product_id',$id)->where('user',$user)->count();
        if(!$exist){
            return response()->json(ERR::GetError('ERR_CART_NOT_EXIST'));
        }

        $del = DB::table('cart')->where('product_id',$id)->where('user',$user)->delete();
        if(!$del){
            return response()->json(ERR::GetError('ERR_CART_DEL'));
        }
        return response()->json(ERR::GetError('OK'));
    }

    /**当 cid>0 时
     * 合并购物车商品
     * @param $cid   customer_id
     * @param $sign  token
     */
    public function refreshCart($cid,$sign){
        if ($cid) {
            // We want to change the token on all the old items in the customers cart
            CartModel::where('customer_id',$cid)->update(['token'=>$sign]);

            // Once the customer is logged in we want to update the customer ID on all items he has
            //get the customer_id==0 records
            $cart_info = CartModel::where('customer_id', 0)->where('token', $sign)->get()->toArray();

            foreach ($cart_info as $cart) {

                CartModel::destroy($cart['id']);

                // The advantage of using $this->add is that it will check if the products already exist and increaser the quantity if necessary.
                $this->add($cid,$sign,$cart['product_id'],$cart['quantity'],$cart['attribute']);
            }
        }
    }

    /**合并相同购物车商品(未登陆与登陆情况都适用)
     * @param $cid
     * @param $token
     * @param $pid
     * @param int $qty
     * @param array $attr
     */
    public function add($cid, $token, $pid, $qty = 1, $attr = null){

        //读取当前客户之前是否有登陆添加过该属性商品
        if($cid > 0){
            $cart=CartModel::where('customer_id',$cid)->where('product_id',$pid)->where('attribute',$attr)->first();
        }else{
            $cart=CartModel::where('customer_id',0)->where('token',$token)->where('product_id',$pid)->where('attribute',$attr)->first();
        }

        if(!$cart){
            $quantity=0;
        }else{
            $quantity=$cart->toArray()['quantity'];
        }

        if(!$cart){  //未添加过

            $cartModel=new CartModel();
            $cartModel->customer_id =   $cid;
            $cartModel->product_id  =   $pid;
            $cartModel->token       =   $token;
            $cartModel->quantity    =   $qty;
            $cartModel->attribute   =   $attr;

            $cartModel->save();

        }else{  //添加过，则更新

            $cart->quantity = $qty+$quantity;
            $cart->save();
        }
    }

}