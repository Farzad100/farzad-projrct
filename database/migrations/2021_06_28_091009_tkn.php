<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Tkn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tkn', function (Blueprint $table) {
            $table->id();
            $table->string('name',20)->nullable();
            $table->string('type',20)->nullable();
            $table->string('token',1500);
            $table->string('rtoken',1500);
            $table->timestamp('expired_at')->nullable();
            $table->index(['name', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tkn');
    }
}
