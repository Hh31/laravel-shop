<?php

namespace App\Admin\Controllers;

use App\Exceptions\InternalException;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\Admin\HandleRefundRequest;
use App\Http\Requests\Request;
use App\Models\Order;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;
use Illuminate\Foundation\Validation\ValidatesRequests;

class OrdersController extends AdminController
{
    use ValidatesRequests;
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = '订单';

    protected function grid()
    {
        $grid = new Grid(new Order);

        // 只展示已支付的订单，并且默认按支付时间倒序排序
        $grid->model()->whereNotNull('paid_at')->orderBy('paid_at', 'desc');

        $grid->no('订单流水号');
        // 展示关联关系的字段时，使用 column 方法
        $grid->column('user.name', '买家');
        $grid->total_amount('总金额')->sortable();
        $grid->paid_at('支付时间')->sortable();
        $grid->ship_status('物流')->display(function($value) {
            return Order::$shipStatusMap[$value];
        });
        $grid->refund_status('退款状态')->display(function($value) {
            return Order::$refundStatusMap[$value];
        });
        // 禁用创建按钮，后台不需要创建订单
        $grid->disableCreateButton();
        $grid->actions(function ($actions) {
            // 禁用删除和编辑按钮
            $actions->disableDelete();
            $actions->disableEdit();
        });
        $grid->tools(function ($tools) {
            // 禁用批量删除按钮
            $tools->batch(function ($batch) {
                $batch->disableDelete();
            });
        });

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Order::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('no', __('No'));
        $show->field('user_id', __('User id'));
        $show->field('address', __('Address'));
        $show->field('total_amount', __('Total amount'));
        $show->field('remark', __('Remark'));
        $show->field('paid_at', __('Paid at'));
        $show->field('payment_method', __('Payment method'));
        $show->field('payment_no', __('Payment no'));
        $show->field('refund_status', __('Refund status'));
        $show->field('refund_on', __('Refund on'));
        $show->field('closed', __('Closed'));
        $show->field('reviewed', __('Reviewed'));
        $show->field('ship_status', __('Ship status'));
        $show->field('ship_data', __('Ship data'));
        $show->field('extra', __('Extra'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Order);

        $form->text('no', __('No'));
        $form->number('user_id', __('User id'));
        $form->textarea('address', __('Address'));
        $form->decimal('total_amount', __('Total amount'));
        $form->textarea('remark', __('Remark'));
        $form->datetime('paid_at', __('Paid at'))->default(date('Y-m-d H:i:s'));
        $form->text('payment_method', __('Payment method'));
        $form->text('payment_no', __('Payment no'));
        $form->text('refund_status', __('Refund status'))->default('pending');
        $form->text('refund_on', __('Refund on'));
        $form->switch('closed', __('Closed'));
        $form->switch('reviewed', __('Reviewed'));
        $form->text('ship_status', __('Ship status'))->default('pending');
        $form->textarea('ship_data', __('Ship data'));
        $form->textarea('extra', __('Extra'));

        return $form;
    }

    public function show($id, Content $content)
    {
        return $content->header('查看订单')->body(view('admin.orders.show',['order' =>Order::find($id)]));
    }

    public function ship(Order $order,Request $request){
        //判断当前订单是否已支付
        if(!$order->paid_at) {
            throw new InternalException('该订单未付款');
        }
        //判断当前订单是是否发货
        if($order->ship_status !== Order::SHIP_STATUS_PENDING){
            throw new InternalException('该订单已发货');
        }
        //
        $data = $this->validate($request,[
            'express_company' => ['required'],
            'express_no' => ['required'],
        ],[],[
            'express_company' => '物流公司',
            'express_no' => '物流编号'
        ]);

        $order->update([
            'ship_status' => Order::SHIP_STATUS_DELIVERED,
            'ship_data' => $data,
        ]);

        //返回上一页
        return redirect()->back();
    }
    public function handleRefund(Order $order, HandleRefundRequest $request)
    {
        // 判断订单状态是否正确
        if ($order->refund_status !== Order::REFUND_STATUS_APPLIED) {
            throw new InvalidRequestException('订单状态不正确');
        }
        // 是否同意退款
        if ($request->input('agree')) {
            //清空拒绝退款理由
            $extra =$order->extra?:[];
            unset($extra['refund_disagree_reason']);
            $order->update([
                'extra' => $extra
            ]);
            $this->_refundOrder($order);
        } else {
            // 将拒绝退款理由放到订单的 extra 字段中
            $extra = $order->extra ?: [];
            $extra['refund_disagree_reason'] = $request->input('reason');
            // 将订单的退款状态改为未退款
            $order->update([
                'refund_status' => Order::REFUND_STATUS_PENDING,
                'extra'         => $extra,
            ]);
        }

        return $order;
    }

    private function _refundOrder(Order $order)
    {
        //判断订单的支付方式
        switch ($order->payment){
            case 'wechat':
                break;
            case 'alipay':
                $refundNo = Order::getAvailableRefund();
                //调用支付宝支付实例的refund的方法
                $ret = app('alipay')->refund([
                   'out_trade_no' => $order->no,
                   'refund_amount' =>$order->total_amount,
                   'out_request_no' => $refundNo,
                ]);
                //根据支付宝的文档，如果返回值力有sub_code字段说明退款失败
                if($ret->sub_code){
                    //将退款失败的保存存入extra字段
                    $extra = $order->extra;
                    $extra['refund_failed_code'] = $ret->sub_code;
                    //将订单的退款状态标记为退款失败
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => $extra,
                    ]);
                }else{
                    //将订单的退款状态标记为退款成功并保存退款订单号
                    $order->update([
                       'refund_no' => $refundNo,
                       'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
            default :
                throw new InvalidRequestException('未知订单支付方式',$order->payment_method);
                break;
        }
    }
}
