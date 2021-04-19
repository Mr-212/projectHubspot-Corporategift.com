<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIndexAppTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('app', function (Blueprint $table) {
           $sm = Schema::getConnection()->getDoctrineSchemaManager();
            // $sm = Schema::getConnection();
           $indexesFound = $sm->listTableIndexes('app');
        
           if(!array_key_exists("hub_app_user_index", $indexesFound))
                $table->index(['hub_app_id','hub_id','hub_user_id'],'hub_app_user_index');
        //    if(!array_key_exists("identifier_index", $indexesFound))
                // $table->index('identifier','identifier_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('app', function (Blueprint $table) {
            $table->dropIndex('hub_app_user_index');
            // $table->dropIndex('identifier_index');
        });
    }
}
