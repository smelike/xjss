<?php
/**
 * Created by PhpStorm.
 * User: HSF
 * Date: 2016/10/13
 * Time: 15:04
 */
namespace App\Http\Controllers\Manage;
use App\Http\Controllers\Controller;
use App\Http\ERR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageController extends Controller{

    public function __construct()
    {
        $this->middleware('guest');
    }


    /**获取正式非管理员用户
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showValidAndNotAdmin()
    {
        $permission = session('user_permission');
        if($permission > 10){
            abort('403');
        }
        $user = DB::table('user')
                ->where('status',1)
                ->where('is_admin',0)
                ->orderBy('created_at','desc')
                ->paginate(20);

        return view('manage.notAdminUser',['user'=>$user]);
    }

    /**设置管理员
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function setAdmin(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 10){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $id = isset($data['id']) ? $data['id'] : 0;

        $user = DB::table('user')->where('id',$id)->where('is_admin',0)->where('status',1)->count();
        if(!$user){
            return response()->json(ERR::GetError('ERR_USER_EXCEPTION'));
        }

        $admin = [
            'is_admin'  => 1,
            'permission'=> 20,
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $res = DB::table('user')->where('id',$id)->update($admin);
        if(!$res){
            return response()->json(ERR::GetError('ERR_USER_SET_ADMIN'));
        }
        return response()->json(ERR::GetError('OK'));
    }

    /**展示管理员：除去经理
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function showAdmin()
    {
        $permission = session('user_permission');
        if($permission > 10){
            abort('403');
        }
        $user = DB::table('user')
            ->where('status',1)
            ->where('is_admin',1)
            ->orderBy('created_at','desc')
            ->paginate(20);

        return view('manage.adminUser',['user'=>$user]);
    }

    /**撤销管理员
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function unsetAdmin(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 10){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $id = isset($data['id']) ? $data['id'] : 0;

        $user = DB::table('user')->where('id',$id)->where('is_admin',1)->where('status',1)->first();
        if(!$user){
            return response()->json(ERR::GetError('ERR_USER_EXCEPTION'));
        }

        //不能撤销同为经理的权限
        if($user->role == 10){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        //还原用户权限
        if($user->role = 20){
            $permission = 30;
        }elseif(in_array($user->role,[30,40,50])){
            $permission = 40;
        }
        $admin = [
            'is_admin'  => 0,
            'permission'=> $permission,
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $res = DB::table('user')->where('id',$id)->update($admin);
        if(!$res){
            return response()->json(ERR::GetError('ERR_USER_SET_ADMIN'));
        }
        return response()->json(ERR::GetError('OK'));
    }
}