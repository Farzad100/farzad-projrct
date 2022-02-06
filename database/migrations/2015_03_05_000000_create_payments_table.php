<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedBigInteger('credit_id')->nullable();
            $table->string('type', 30)->nullable();
            $table->unsignedInteger('amount')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->string('ref_id', 50)->nullable();
            $table->unsignedBigInteger('discount_id')->nullable();
            $table->foreign('discount_id')->references('id')->on('discounts');
            $table->string('authority', 100)->nullable();
            $table->string('card_mask', 20)->nullable();
            $table->text('card_hash')->nullable();
            $table->string('token', 5)->nullable();
            $table->string('ip', 20)->nullable();
            $table->timestamp('charged_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->string('meta', 500)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payments');
    }
}
