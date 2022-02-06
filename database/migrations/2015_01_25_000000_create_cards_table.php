<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
  public function up()
  {
    Schema::create('cards', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id')->nullable();
      $table->foreign('user_id')->references('id')->on('users'); 
      $table->string('card_number', 20)->unique()->nullable();
      $table->tinyInteger('status')->default(1);
      $table->timestamp('sent_at')->nullable();
      $table->timestamp('delivered_at')->nullable();
      $table->unsignedBigInteger('series')->nullable();
      $table->string('comment', 190)->nullable();
      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down()
  {
    Schema::dropIfExists('cards');
  }
}
