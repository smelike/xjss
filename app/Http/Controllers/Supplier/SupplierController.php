<?php
/**
 * Created by PhpStorm.
 * User: HSF
 * Date: 2016/10/24
 * Time: 11:31
 */
namespace App\Http\Controllers\Supplier;
use App\Http\Controllers\Controller;
use App\Http\ERR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierController extends Controller{

    public function __construct()
    {
        $this->middleware('guest');
    }


    /**获取供应商列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getSupplier()
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }

        $supplier = DB::table('supplier')->orderBy('created_at','desc')->simplePaginate(20);
        $count = $supplier->count();
        return view('supplier.supplier',['supplier'=>$supplier,'count'=>$count]);
    }


    /**添加供应商
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addSupplier(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = array_map('e',$request->all());
        $name = isset($data['name'])    ? $data['name'] : "";
        $addr = isset($data['addr'])    ? $data['addr'] : "";

        if($name == "" || $addr == ""){
            return response()->json(ERR::GetError('ERR_SUPPLIER_PARAM'));
        }

        $exist = DB::table('supplier')->where('supplier',$name)->count();
        if($exist){
            return response()->json(ERR::GetError('ERR_SUPPLIER_EXIST'));
        }
        $inst = [
            'supplier'  => $name,
            'address'   => $addr,
            'created_at'=> date('Y-m-d')
        ];
        $res = DB::table('supplier')->insert($inst);
        if(!$res){
            return response()->json(ERR::GetError('ERR_SUPPLIER_INSERT'));
        }

        return response()->json(ERR::GetError('OK'));
    }


    /**供应商编辑
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getEditSupplier($id)
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }

        $supplier = DB::table('supplier')->where('id',$id)->first();
        if(!$supplier){
            abort('404');
        }

        return view('supplier.editSupplier',['supplier'=>$supplier]);
    }


    /**供应商编辑
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editSupplier(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = array_map('e',$request->all());
        $id   = isset($data['id'])      ? $data['id']   : "";
        $name = isset($data['name'])    ? $data['name'] : "";
        $addr = isset($data['addr'])    ? $data['addr'] : "";

        if($name == "" || $addr == ""){
            return response()->json(ERR::GetError('ERR_SUPPLIER_PARAM'));
        }

        $exist = DB::table('supplier')->where('id',$id)->count();
        if(!$exist){
            return response()->json(ERR::GetError('ERR_SUPPLIER_EXCEPTION'));
        }

        $exist = DB::table('supplier')->where('id','<>',$id)->where('supplier',$name)->count();
        if($exist){
            return response()->json(ERR::GetError('ERR_SUPPLIER_EXIST'));
        }

        $edit = [
            'supplier'  => $name,
            'address'   => $addr,
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $res = DB::table('supplier')->where('id',$id)->update($edit);
        if(!$res){
            return response()->json(ERR::GetError('ERR_SUPPLIER_UPDATE'));
        }
        return response()->json(ERR::GetError('OK'));
    }


    /**删除供应商
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delSupplier($id)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $exist = DB::table('supplier')->where('id',$id)->count();
        if(!$exist){
            return response()->json(ERR::GetError('ERR_SUPPLIER_EXCEPTION'));
        }

        $del = DB::table('supplier')->where('id',$id)->delete($id);
        if(!$del){
            return response()->json(ERR::GetError('ERR_SUPPLIER_DEL'));
        }
        return response()->json(ERR::GetError('OK'));
    }

}