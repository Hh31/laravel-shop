<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCouponCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_codes', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('优惠券表id');
            $table->string('name')->comment('优惠券标题');
            $table->string('code')->unique()->comment('优惠码');
            $table->string('type')->comment('优惠类型');
            $table->decimal('value')->comment('折扣值');
            $table->unsignedInteger('total')->comment('全网可兑换数量');
            $table->unsignedInteger('used')->default(0)->comment('当前已兑换数量');
            $table->decimal('min_amount', 10, 2)->comment('使用该优惠券最低订单金额');
            $table->datetime('not_before')->nullable()->comment('此时间段之前不可用');
            $table->datetime('not_after')->nullable()->comment('此时间段之后不可用');
            $table->boolean('enabled')->comment('是否生效');
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
        Schema::dropIfExists('coupon_codes');
    }
}
