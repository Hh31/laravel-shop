<?php

namespace App\Services;

use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartService{
    public function get(){
        return Auth::user()->cartItems()->with(['productSku.product'])->get();
    }

    public function add($skuId,$amount){
        $user = Auth::user();

        //从数据表中查询该商品是否已经在购物车中
        if($items = $user->cartItems()->where('product_sku_id',$skuId)->first()){
            $items->update(['amount' => $items->amount+$amount]);
        }else{
            $items = new CartItem(['amount'=>$amount]);
            $items->user()->associate($user);
            $items->productSku()->associate($skuId);
            $items->save();
        }
        return $items;
    }

    public function remove($skuIds){
        if(!is_array($skuIds)){
            $skuIds = [$skuIds];
        }
        Auth::user()->cartItems()->whereIn('product_sku_id',$skuIds)->delete();
    }
}
