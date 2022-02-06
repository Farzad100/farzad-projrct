<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChunksTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('chunks', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id')->index()->nullable();
      $table->foreign('user_id')->references('id')->on('users');
      $table->string('file_id', 64)->index()->nullable();
      $table->unsignedTinyInteger('part')->index()->nullable();
      $table->string('name', 100)->nullable();
      $table->string('client_name', 100)->nullable();
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
    Schema::dropIfExists('chunks');
  }
}
