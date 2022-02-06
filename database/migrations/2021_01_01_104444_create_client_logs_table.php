<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('client_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('client_id')->index()->nullable();
            $table->foreign('client_id')->references('id')->on('clients');
            $table->unsignedInteger('scope')->nullable();
            $table->tinyInteger('ok')->default(0);
            $table->string('track_id', 100)->index()->nullable();
            $table->string('request', 550)->nullable();
            $table->string('response', 250)->nullable();
            $table->string('ip', 20)->nullable();
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
        Schema::dropIfExists('client_logs');
    }
}
