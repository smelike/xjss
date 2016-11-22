<?php
//require_once "../lib/WxPay.Api.php";
//require_once "WxPay.JsApiPay.php";
//require_once 'log.php';

namespace App\Http\Controllers\Wx;
use App\Http\Wx\lib\WxPayApi;
use App\Http\Wx\lib\WxPayUnifiedOrder;

class Jsapi {

	public function __construct()
	{
		//初始化日志
		$logHandler= new CLogFileHandler(storage_path()."/logs/".'wxpay_'.date('Y-m-d').'.log');
		$log = Log::Init($logHandler, 15);

		//①、获取用户openid
		$tools = new JsApiPay();
		$openId = $tools->GetOpenid();

		//商户订单号 共20位，14位时间戳 + 6位随机数
		$out_trade_no=self::genOutTradeNo();

		//②、统一下单
		$input = new WxPayUnifiedOrder();
		$input->SetBody("test");								//订单描述
		$input->SetAttach("test");								//设置订单附加数据
		$input->SetOut_trade_no($out_trade_no);					//设置商户订单号
		$input->SetTotal_fee("1");								//设置金额 单位：分
		$input->SetTime_start(date("YmdHis"));					//设置开始时间
		$input->SetTime_expire(date("YmdHis", time() + 600));	//设置订单过期日期
		$input->SetGoods_tag("test");							//设置商品标签
		$input->SetNotify_url("http://paysdk.weixin.qq.com/example/notify.php");//设置回调
		$input->SetTrade_type("JSAPI");
		$input->SetOpenid($openId);
		$order = WxPayApi::unifiedOrder($input);

		//打印支付参数
		echo '<font color="#f00"><b>统一下单支付单信息</b></font><br/>';
		$this->printf_info($order);

		$jsApiParameters = $tools->GetJsApiParameters($order);

		//获取共享收货地址js函数参数
		//$editAddress = $tools->GetEditAddressParameters();

		$data=[
			'jsApiParameters'	=>	$jsApiParameters,
			'out_grade_no'		=>  $out_trade_no
		];
		return $data;

		//③、在支持成功回调通知中处理成功之后的事宜，见 notify.php
		/**
		 * 注意：
		 * 1、当你的回调地址不可访问的时候，回调通知会失败，可以通过查询订单来确认支付是否成功
		 * 2、jsapi支付时需要填入用户openid，WxPay.JsApiPay.php中有获取openid流程 （文档可以参考微信公众平台“网页授权接口”，
		 * 参考http://mp.weixin.qq.com/wiki/17/c0f37d5704f0b64713d5d2c37b468d75.html）
		 */
	}

	//打印输出数组信息
	function printf_info($data)
	{
		foreach($data as $key=>$value){
			echo "<font color='#00ff55;'>$key</font> : $value <br/>";
		}
	}

	//生成商户订单号
	private function genOutTradeNo()
	{
		// 共20位，14位时间戳 + 6位随机数
		return date('YmdHis') . str_pad(mt_rand(1, 999999), 6, '0', STR_PAD_LEFT);
	}
}
