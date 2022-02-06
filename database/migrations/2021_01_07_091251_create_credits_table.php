<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('order_id')->index()->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedBigInteger('shop_id')->index()->nullable();
            $table->foreign('shop_id')->references('id')->on('shops'); 
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->string('status', 20)->nullable();
            $table->unsignedInteger('amount')->nullable();
            $table->unsignedInteger('prepayment')->nullable();
            $table->unsignedInteger('credit_required')->nullable();
            $table->unsignedInteger('credit')->nullable();
            $table->unsignedInteger('cash')->nullable(); 
            $table->unsignedInteger('payable')->nullable(); 
            $table->string('track_id', 20)->nullable();
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamp('checked_at')->nullable();
            $table->string('meta', 200)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('credits');
    }
}
