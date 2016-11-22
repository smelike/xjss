<?php
namespace App\Http\Controllers\Wx;
use App\Http\Wx\lib\WxPayApi;
use App\Http\Wx\lib\WxPayNotify;
use App\Http\Wx\lib\WxPayOrderQuery;
use Illuminate\Support\Facades\DB;


//ini_set('date.timezone','Asia/Shanghai');
config(['app.timezone' => 'Asia/Shanghai']);
error_reporting(E_ERROR);

//初始化日志
$logHandler= new CLogFileHandler(storage_path()."/logs/".'wxpay_'.date('Y-m-d').'.log');
$log = Log::Init($logHandler, 15);

class PayNotifyCallBack extends WxPayNotify
{
	public function __construct()
	{
		$xml=file_get_contents('php://input');

		//todo::将xml转换成array
		$data=(array)simplexml_load_string($xml,'SimpleXMLElement',LIBXML_NOCDATA);
		$this->NotifyProcess($data,$msg='');
	}

	//查询订单
	public function Queryorder($transaction_id)
	{
		$input = new WxPayOrderQuery();
		$input->SetTransaction_id($transaction_id);
		$result = WxPayApi::orderQuery($input);
		Log::DEBUG("query:" . json_encode($result));
		if(array_key_exists("return_code", $result)
			&& array_key_exists("result_code", $result)
			&& $result["return_code"] == "SUCCESS"
			&& $result["result_code"] == "SUCCESS")
		{
			return true;
		}
		return false;
	}
	
	//重写回调处理函数
	public function NotifyProcess($data, &$msg)
	{
		Log::DEBUG("call back:" . json_encode($data));
		$notfiyOutput = array();
		
		if(!array_key_exists("transaction_id", $data)){
			$msg = "输入参数不正确";
			return false;
		}
		//查询订单，判断订单真实性
		if(!$this->Queryorder($data["transaction_id"])){
			$msg = "订单查询失败";
			return false;
		}

		$status=DB::table('order')->where('out_grade_no',$data['out_grade_no'])->value('order_status');

		if(!$status){
			return false;
		}elseif($status = 1){	//只有是待付款状态才被回调修改
			$update=DB::table('order')->where('out_grade_no',$data['out_grade_no'])->update(['order_status'=>3]);
			if(!$update){
				return false;
			}
			return true;
		}else{
			return true;
		}
	}
}

Log::DEBUG("begin notify");
$notify = new PayNotifyCallBack();
$notify->Handle(false);
