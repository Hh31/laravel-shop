<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    //
    public function index(Request $request) {
//        $products = Product::query()->where('on_sale',true)->paginate(16);
//
//        return view('products.index',['products' => $products]);
        //创建一个查询构造器
        $products = Product::query()->where('on_sale',true);
        //判断是否有提交$search参数，如果有就赋值给$search参数
        //
        if ($search = $request->input('search','')) {
            $like = '%'.$search.'%';
            $products->where(function ($query) use ($like){
                $query->where('title','like',$like)
                    ->OrWhere('description','like',$like)
                    ->OrWhereHas('skus',function ($query) use ($like){
                        $query->where('title','like',$like)
                            ->Orwhere('description','like',$like);
                    });
            });
        }
        if ($order = $request->input('order','')){
            if (preg_match('/^(.+)_(asc|desc)$/',$order,$m)){
                if(in_array($m[1],['price','sold_count','rating'])){
                    $products->orderBy($m[1],$m[2]);
                }
            }
        }
        $products = $products->paginate(16);
        return view('products.index',[
            'products' => $products,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
        ]);
    }
}
