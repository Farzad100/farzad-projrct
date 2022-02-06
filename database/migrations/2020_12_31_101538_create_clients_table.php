<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name', 30)->unique()->nullable();
            $table->tinyInteger('is_active')->default(0);
            $table->string('title', 50)->nullable();
            $table->string('scopes', 70)->nullable();
            $table->string('token', 400)->nullable();
            $table->timestamp('token_expired_at')->nullable();
            $table->string('refresh_token', 200)->nullable();
            $table->timestamp('refresh_token_expired_at')->nullable();
            $table->string('meta', 100)->nullable();
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
        Schema::dropIfExists('clients');
    }
}
