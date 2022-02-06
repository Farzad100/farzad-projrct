<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBontxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bontxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('card_id')->nullable();
            $table->foreign('card_id')->references('id')->on('cards');
            $table->unsignedBigInteger('trace')->index()->nullable();
            $table->unsignedBigInteger('amount')->nullable();
            $table->unsignedBigInteger('available')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->string('tx_date', 15)->nullable();
            $table->string('refnum', 20)->nullable();
            $table->string('prcode', 20)->nullable();
            $table->string('acc_termid', 20)->nullable();
            $table->string('error', 500)->nullable();
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
        Schema::dropIfExists('bontxes');
    }
}
