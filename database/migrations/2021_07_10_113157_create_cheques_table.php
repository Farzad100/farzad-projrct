<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChequesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->index()->nullable()->constrained();
            $table->string('type', 5)->index()->nullable(); //chf,chb,gch
            $table->tinyInteger('badge')->nullable();
            $table->foreignId('account_id')->index()->nullable()->constrained();
            $table->string('isbn', 20)->nullable();
            $table->string('prepend', 5)->nullable();
            $table->string('series', 6)->nullable();
            $table->string('append', 5)->nullable();
            $table->string('bank_id', 3)->nullable();
            $table->tinyInteger('is_submitted')->nullable();
            $table->tinyInteger('is_verified')->nullable();
            $table->tinyInteger('need_chb')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->foreignId('decided_by')->nullable()->constrained('users');
            $table->timestamp('photo_verified_at')->nullable();
            $table->timestamp('data_verified_at')->nullable();
            $table->timestamp('submit_verified_at')->nullable();
            $table->string('address', 100)->nullable();
            $table->string('format', 5)->nullable();
            $table->string('cdn', 5)->nullable();
            $table->string('reason', 300)->nullable();
            $table->string('meta', 500)->nullable();
            $table->unsignedInteger('size')->nullable();
            $table->tinyInteger('is_readable')->nullable();
            $table->string('provider', 10)->nullable();
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
        Schema::dropIfExists('cheques');
    }
}
