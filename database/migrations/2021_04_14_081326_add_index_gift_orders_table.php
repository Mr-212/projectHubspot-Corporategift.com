<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexGiftOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gift_orders', function (Blueprint $table) {
           $table->index(['app_id','object_id','object_type'],'app_object_type_index');
           $table->index('gift_id','gift_id_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gift_orders', function (Blueprint $table) {
            $table->dropIndex('app_object_type_index');
            $table->dropIndex('gift_id_index');
        });
    }
}
