<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('group', 20)->nullable();
            $table->string('type', 20)->nullable();
            $table->string('name', 40)->nullable();
            $table->tinyInteger('is_public')->default(1);
            $table->string('prop', 20)->nullable();
            $table->text('val')->nullable();
            $table->text('comment')->nullable();
            $table->tinyInteger('display')->default(1);
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
        Schema::dropIfExists('settings');
    }
}
