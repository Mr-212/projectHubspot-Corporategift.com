<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGiftOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gift_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('app_id')->nullable();
            $table->unsignedBigInteger('object_id')->nullable();
            $table->string('object_type')->nullable();

            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('gift_id')->nullable();
            $table->unsignedBigInteger('gift_number')->nullable();

            $table->string('status')->nullable();
            $table->text('api_request')->nullable();
            $table->text('api_response')->nullable();
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
        Schema::dropIfExists('gift_orders');
    }
}
