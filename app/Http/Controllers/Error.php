<?php

/**
 * Created by John.
 * User: John
 * Date: 2016/3/1
 * Time: 15:35
 */
namespace App\Http;

class ERR {

	public static $Max_Message_ID = 65535;
	public static $errCode = array(

		//**START**
		//success
		['OK', '0000', 'SUCCESS', '操作成功'],

		// Application
		['ERROR_APP', '1000', 'Application error.', '应用错误'],

		// User module
		['ERR_USER_PHONE   					', '2000', 'User phone error.		', '请输入正确的手机号'],
		['ERR_USER_EXIST	           		', '2001', 'User phone exist.		', '此号码已注册，可直接登陆'],
		['ERR_USER_NOT_EXIST          		', '2002', 'User phone not exist.	', '此号码未注册，请前往注册'],
		['ERR_CAPTCHA_SEND           		', '2003', 'Captcha send error.		', '验证码发送失败，请稍后重试'],
		['ERR_CAPTCHA           			', '2004', 'Captcha error.			', '验证码错误或已失效！'],
		['ERR_USER_REGISTER             	', '2005', 'User register error.	', '注册失败，请重新注册'],

		['ERR_USER_CHECK           			', '2006', 'User waiting check.		', '等待管理员审核！'],
		['ERR_USER_DEL           			', '2007', 'User has been deleted.	', '已被删除，请联系管理员！'],
		['ERR_USER_INFO           			', '2007', 'User info error.		', '个人信息不存在'],

		['ERR_USER_NOT_EXIST       			', '2007', 'User phone not exist.	', '用户不存在，请前往注册'],

		['ERR_USER_NAME 					', '2009', 'User name error.		', '用户名不能为空！'],
		['ERR_USER_ROLE 					', '2009', 'User type error.		', '注册类型错误或为空！'],
		['ERR_USER_PWD 						', '2009', 'User pwd error.			', '密码错误，请重新登陆！'],

		['ERR_USER_PERMISSION 				', '2009', 'User have no permission.', '权限不足！'],
		['ERR_USER_INVALID 					', '2009', 'User not exist.			', '用户信息错误，刷新重试！'],

		['ERR_USER_DEL 						', '2009', 'Delete user error.		', '用户删除失败！'],
		['ERR_USER_CHECK 					', '2009', 'User check error.		', '用户审核失败！'],


		['ERR_USER_EXCEPTION 				', '2009', 'User info error.		', '用户信息错误，刷新重试！'],

		//logout
		['ERR_USER_LOGOUT          			', '2011', 'User logout error.		', '登出失败，请稍后重试'],

		//Product
		['ERR_PRODUCT_EXIST          		', '2011', 'Product exist.			', '产品名称已存在，刷新重试！'],
		['ERR_PRODUCT_NOT_EXIST          	', '2011', 'Product not exist.		', '产品不存在，刷新重试！'],
		['ERR_PRODUCT_DEL          			', '2011', 'Product del error.		', '产品删除失败，刷新重试！'],
		['ERR_PRODUCT_PARAM          		', '2011', 'Product param error.	', '产品参数错误，请重试！'],
		['ERR_PRODUCT_ADD          			', '2011', 'Product add error.		', '产品添加失败，请重试！'],

		//Category
		['ERR_CATE_NOT_EXIST          		', '2011', 'Category not exist.		', '产品分类不存在，刷新重试！'],
		['ERR_CATE_EXIST          			', '2011', 'Category has been exist.', '产品分类已存在，刷新重试！'],
		['ERR_CATE_ADD          			', '2011', 'Category add error.		', '产品分类添加失败，刷新重试！'],
		['ERR_CATE_EMPTY          			', '2011', 'Category name empty.	', '分类名称不能为空！'],
		['ERR_CATE_EDIT          			', '2011', 'Category edit error.	', '分类编辑失败，刷新重试！'],
		['ERR_CATE_DEL          			', '2011', 'Category del error.		', '分类删除失败，刷新重试！'],
		['ERR_CATE_PRODUCT          		', '2011', 'Category has product.	', '此分类拥有产品，不能直接删除！'],

		//Manage
		['ERR_USER_SET_ADMIN          		', '2011', 'User set admin error.	', '管理员设置失败，刷新重试！'],

		//Order
		['ERR_CHECK_STATUS          		', '2011', 'Check order status error', '审核订单状态错误，刷新重试！'],
		['ERR_ORDER_EXCEPTION          		', '2011', 'Order exception.		', '订单信息异常，刷新重试！'],
		['ERR_ORDER_CHECK          			', '2011', 'Order check error.		', '订单审核失败，刷新重试！'],
		['ERR_ORDER_STATUS          		', '2011', 'Update order state error.', '更新订单状态错误，刷新重试！'],
		['ERR_ORDER_PRODUCT_QTY          	', '2011', 'Order product qty error.', '订单产品数量错误，刷新重试！'],
		['ERR_ORDER_PARAM		          	', '2011', 'Order param error.		', '订单参数错误，请重新下单！'],

		//Cart
		['ERR_CART_ADD		          		', '2011', 'Cart add error.			', '购物车添加失败，刷新重试！'],
		['ERR_CART_NOT_EXIST		        ', '2011', 'Cart product error.		', '购物车产品不存在，刷新重试！'],
		['ERR_CART_DEL		        		', '2011', 'Cart delete error.		', '购物车产品删除失败，刷新重试！'],

		//Info
		['ERR_INFO_PARAM		          	', '2011', 'Info param error.		', '消息通知参数错误，刷新重试！'],
		['ERR_INFO_UPDATE		        	', '2011', 'Info update error.		', '消息更新失败，刷新重试！'],
		['ERR_INFO_CANCEL		        	', '2011', 'Info cancel error.		', '取消消息通知失败，刷新重试！'],
		['ERR_INFO_DELETE		        	', '2011', 'Info delete error.		', '删除消息通知失败，刷新重试！'],


		//Supplier
		['ERR_SUPPLIER_PARAM'				, '6003', 'Supplier param error.	', '供应商参数错误，刷新重试！'],
		['ERR_SUPPLIER_EXIST'				, '6003', 'Supplier exist.			', '供应商已存在，刷新重试！'],
		['ERR_SUPPLIER_INSERT'				, '6003', 'Supplier insert error.	', '供应商添加失败，刷新重试！'],
		['ERR_SUPPLIER_UPDATE'				, '6003', 'Supplier update error.	', '供应商更新失败，刷新重试！'],
		['ERR_SUPPLIER_EXCEPTION'			, '6003', 'Supplier info exception.	', '供应商信息异常，刷新重试！'],


		//SYSTEM
		['ERR_SYSTEM', '8888', 'system error.' , '系统错误'],
		//--END--
		['ERR_UNKNOWN', '9999', 'unknown error.', '未知的错误'],

	);
	// 构造函数

	public function __construct() {
		//parent::__construct();
	}
	// 错误信息语言
	//const ERR_MSG_LANGUAGE = 'ENG';

	const ERR_MSG_LANGUAGE = 'CHN';
	// 错误码列表

	/**
	 * 根据错误串，返回错误代码和错误信息
	 *
	 * @param $var [in] 错误串
	 * @param string $lang [in] 错误信息的语言('ENG', 'CHN')
	 * @return array 将错误代码和错误信息以数组的方式返回
	 */
	public static function GetError($var, $lang = self::ERR_MSG_LANGUAGE) {
		//$value = array_get(self::$errCode, $var);
		//$rvalue = array_shift($value);

		$count = count(self::$errCode);
		for ($x = 0; $x < $count; $x++) {
			if (trim(self::$errCode[$x][0]) == trim($var)) {
				break;
			}

		}

		if ($x >= $count) {
			$x = $count - 1;
		}

		if ($lang == 'CHN') {
			$rvalue = array('err_code' => self::$errCode[$x][1], 'err_msg' => self::$errCode[$x][3]);
		} else {
			$rvalue = array('err_code' => self::$errCode[$x][1], 'err_msg' => self::$errCode[$x][2]);
		}

		return $rvalue;
	}
}
