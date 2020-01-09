<?php

namespace App\Http\Controllers;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\Request;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Services\CartService;
use App\Services\OrderService;
use Carbon\Carbon;


class OrdersController extends Controller
{
    public function store(OrderRequest $request,OrderService $orderService){
        $user = $request->user();
        $address = UserAddress::find($request->input('address_id'));
        return $orderService->store($user,$address,$request->input('remark'),$request->input('items'));
    }

    public function index(Request $request){
        $orders = Order::query()
            ->with(['items.product','items.productSku'])
            ->where('user_id',$request->user()->id)
            ->orderBy('created_at','desc')
            ->paginate();

        return view('orders.index',['orders'=>$orders]);
    }

    public function show(Order $order,Request $request){
        return view('orders.show',['order' => $order->load(['items.product','items.productSku'])]);
    }
}