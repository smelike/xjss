<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::group(['prefix'=>'xj']   ,   function(){

    Route::get('/', function () {
        return view('welcome');
    });

    //主页
    Route::get('home'           ,   'User\UserController@Home');

    Route::post('user/captcha'  ,   'User\UserController@getCaptcha');

    Route::get('user/register'  ,   function(){return view('user.register');});
    Route::post('user/register' ,   'User\UserController@Register');
    Route::get('user/register/notice'   ,   function(){return view('user.notice');});

    Route::get('user/login'     ,   function(){return view('user.login');});
    Route::post('user/login'    ,   'User\UserController@Login');

    Route::group(['middleware'=>'user.expire']  , function(){

        Route::get('user/info'          ,   'User\UserController@getUserInfo');
        Route::get('user/info/edit'     ,   'User\UserController@infoEdit');
        Route::post('user/info/edit'    ,   'User\UserController@userInfoEdit');

        Route::get('user/valid'         ,   'User\UserController@getValidUser');
        Route::get('user/apply'         ,   'User\UserController@getApplyUser');
        Route::get('user/del'           ,   'User\UserController@getDelUser');

        Route::get('product'            ,   'Product\ProductController@getAllProducts');
        Route::get('product/add'        ,   'Product\ProductController@showAddProduct');
        Route::get('product/edit/{id}'  ,   'Product\ProductController@showEditPage')->where('id','\d+');
        Route::get('product/desc/{id}'  ,   'Product\ProductController@getProductDesc')->where('id','\d+');

        //业务员下单 选择目标客户
        Route::get('product/customer/{type}',   'Product\ProductController@getCustomerByType');

        Route::get('category'           ,   'Product\CategoryController@showCategory');
        Route::get('category/edit/{id}' ,   'Product\CategoryController@showEditPage')->where('id','\d+');
        Route::get('category/add'       ,   'Product\CategoryController@showAddPage');

        Route::get('manage/common/user' ,   'Manage\ManageController@showValidAndNotAdmin');
        Route::get('manage/admin/user' ,   'Manage\ManageController@showAdmin');


        Route::get('cart'               ,   'Cart\CartController@getCart');
        Route::get('cart/del/{id}'      ,   'Cart\CartController@delCart');


        //订单审核
        Route::get('order/check'        ,   'Order\OrderController@getUncheckedOrder');
        Route::get('order/check/{id}'   ,   'Order\OrderController@getUncheckedOrderDesc');
        Route::get('order/list'         ,   'Order\OrderController@getOrderList');
        //管理员、开票员权限的订单更新
        Route::get('order/update/{id}'  ,   'Order\OrderController@updateOrderById');
        //普通用户订单详情
        Route::get('order/list/desc/{id}',   'Order\OrderController@commonOrderListDesc');


        //信息管理
        Route::get('info/list'          ,   'Info\InfoController@getAllInfo');
        Route::get('info/desc/{id}'     ,   'Info\InfoController@manageInfoDesc');
        Route::get('info/add'           ,   'Info\InfoController@getAddInfo');
        Route::get('info/cancel/{id}'   ,   'Info\InfoController@cancelInfoPublish');
        Route::get('info/del/{id}'      ,   'Info\InfoController@delInfo');

        Route::get('supplier'           ,   'Supplier\SupplierController@getSupplier');
        Route::get('supplier/add'       ,   function(){return view('supplier.addSupplier');});
        Route::get('supplier/edit/{id}' ,   'Supplier\SupplierController@getEditSupplier');
        Route::get('supplier/del/{id}'  ,   'Supplier\SupplierController@delSupplier');
    });

    //伪删除正式用户
    Route::post('user/del'          ,   'User\UserController@delUser');

    //审核申请用户
    Route::post('user/apply/check'  ,   'User\UserController@checkUser');

    //删除申请用户
    Route::post('user/apply/del'    ,   'User\UserController@delApplyUser');

    //恢复删除状态用户
    Route::post('user/del/recover'  ,   'User\UserController@recoverUser');
    //彻底删除 删除状态的用户
    Route::post('user/del/delete'   ,   'User\UserController@deleteUser');

    //管理员权限：编辑、添加、搜索、删除产品
    Route::post('product/edit'       ,   'Product\ProductController@editProduct');
    Route::post('product/add'       ,   'Product\ProductController@addProduct');
    Route::post('product/search'    ,   'Product\ProductController@searchProduct');
    Route::post('product/del'       ,   'Product\ProductController@delProduct');

    //管理员权限：添加、编辑、删除产品分类
    Route::post('category/add'       ,   'Product\CategoryController@addCategory');
    Route::post('category/edit'       ,   'Product\CategoryController@editCategory');
    Route::post('category/del'       ,   'Product\CategoryController@delCategory');

    //经理权限：管理员的设置、撤销
    Route::post('manage/set/admin' ,   'Manage\ManageController@setAdmin');
    Route::post('manage/unset/admin' ,   'Manage\ManageController@unsetAdmin');

    //购物车
    Route::post('cart/add'          ,   'Cart\CartController@addCart');
    Route::post('cart/update'       ,   'Cart\CartController@updateCart');

    //管理员、开票员权限：订单管理
    Route::post('order/check'       ,   'Order\OrderController@updateUncheckedOrder');//订单审核
    Route::post('order/update'       ,   'Order\OrderController@postUpdateOrderById');
    Route::post('order/manage/search',   'Order\OrderController@manageSearch');
    //业务员 修改 被打回的订单
    Route::post('order/edit'        ,   'Order\OrderController@editOrderById');
    //下单用户取消订单
    Route::post('order/cancel'       ,   'Order\OrderController@cancelOrder');

    //信息管理
    Route::post('info/manage'        ,   'Info\InfoController@manageInfo');
    Route::post('info/add'          ,   'Info\InfoController@addInfo');


    //供应商管理
    Route::post('supplier/add'      ,   'Supplier\SupplierController@addSupplier');
    Route::post('supplier/edit'      ,   'Supplier\SupplierController@editSupplier');
});



