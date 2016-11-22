<?php
/**
 * Created by PhpStorm.
 * User: HSF
 * Date: 2016/8/26
 * Time: 10:48
 */
namespace App\Http\Controllers\User;
use App\Http\Controllers\Controller;
use App\Http\Controllers\File\FileUploadController;
use App\Http\Controllers\Wx\JsApiPay;
use App\Http\ERR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest');
    }


    /**获取短信验证码
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCaptcha(Request $request)
    {
        $data = $request->all();
        $tel = isset($data['tel']) ? $data['tel'] : '';

        if(!preg_match('/^1\d{10}$/',$tel)){
            return response()->json(ERR::GetError('ERR_USER_PHONE'));
        }

        //判断此号码是否已注册
        $user = DB::table('user')->where('telephone',$tel)->first();
        if($user){    //注册时
            return response()->json(ERR::GetError('ERR_USER_EXIST'));
        }

        //生成验证码
        $captcha = mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9).mt_rand(0,9);

        //缓存 验证码 与 生成时间
        session(['captcha'=>$captcha]);
        session([$tel.'_'.$captcha=>time()]);

        //请求短信接口
        $uid = 'xiaoquan';
        $pwd = 'lf123456';
        $msg = "【晓泉信息】您的验证码是：{$captcha}（5分钟内输入有效，别告诉别人哦。）";
        $msg = iconv('UTF-8','GBK',$msg);
        $url = 'http://www.smsadmin.cn/smsmarketing/wwwroot/api/get_send/?uid='.$uid.'&pwd='.$pwd.'&mobile='.$tel.'&msg='.$msg.'&dtime=';

        $info = file_get_contents($url);
        $state = substr($info,0,1);

        if($state != '0'){
            return response()->json(ERR::GetError('ERR_CAPTCHA_SEND'));
        }

        return response()->json(ERR::GetError('OK'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Register(Request $request)
    {
        $data = array_map('trim',$request->all());

        $name = isset($data['name'])    ? trim($data['name']) : '';
        $tel  = isset($data['tel'])     ? $data['tel']  : '';
        $pwd  = isset($data['pwd'])     ? htmlentities($data['pwd'])  : '';
        $role = isset($data['role'])    ? $data['role'] : '';
        $captcha = isset($data['captcha']) ? $data['captcha'] : '';


        if($name == ''){
            return response()->json(ERR::GetError('ERR_USER_NAME'));
        }

        if(!preg_match('/^1\d{10}$/',$tel)){
            return response()->json(ERR::GetError('ERR_USER_PHONE'));
        }

        if($role == '' || !is_numeric($role) || $role > 50){
            return response()->json(ERR::GetError('ERR_USER_ROLE'));
        }

        //校检验证码 5分钟有效
        if($captcha != session('captcha') || time()-session($tel.'_'.$captcha) >300){
            return response()->json(ERR::GetError('ERR_CAPTCHA'));
        }

        if($role == 20){    //开票员权限
            $permossion = 30;
        }
        if(in_array($role,[30,40,50])){     //业务员、经销商、客户权限
            $permossion = 40;
        }
        $info =[
            'name'      => $name,
            'telephone' => $tel,
            'password'  => md5($pwd),
            'role'      => $role,
            'status'    => 0,        //待审核
            'is_admin'  => 0,
            'permission'=> $permossion,
            'created_at'=> date('Y-m-d H:i:s')
        ];

        $user = DB::table('user')->insert($info);
        if(!$user){
            return response()->json(ERR::GetError('ERR_USER_REGISTER'));
        }

        return response()->json(ERR::GetError('OK'));
    }


    /**登陆：先采用短信验证码方式
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Login(Request $request)
    {
        $data = array_map('trim',$request->all());

        $tel = isset($data['tel']) ? $data['tel'] : '';
        $pwd = isset($data['pwd']) ? $data['pwd'] : '';

        if(!preg_match('/^1\d{10}$/',$tel)){
            return response()->json(ERR::GetError('ERR_USER_PHONE'));
        }

        $user = DB::table('user')->where('telephone',$tel)->first();
        if(!$user){
            return response()->json(ERR::GetError('ERR_USER_NOT_EXIST'));
        }

        if(md5($pwd) != $user->password){
            return response()->json(ERR::GetError('ERR_USER_PWD'));
        }

        if($user->status == 0){
            return response()->json(ERR::GetError('ERR_USER_CHECK'));
        }
        if($user->status == 2){
            return response()->json(ERR::GetError('ERR_USER_DEL'));
        }

        session(['user_permission'=>$user->permission]);
        session(['user_role'=>$user->role]);
        session(['user_tel'=>$tel]);

        return response()->json(ERR::GetError('OK'));
    }


    /**登陆成功，跳转主页
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function Home()
    {
        $permission = session('user_permission');

        return view('main',['permission'=>$permission]);
    }


    /**
     * 获取个人信息
     */
    public function getUserInfo()
    {
        $tel  = session('user_tel');
        $user = DB::table('user')->where('telephone',$tel)->first();
        return view('user.info',['user'=>$user]);
    }


    /**个人信息编辑
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function infoEdit()
    {
        $tel = session('user_tel');
        $user = DB::table('user')->where('telephone',$tel)->first();

        return view('user.editInfo',['user'=>$user]);
    }


    /**更新 user info
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function userInfoEdit(Request $request)
    {
        $data = array_map('trim',$request->all());
        $name = isset($data['name'])    ? $data['name'] : '';
        $tel  = isset($data['tel'])     ? $data['tel']  : '';

        if(!preg_match('/^1\d{10}$/',$tel)){
            return response()->json(ERR::GetError('ERR_USER_PHONE'));
        }

        if($name == ''){
            return response()->json(ERR::GetError('ERR_USER_NAME'));
        }

        $user_tel = session('user_tel');
        $data = [
            'name'      => $name,
            'telephone' => $tel,
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        $user = DB::table('user')->where('telephone',$user_tel)->update($data);
        if(!$user){
            return response()->json(ERR::GetError('ERR_USER_EDIT'));
        }
        session()->flush();
        return response()->json(ERR::GetError('OK'));
    }


    /**获取所有有效用户
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getValidUser()
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }

        $user = DB::table('user')
                ->where('status',1)
                ->orderBy('role')
                ->paginate(15);

        return view('user.validUser',['user'=>$user]);
    }

    /**获取待审核员工
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getApplyUser()
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }

        $user = DB::table('user')
                ->where('status',0)
                ->orderBy('created_at','desc')
                ->paginate(15);

        return view('user.applyUser',['user'=>$user]);
    }


    /**获取已删除状态员工
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function getDelUser()
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }

        $user = DB::table('user')
                ->where('status',2)
                ->orderBy('updated_at','desc')
                ->paginate(15);

        return view('user.delUser',['user'=>$user]);
    }


    /**伪删除正式员工
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delUser(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $tel = isset($data['user']) ? htmlentities($data['user']) : '';

        $info = DB::table('user')->where('telephone',$tel)->where('status',1)->first();
        if(!$info){
            return response()->json(ERR::GetError('ERR_USER_INVALID'));
        }

        //不能删除经理
        if($info->role == 10){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $res = DB::table('user')->where('telephone',$tel)->update(['status'=>2,'updated_at'=>date('Y-m-d H:i:s')]);
        if(!$res){
            return response()->json(ERR::GetError('ERR_USER_DEL'));
        }
        return response()->json(ERR::GetError('OK'));
    }

    /**审核申请用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkUser(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $tel = isset($data['user']) ? htmlentities($data['user']) : '';

        $user = DB::table('user')->where('telephone',$tel)->where('status',0)->first();
        if(!$user){
            return response()->json(ERR::GetError('ERR_USER_INVALID'));
        }

        $res = DB::table('user')->where('telephone',$tel)->update(['status'=>1,'updated_at'=>date('Y-m-d H:i:s')]);
        if(!$res){
            return response()->json(ERR::GetError('ERR_USER_CHECK'));
        }
        return response()->json(ERR::GetError('OK'));
    }


    /**直接删除 申请用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delApplyUser(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $tel = isset($data['user']) ? htmlentities($data['user']) : '';

        $user = DB::table('user')->where('telephone',$tel)->where('status',0)->first();
        if(!$user){
            return response()->json(ERR::GetError('ERR_USER_INVALID'));
        }

        $res = DB::table('user')->where('telephone',$tel)->delete();
        if(!$res){
            return response()->json(ERR::GetError('ERR_USER_DEL'));
        }
        return response()->json(ERR::GetError('OK'));
    }


    /**恢复伪删除员工
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function recoverUser(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $tel = isset($data['user']) ? htmlentities($data['user']) : '';

        $user = DB::table('user')->where('telephone',$tel)->where('status',2)->first();
        if(!$user){
            return response()->json(ERR::GetError('ERR_USER_INVALID'));
        }

        $res = DB::table('user')->where('telephone',$tel)->update(['status'=>1,'updated_at'=>date('Y-m-d H:i:s')]);
        if(!$res){
            return response()->json(ERR::GetError('ERR_USER_DEL'));
        }
        return response()->json(ERR::GetError('OK'));
    }

    /**彻底删除 伪删除的用户
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = $request->all();
        $tel = isset($data['user']) ? htmlentities($data['user']) : '';

        $user = DB::table('user')->where('telephone',$tel)->where('status',2)->first();
        if(!$user){
            return response()->json(ERR::GetError('ERR_USER_INVALID'));
        }

        $res = DB::table('user')->where('telephone',$tel)->delete();
        if(!$res){
            return response()->json(ERR::GetError('ERR_USER_DEL'));
        }
        return response()->json(ERR::GetError('OK'));
    }
}