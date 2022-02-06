<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInboxesTable extends Migration
{
    public function up()
    {
        Schema::create('inboxes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->unsignedBigInteger('created_by')->index()->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->string('related_to', 30)->index()->nullable();
            $table->string('title', 100)->nullable();
            $table->text('caption')->nullable();
            $table->unsignedBigInteger('attachment_id')->index()->nullable();
            $table->foreign('attachment_id')->references('id')->on('docs');
            $table->string('meta', 200)->nullable();
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
        Schema::dropIfExists('inboxes');
    }
}
