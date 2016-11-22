<?php
/**
 * Created by PhpStorm.
 * User: HSF
 * Date: 2016/7/22
 * Time: 15:11
 */
namespace App\Http\Controllers\Wx;
use App\Http\Controllers\Controller;

class QRcode extends Controller{

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * 生成带参数的微信二维码
     * @param string $str
     */
    public function getQrcode($str){

        if(!session('access_token')){
            //按摩床服务号信息
            $appid='wx2f0b7bcc8fb6758e';
            $secret='f57ed2534fac5b764fa161063bd46546';
            $url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.$appid.'&secret='.$secret;
            $res=json_decode(file_get_contents($url));

            $access_token=$res->access_token;
            session(['access_token'=>$access_token]);
        }else{
            $access_token = session('access_token');
        }
        $url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$access_token;

        $post_data='{"action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "'.$str.'"}}}';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        //禁止服务器端校检SSL
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $output = curl_exec($ch);
        curl_close($ch);

        //创建二维码ticket
        $ticket=json_decode($output)->ticket;
        $urlget='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($ticket);

        $qrcode=file_get_contents($urlget);

        header('content-type:image/jpg');
        echo $qrcode;
    }
}