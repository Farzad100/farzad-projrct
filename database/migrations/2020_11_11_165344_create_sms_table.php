<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('mobile', 20)->index()->nullable();
            $table->unsignedBigInteger('bulk_id')->nullable();
            $table->unsignedBigInteger('pattern_id')->index()->nullable();
            $table->foreign('pattern_id')->references('id')->on('patterns');
            $table->text('message')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('status', '20')->index()->nullable();
            $table->unsignedInteger('cost')->nullable();
            $table->unsignedInteger('cost_origin')->nullable();
            $table->unsignedInteger('payback')->nullable();
            $table->string('number', '20')->nullable();
            $table->string('meta', '100')->nullable();
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
        Schema::dropIfExists('sms');
    }
}
