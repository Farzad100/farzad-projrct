<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrgansTable extends Migration
{
  public function up()
  {
    Schema::create('organs', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->unsignedBigInteger('user_id')->nullable();
      $table->foreign('user_id')->references('id')->on('users');
      $table->unsignedBigInteger('company_id')->nullable();
      $table->foreign('company_id')->references('id')->on('companies');
      $table->unsignedBigInteger('code')->unique()->nullable();
      $table->string('name', 50)->nullable();
      $table->string('fame', 50)->nullable();
      $table->string('type', 5)->default('i'); //g,i
      $table->bigInteger('credit')->default(0);
      $table->tinyInteger('negative_percent')->default(0);
      $table->unsignedInteger('employees')->nullable(); // 20,100,500,2500,100000
      $table->tinyInteger('age')->nullable(); // 3,10,100
      $table->string('phone', 20)->nullable();
      $table->string('phone_direct', 10)->nullable();
      $table->timestamp('phone_verified_at')->nullable();
      $table->string('website', 100)->nullable();
      $table->string('email', 100)->nullable();
      $table->text('about')->nullable();
      $table->timestamp('start_at')->nullable();
      $table->string('status', 20)->nullable();
      $table->tinyInteger('level')->default(1);
      $table->string('agent_position', 10)->nullable(); // cao,ceo,cfo,chro,hr
      $table->timestamp('docs_uploaded_at')->nullable();
      $table->timestamp('docs_accepted_at')->nullable();
      $table->timestamp('premium_uploaded_at')->nullable();
      $table->timestamp('premium_accepted_at')->nullable();
      $table->string('payback_type', '10')->default('cheque');
      $table->string('meta',500)->nullable();
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
    Schema::dropIfExists('organs');
  }
}
