<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotesTable extends Migration
{
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->index()->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->string('template', 50)->nullable();
            $table->string('title', 50)->nullable();
            $table->text('caption')->nullable();
            $table->string('attachment', 100)->nullable();
            $table->string('meta', 150)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users');
            $table->unsignedBigInteger('edited_by')->nullable();
            $table->foreign('edited_by')->references('id')->on('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notes');
    }
}
