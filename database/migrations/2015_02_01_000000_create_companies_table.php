<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompaniesTable extends Migration
{

    public function up()
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->nullable();
            $table->string('fame', 30)->nullable();
            $table->string('nic', 20)->unique()->nullable(); //national id of company
            $table->string('ec', 20)->nullable(); //economic code
            $table->string('rn', 20)->nullable(); //registration number
            $table->string('ceo_fname', 50)->nullable();
            $table->string('ceo_lname', 50)->nullable();
            $table->string('ceo_nid', 50)->nullable();
            $table->string('ceo_mobile', 15)->nullable();
            $table->string('state', 50)->nullable();
            $table->string('city', 50)->nullable();
            $table->string('address', 300)->nullable();
            $table->string('postal_code', 15)->nullable();
            $table->timestamp('address_verified_at')->nullable();
            $table->string('phone', 20)->nullable();
            $table->timestamp('phone_verified_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('companies');
    }
}
