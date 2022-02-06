<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Icbs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('icbs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('method', 40)->index()->nullable();
            $table->mediumText('result')->nullable();
            $table->string('extra', 50)->nullable();
            $table->unsignedBigInteger('admin_id')->index()->nullable();
            $table->foreign('admin_id')->references('id')->on('users');
            $table->timestamps();

            $table->index(['nid', 'extra', 'method']);
            $table->index(['nid', 'method']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('icbs');
    }
}
