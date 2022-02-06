<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateShopsTable extends Migration
{
  public function up()
  {
    Schema::create('shops', function (Blueprint $table) {
      $table->id();
      $table->unsignedBigInteger('user_id')->index()->nullable();
      $table->foreign('user_id')->references('id')->on('users');
      $table->unsignedBigInteger('company_id')->nullable();
      $table->foreign('company_id')->references('id')->on('companies');
      $table->string('name', 100)->nullable();
      $table->string('category', 20)->nullable();
      $table->string('type', 10)->nullable();
      $table->string('website', 50)->nullable();
      $table->string('email', 150)->nullable();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('phone', 20)->nullable();
      $table->string('phone_direct', 10)->nullable();
      $table->timestamp('phone_verified_at')->nullable();
      $table->string('state', 30)->nullable();
      $table->string('city', 30)->nullable();
      $table->string('address', 200)->nullable();
      $table->string('postal_code', 15)->nullable();
      $table->timestamp('address_verified_at')->nullable();
      $table->string('status', 20)->default('pending');
      $table->timestamp('agreed_at')->nullable();
      $table->timestamp('docs_uploaded_at')->nullable();
      $table->timestamp('docs_accepted_at')->nullable();
      $table->timestamp('start_at')->nullable();
      $table->boolean('quick_payback')->default(true);
      $table->boolean('commission_default')->default(true);
      $table->decimal('commission_percent', 5, 2)->default(0);
      $table->unsignedInteger('commission_amount')->default(0);
      $table->text('about')->nullable();
      $table->string('meta', 200)->nullable();
      $table->timestamp('verified_at')->nullable();
      $table->timestamps();
      $table->softDeletes();
    });
  }

  public function down()
  {
    Schema::dropIfExists('shops');
  }
}
