    <?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');//后台主页
    $router->get('users','UsersController@index');//用户管理
    $router->get('products', 'ProductsController@index');//商品管理
    $router->get('products/create','ProductsController@create');//新建商品
    $router->post('products','ProductsController@store');
    $router->get('products/{id}/edit', 'ProductsController@edit');//编辑商品
    $router->put('products/{id}', 'ProductsController@update');
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
    $router->post('orders/{order}/ship', 'OrdersController@ship')->name('admin.orders.ship');
    $router->post('orders/{order}/refund', 'OrdersController@handleRefund')->name('admin.orders.handle_refund');
    $router->get('coupon_codes', 'CouponCodesController@index');
});
