<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 15)->index()->nullable();
            $table->string('fname', 50)->nullable();
            $table->string('lname', 50)->nullable();
            $table->string('nid', 12)->index()->nullable();
            $table->string('birth', 10)->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->string('email', 180)->nullable();
            $table->tinyInteger('is_admin')->default(0);
            $table->string('rfrr', 50)->nullable();
            $table->string('utm_source', 50)->nullable();
            $table->string('utm_medium', 50)->nullable();
            $table->string('utm_campaign', 50)->nullable();
            $table->string('utm_content', 50)->nullable();
            $table->string('click_id', 50)->nullable();
            $table->string('invite_code', 30)->nullable();
            $table->string('badge', 10)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('username_alt', 50)->nullable();
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
