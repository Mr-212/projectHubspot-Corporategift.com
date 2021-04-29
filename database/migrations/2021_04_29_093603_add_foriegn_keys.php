<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddForiegnKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreign('app_id')->reference('id')->on('apps')->onDelete('null')->onUpdate('cascade');
        });

        Schema::table('gift_orders', function (Blueprint $table) {
            $table->foreign('app_id')->reference('id')->on('apps')->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign('app_id');
        });

        Schema::table('gift_orders', function (Blueprint $table) {
            $table->dropForeign('app_id');
        });
    }
}
