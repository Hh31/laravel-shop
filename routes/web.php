<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


//Route::get('/', 'PagesController@root')->name('root')->middleware('verified');

Route::redirect('/', '/products')->name('root');
Route::get('products','ProductsController@index')->name('products.index');//首页





Auth::routes(['verify' => true]);


//Route::get('/', 'PagesController@root')->name('root');
//
//Auth::routes();

Route::group(['middleware' => ['auth','verified']], function () {
    //地址
    Route::get('user_addresses', 'UserAddressesController@index')->name('user_addresses.index');//地址列表
    Route::get('user_addresses/create', 'UserAddressesController@create')->name('user_addresses.create');//
    Route::post('user_addresses', 'UserAddressesController@store')->name('user_addresses.store');//
    Route::get('user_addresses/{user_address}', 'UserAddressesController@edit')->name('user_addresses.edit');//
    Route::put('user_addresses/{user_address}', 'UserAddressesController@update')->name('user_addresses.update');//
    Route::delete('user_addresses/{user_address}', 'UserAddressesController@destroy')->name('user_addresses.destroy');//删除地址

    //商品
    Route::post('products/{product}/favorite','ProductsController@favor')->name('products.favor');//收藏
    Route::delete('products/{product}/favorite','ProductsController@disfavor')->name('products.disfavor');//取消收藏
    Route::get('products/favorites', 'ProductsController@favorites')->name('products.favorites');

    //购物车
    Route::post('cart', 'CartController@add')->name('cart.add');
    Route::get('cart', 'CartController@index')->name('cart.index');//购物车详情
    Route::delete('cart/{sku}', 'CartController@remove')->name('cart.remove');

    //订单
    Route::post('orders', 'OrdersController@store')->name('orders.store');
    Route::get('orders', 'OrdersController@index')->name('orders.index');//订单页
    Route::get('orders/{order}', 'OrdersController@show')->name('orders.show');//订单详情
    Route::post('orders/{order}/received', 'OrdersController@received')->name('orders.received');
    Route::get('orders/{order}/review', 'OrdersController@review')->name('orders.review.show');
    Route::post('orders/{order}/review', 'OrdersController@sendReview')->name('orders.review.store');
    Route::post('orders/{order}/apply_refund', 'OrdersController@applyRefund')->name('orders.apply_refund');

    //支付
    Route::get('payment/{order}/alipay', 'PaymentController@payByAlipay')->name('payment.alipay');//支付宝支付
    Route::get('payment/alipay/return', 'PaymentController@alipayReturn')->name('payment.alipay.return');


});

Route::get('products/{product}','ProductsController@show')->name('products.show');//商品详情
Route::post('payment/alipay/notify', 'PaymentController@alipayNotify')->name('payment.alipay.notify');
Route::get('alipay', function() {
    return app('alipay')->web([
        'out_trade_no' => time(),
        'total_amount' => '1',
        'subject' => 'test subject - 测试',
    ]);
});
