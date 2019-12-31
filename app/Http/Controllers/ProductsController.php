<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductsController extends Controller
{
    //
    /**
     * 商品首页
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @author: hefusheng 2019/12/31
     */
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

    /**
     * 商品详情页
     * @param Request $request
     * @param Product $product
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws InvalidRequestException
     * @author: hefusheng 2019/12/31
     */
    public function show (Request $request,Product $product) {
        if (!$product->on_sale) {
            throw new InvalidRequestException('商品未上架');
        }
        $favored = false;
        if($users = $request->user()){
            $favored = boolval($users->favoriteProducts->find($product->id));
        }

        return view('products.show',['product' => $product,'favored' => $favored]);
    }

    /**
     * 添加收藏
     * @param Product $product
     * @param Request $request
     * @return array
     * @author: hefusheng 2019/12/31
     */
    public function favor(Product $product,Request $request){
        $users=$request->user();
        if($users->favoriteProducts()->find($product->id)){
            return [];
        }

        $users->favoriteProducts()->attach($product);
        return [];
    }

    /**
     * 取消收藏
     * @param Product $product
     * @param Request $request
     * @return array
     * @author: hefusheng 2019/12/31
     */
    public function disfavor(Product $product,Request $request){
        $users = $request->user();
        $users->favoriteProducts()->detach($product);

        return [];
    }

    public function favorites(Request $request){
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites',['products' => $products]);
    }
}
