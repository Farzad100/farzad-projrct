<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountsTable extends Migration
{
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->index()->nullable();
            $table->unsignedInteger('amount')->nullable();
            $table->unsignedInteger('percent')->nullable();
            $table->string('title', 50)->nullable();
            $table->tinyInteger('just_first')->default(1);
            $table->string('tags',50)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->unsignedInteger('limit')->nullable();
            $table->integer('limit_per_user')->nullable();
            $table->tinyInteger('is_active')->default(1);
            $table->string('comment', 300)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}
