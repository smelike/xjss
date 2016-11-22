<?php
/**
 * Created by PhpStorm.
 * User: HSF
 * Date: 2016/10/13
 * Time: 10:50
 */
namespace App\Http\Controllers\Product;
use App\Http\Controllers\Controller;
use App\Http\ERR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller{

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**管理员权限：分类查看
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function showCategory()
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }

        $cate = DB::table('category')->orderBy('created_at','desc')->simplePaginate(20);
        $count = DB::table('category')->count();
        return view('category.category',['cate'=>$cate,'count'=>$count]);
    }


    /**增加分类
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAddPage()
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }
        return view('category.add');
    }

    /**增加分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addCategory(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }
        $data = array_map('trim',$request->all());
        $cate = isset($data['name']) ? htmlentities($data['name']) : '';
        if($cate == ''){
            return response()->json(ERR::GetError('ERR_CATE_EMPTY'));
        }

        $exist = DB::table('category')->where('category',$cate)->count();
        if($exist){
            return response()->json(ERR::GetError('ERR_CATE_EXIST'));
        }
        $category = [
            'category'  => $cate,
            'created_at'=> date('Y-m-d')
        ];
        $res = DB::table('category')->insert($category);
        if(!$res){
            return response()->json(ERR::GetError('ERR_CATE_ADD'));
        }
        return response()->json(ERR::GetError('OK'));
    }
    /**显示编辑分类
     * @param $cate_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function showEditPage($cate_id)
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }

        $cate = DB::table('category')->where('id',$cate_id)->first();
        if(!$cate){
            abort('404');
        }

        return view('category.edit',['cate'=>$cate]);
    }

    /**编辑分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function editCategory(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = array_map('trim',$request->all());

        $cate_id = isset($data['id'])   ? $data['id'] : 0;
        $cate_name = isset($data['cate']) ? $data['cate'] : '';

        if($cate_name == ''){
            return response()->json(ERR::GetError('ERR_CATE_EMPTY'));
        }

        $exist = DB::table('category')->where('id',$cate_id)->count();
        if(!$exist){
            return response()->json(ERR::GetError('ERR_CATE_NOT_EXIST'));
        }

        $count = DB::table('category')->where('category',$cate_name)->count();
        if($count > 1){
            return response()->json(ERR::GetError('ERR_CATE_EXIST'));
        }

        $category = [
            'category'  => $cate_name,
            'updated_at'=> date('Y-m-d H:i:s')
        ];
        $res = DB::table('category')->where('id',$cate_id)->update($category);
        if(!$res){
            return response()->json(ERR::GetError('ERR_CATE_EDIT'));
        }
        return response()->json(ERR::GetError('OK'));
    }

    /**删除产品分类
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delCategory(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $cate_id = isset($data['id']) ? $data['id'] : 0;

        $exist = DB::table('category')->where('id',$cate_id)->count();
        if(!$exist){
            return response()->json(ERR::GetError('ERR_CATE_NOT_EXIST'));
        }

        //查找此分类下面是否有产品
        $product = DB::table('product')->where('category',$cate_id)->count();
        if($product > 0){
            return response()->json(ERR::GetError('ERR_CATE_PRODUCT'));
        }

        $del = DB::table('category')->where('id',$cate_id)->delete();
        if(!$del){
            return response()->json(ERR::GetError('ERR_CATE_DEL'));
        }

        return response()->json(ERR::GetError('OK'));
    }
}