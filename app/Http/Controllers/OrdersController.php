<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\Request;
use App\Http\Requests\SendReviewRequest;
use App\Models\Order;
use App\Models\UserAddress;
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

    public function received(Order $order,Request $request){
        //校验权限
//        $this->authorize('own',$order);

        //判断订单的发货状态是否为已发货
        if ($order->ship_status !== Order::SHIP_STATUS_DELIVERED){
            throw new InternalException('订单的发货状态不正确');
        }

        //更新发货状态为已收货
        $order->update(['ship_status' => Order::SHIP_STATUS_RECEIVED]);

        //原网页
        return redirect()->back();
    }

    public function review(Order $order){
        // 校验权限
//        $this->authorize('own', $order);
        //判断是否已经支付
        if (!$order->paid_at){
            throw new InvalidRequestException('该订单未支付，不可评价');
        }

        return view('orders.review',['order'=>$order->load(['items.product','items.productSku'])]);
    }

    public function sendReview(Order $order,SendReviewRequest $request){
        // 校验权限
//        $this->authorize('own', $order);
        //判断是否已经支付
        if (!$order->paid_at){
            throw new InvalidRequestException('该订单未支付，不可评价');
        }
        //判断是否已经评价
        if($order->reviewed){
            throw new InvalidRequestException('该订单已经评价，不可重复提交');
        }

        $reviews = $request->input('reviews');

        //开启事务
        \DB::transaction(function () use ($reviews,$order){
            //遍历用户提交的数据
            foreach ($reviews as $review){
                $orderItem = $order->items()->find($review['id']);

                //保存评分和评价
                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }
            $order->update(['reviewed' => true]);
            event(new OrderReviewed($order));
        });

        return redirect()->back();
    }

    public function applyRefund(ApplyRefundRequest $request,Order $order){
//        //校验订单是否属于当前订单
//        $this->authorize('own',$order);
        //订单是否已经付款
        if(!$order->paid_at){
            throw new InvalidRequestException('未付款，无法退款');
        }
        //判断退款状态
        if ($order->refund_status !== Order::REFUND_STATUS_PENDING){
            throw new InvalidRequestException('该订单已经申请过退款，请勿重复提交');
        }

        $extra = $order->extra?:[];
        $extra['refund_reason'] = $request->input('reason');

        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra' => $extra,
        ]);

        return $order;
    }
}
