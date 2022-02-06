<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('docs', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('user_id')->index()->nullable()->constrained();
            $table->foreignId('order_id')->index()->nullable()->constrained(); 
            $table->foreignId('shop_id')->index()->nullable()->constrained(); 
            $table->foreignId('company_id')->index()->nullable()->constrained(); 
            $table->foreignId('organ_id')->index()->nullable()->constrained(); 
            $table->foreignId('account_id')->index()->nullable()->constrained();
            $table->string('type',30)->index()->nullable(); //NationalCard,BusinessLicense,StoreDocument,StorePicture,...
            $table->tinyInteger('is_verified')->default(0); 
            $table->timestamp('uploaded_at')->nullable(); 
            $table->timestamp('decided_at')->nullable();
            $table->string('title',50)->nullable();
            $table->string('address',100)->nullable();
            $table->foreignId('decided_by')->nullable()->constrained('users');
            $table->foreignId('uploaded_by')->nullable()->constrained('users');
            $table->string('reason',500)->nullable();
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
        Schema::dropIfExists('docs');
    }
}
