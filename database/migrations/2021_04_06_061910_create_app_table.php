<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('hub_id')->nullable();
            $table->bigInteger('hub_app_id')->nullable();
            $table->bigInteger('hub_user_id')->nullable();
            $table->string('hub_user')->nullable();
            $table->string('hub_access_token')->nullable();
            $table->string('hub_refresh_token')->nullable();
            $table->string('hub_expires_in')->nullable();

            $table->string('corporate_gift_token')->nullable();
            $table->boolean('is_active')->nullable()->default(0);
            $table->text('request_data')->nullable();
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
        Schema::dropIfExists('app');
    }
}
