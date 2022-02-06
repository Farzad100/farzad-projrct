<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->id();
            $table->string('prop',15)->nullable(); //mobile, email,
            $table->string('val',100)->index()->nullable();
            $table->string('type',30)->index()->nullable();
            $table->tinyInteger('is_verified')->default(0);
            $table->timestamp('verified_at')->nullable();
            $table->string('otp',10)->nullable();
            $table->timestamp('otp_sent_at')->nullable();
            $table->tinyInteger('try_times')->default(0);
            $table->string('track_id',20)->index()->nullable();
            $table->string('ip', 20)->nullable();
            $table->string('meta', 200)->nullable();
            $table->timestamps();

            $table->index(['val','track_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verifications');
    }
}
