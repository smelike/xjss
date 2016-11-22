<?php

namespace App\Http\Controllers\Info;
use App\Http\Controllers\Controller;
use App\Http\ERR;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class InfoController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest');
    }


    /**创建新的信息
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAddInfo()
    {
        $permission = session('user_permission');
        if($permission > 20){
            abort('403');
        }

        return view('info.addInfo');
    }

    /**创建消息通知
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addInfo(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }
        $user_tel = session('user_tel');

        $data = array_map('htmlentities',$request->all());
        $title = isset($data['title'])  ? $data['title']    : '';
        $info  = isset($data['info'])   ? $data['info']     : '';
        $action = isset($data['action'])? $data['action']   : '';

        if($title == "" || $info == "" || $action == ""){
            return response()->json(ERR::GetError('ERR_INFO_PARAM'));
        }
        if(!in_array($action,['save','publish'])){
            return response()->json(ERR::GetError('ERR_INFO_PARAM'));
        }

        $insert_data = [
            'title'     => $title,
            'creator'   => $user_tel,
            'info'      => $info,
            'status'    => 0,
            'created_at'=> date('Y-m-d')
        ];

        if($action == 'publish'){
            $insert_data['status'] = 1;
        }

        $res = DB::table('info')->insert($insert_data);
        if(!$res){
            return response()->json(ERR::GetError('ERR_INFO_CREATE'));
        }

        return response()->json(ERR::GetError('OK'));
    }

    /**管理员权限：信息列表
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getAllInfo()
    {
        $permission = session('user_permission');
        if($permission > 20){
            $info = $this->getCommonInfoList();
            return view('info.commonInfoList',['infos'=>$info]);
        }

        $info = DB::table('info')
                ->leftJoin('user','info.creator','=','user.telephone')
                ->orderBy('info.created_at','desc')
                ->select('info.*','user.name')
                ->simplePaginate(20);
        $count = $info->count();
        return view('info.manageInfo',['infos'=>$info,'count'=>$count]);
    }


    /**普通用户权限：获取已发布的消息列表
     * @return mixed
     */
    private function getCommonInfoList()
    {
        $info = DB::table('info')
                ->where('info.status',1)
                ->orderBy('info.created_at','desc')
                ->simplePaginate(10);
        return $info;
    }


    /**管理员权限：信息详情
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function manageInfoDesc($id)
    {
        $permission = session('user_permission');

        $info = DB::table('info')->where('info.id',$id)->first();
        if(!$info){
            abort('404');
        }

        $info->info = htmlspecialchars_decode($info->info);

        return view('info.manageInfoDesc',['info'=>$info,'privilege'=>$permission]);
    }


    /**更新与发布消息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function manageInfo(Request $request)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }

        $data = array_map('htmlspecialchars',$request->all());

        $id     = isset($data['id'])     ? $data['id']      : '';
        $title  = isset($data['title'])  ? $data['title']   : '';
        $info   = isset($data['info'])   ? $data['info']    : '';
        $action = isset($data['action']) ? $data['action']  : '';

        if(!$id || !$title || !$info || !$action ){
            return response()->json(ERR::GetError('ERR_INFO_PARAM'));
        }

        if(!in_array($action,['update','publish'])){
            return response()->json(ERR::GetError('ERR_INFO_PARAM'));
        }

        $exist = DB::table('info')->where('id',$id)->count();
        if(!$exist){
            return response()->json(ERR::GetError('ERR_INFO_PARAM'));
        }

        $info_data = [
            'title'     => $title,
            'info'      => $info,
            'updated_at'=> date('Y-m-d H:i:s')
        ];

        if($action == 'publish'){
            $info_data['status'] = 1;
        }

        $upd = DB::table('info')->where('id',$id)->update($info_data);
        if(!$upd){
            return response()->json(ERR::GetError('ERR_INFO_UPDATE'));
        }

        return response()->json(ERR::GetError('OK'));
    }


    /**取消消息发布
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancelInfoPublish($id)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }
        $info = DB::table('info')->where('id',$id)->count();
        if(!$info){
            return response()->json(ERR::GetError('ERR_INFO_PARAM'));
        }

        $data = ['status'=>0,'updated_at'=>date('Y-m-d H:i:s')];
        $res = DB::table('info')->where('id',$id)->update($data);
        if(!$res){
            return response()->json(ERR::GetError('ERR_INFO_CANCEL'));
        }
        return response()->json(ERR::GetError('OK'));
    }


    /**删除 信息
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delInfo($id)
    {
        $permission = session('user_permission');
        if($permission > 20){
            return response()->json(ERR::GetError('ERR_USER_PERMISSION'));
        }
        $info = DB::table('info')->where('id',$id)->count();
        if(!$info){
            return response()->json(ERR::GetError('ERR_INFO_PARAM'));
        }

        $res = DB::table('info')->where('id',$id)->delete();
        if(!$res){
            return response()->json(ERR::GetError('ERR_INFO_DELETE'));
        }
        return response()->json(ERR::GetError('OK'));
    }

}
