
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->index()->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->string('oid',20)->unique();
            $table->string('status',20)->nullable();
            $table->integer('amount')->nullable();
            $table->integer('costs')->nullable();
            $table->unsignedBigInteger('prepayment')->nullable();
            $table->unsignedTinyInteger('months')->nullable();
            $table->unsignedTinyInteger('cheques')->nullable();
            $table->unsignedTinyInteger('passed')->default(0);
            $table->unsignedBigInteger('ghest')->nullable();
            $table->unsignedBigInteger('total')->nullable();
            $table->unsignedTinyInteger('allowed_ghest')->nullable();
            $table->decimal('gain',6,5)->nullable();
            $table->string('product',100)->nullable();
            $table->unsignedBigInteger('organ_id')->nullable();
            $table->foreign('organ_id')->references('id')->on('organs');
            $table->timestamp('organ_accepted_at')->nullable();
            $table->unsignedBigInteger('shop_id')->nullable();
            $table->foreign('shop_id')->references('id')->on('shops');
            $table->string('address',300)->nullable();
            $table->string('payback_type',10)->default('cheque');
            $table->unsignedInteger('series')->nullable();
            $table->unsignedInteger('series_card')->nullable();
            $table->timestamp('first_ghest_at')->nullable();
            $table->timestamp('docs_uploaded_at')->nullable();
            $table->timestamp('docs_warning_at')->nullable();
            $table->timestamp('docs_accepted_at')->nullable();
            $table->timestamp('secondary_uploaded_at')->nullable();
            $table->timestamp('secondary_accepted_at')->nullable();
            $table->timestamp('docs_received_at')->nullable();
            $table->timestamp('prepaid_at')->nullable();
            $table->timestamp('charged_at')->nullable();
            $table->string('charge_type',10)->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->text('reason')->nullable();
            $table->unsignedBigInteger('provider_id')->index()->nullable();
            $table->foreign('provider_id')->references('id')->on('providers');
            $table->unsignedBigInteger('guaranteed_by')->index()->nullable();
            $table->foreign('guaranteed_by')->references('id')->on('users'); 
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
        Schema::dropIfExists('orders');
    }
}
