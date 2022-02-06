<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGhestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ghests', function (Blueprint $table) {
            $table->id();
            $table->string('type', 10)->index()->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->foreign('order_id')->references('id')->on('orders');
            $table->unsignedBigInteger('payment_id')->unique()->nullable();
            $table->foreign('payment_id')->references('id')->on('payments');
            $table->unsignedBigInteger('account_id')->index()->nullable();
            $table->foreign('account_id')->references('id')->on('accounts');
            $table->string('series', 20)->nullable();
            $table->integer('amount')->nullable();
            $table->timestamp('ghest_date')->nullable();
            $table->string('shamsi', 10)->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('passed_at')->nullable();
            $table->timestamp('backed_at')->nullable();
            $table->timestamp('first_alarm_at')->nullable();
            $table->timestamp('last_alarm_at')->nullable();
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
        Schema::dropIfExists('ghests');
    }
}
