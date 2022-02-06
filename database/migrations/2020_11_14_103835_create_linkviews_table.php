<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLinkviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('linkviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_id')->index()->nullable();
            $table->foreign('link_id')->references('id')->on('links');
            $table->string('ip', 20)->nullable();
            $table->string('country', 40)->nullable();
            $table->index(['ip', 'link_id']);
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
        Schema::dropIfExists('linkviews');
    }
}
