<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsermetasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usermetas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users'); 
            $table->timestamp('verified_at')->nullable();
            $table->string('mobile_alt', 10)->nullable();
            $table->string('gender', 1)->nullable();
            $table->tinyInteger('is_married')->nullable();
            $table->string('education', 10)->nullable();
            $table->tinyInteger('has_cheque')->nullable();
            $table->string('job', 50)->nullable();
            $table->tinyInteger('salary')->nullable();
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
        Schema::dropIfExists('usermetas');
    }
}
