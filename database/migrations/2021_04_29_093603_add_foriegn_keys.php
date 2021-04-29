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
            $table->foreign('app_id')->nullable()->constrained("app")->cascadeOnUpdate()->nullOnDelete();;
        });

        Schema::table('gift_orders', function (Blueprint $table) {
            $table->foreign('app_id')->nullable()->constrained("app")->cascadeOnUpdate()->cascadeOnDelete();;
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
