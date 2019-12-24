<?php
function test_helper()
{
    return 'OK';
}

/**
 * 将当前请求的路由名称转换为 CSS 类名称
 * @return mixed
 * @author: hefusheng 2019/12/24
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}
