<?php

use App\Models\Order;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('no')->unique();
            $table->unsignedBigInteger('user_id')->comment('用户id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('address')->comment('地址');
            $table->decimal('total_amount',10,2)->comment('总金额');
            $table->text('remark')->nullable()->comment('订单备注');
            $table->dateTime('paid_at')->nullable()->comment('支付时间');
            $table->string('payment_method')->nullable()->comment('支付方法');
            $table->string('payment_no')->nullable()->comment('支付编号');
            $table->string('refund_status')->default(\App\Models\Order::REFUND_STATUS_PENDING)->comment('退款状态');
            $table->string('refund_on')->nullable()->unique()->comment('退款单号');
            $table->boolean('closed')->default(false)->comment('订单是否关闭');
            $table->boolean('reviewed')->default(false)->comment('订单是否评价');
            $table->string('ship_status')->default(Order::SHIP_STATUS_PENDING)->comment('物流状态');
            $table->text('ship_data')->comment('物流数据');
            $table->text('extra')->comment('其他额外数据');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
