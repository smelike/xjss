<?php
/**
 * Created by PhpStorm.
 * User: HSF
 * Date: 2016/5/12
 * Time: 14:59
 */

namespace App\Http\Controllers\File;
use App\Http\Controllers\Controller;
use App\Http\ERR;
use Illuminate\Support\Facades\Input;

class FileUploadController extends Controller{

    public function __construct()
    {
        $this->middleware('guest');
    }


    /**文件上传
     * @param $path
     * @return array|string
     */
    public static function upload($path){

        //var_dump(request()->file());exit;

        //'avatar'是input标签name属性值，必须要对应
        if(request()->hasFile('avatar')){
            if(request()->file('avatar')->isValid()){

                $fileSize = request()->file('avatar')->getClientSize();

                $maxsize = request()->file('avatar')->getMaxFilesize();

                if($fileSize > $maxsize){
                    return ERR::GetError('ERR_FILE_LENGTH');
                }

                //原始文件名
                //$filename=request()->file('avatar')->getClientOriginalName();

                //获取文件扩展名 jpg png ...
                $type=request()->file('avatar')->getClientOriginalExtension();

                //获取 mime类型  image/png ...
                $mime=request()->file('avatar')->getClientMimeType();

                $allow = array('image/png','image/jpg','image/jpeg','image/gif','image/bmp','image/pjpeg');

                if(!in_array($mime,$allow)){
                    return ERR::GetError('ERR_FILE_MIME');
                }

                //返回项目所在路径
                $rootPath=base_path();

                $path = $rootPath.$path;

                $newName = substr(uniqid(),4).'.'.$type;
                $file=request()->file('avatar')->move($path,$newName);
                if($file){
                    //保存到数据库的图片名
                    return $newName;
                }else{
                    return false;
                }
            }else{
                //return request()->file('avatar')->getErrorMessage();
                return ERR::GetError('ERR_FILE_UPLOAD');
            }
        }else{
            return ERR::GetError('ERR_FILE_NOT_EXITS');
        }
    }
}