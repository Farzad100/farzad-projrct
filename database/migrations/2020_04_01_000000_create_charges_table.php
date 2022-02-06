<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChargesTable extends Migration
{
    public function up()
    {
        Schema::create('charges', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('card_id')->nullable();
            $table->foreign('card_id')->references('id')->on('cards');
            $table->string('type',10)->nullable();
            $table->unsignedBigInteger('amount')->nullable();
            $table->tinyInteger('status')->default(0);
            $table->timestamp('charged_at')->nullable(); 
            $table->string('tracker', 50)->nullable();
            $table->string('track_id', 40)->nullable();
            $table->string('deposit', 30)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->string('comment',200)->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('charges');
    }
}
