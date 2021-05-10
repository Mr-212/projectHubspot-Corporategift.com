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
            $table->foreign('app_id')->references("id")->on('app')->cascadeOnUpdate()->nullOnDelete();
        });

        Schema::table('gift_orders', function (Blueprint $table) {
                $table->foreign('app_id')->references("id")->on('app')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::disableForeignKeyConstraints();

        Schema::table('users', function (Blueprint $table) {
           if (Schema::hasColumn('app', 'app_id')){
                $table->dropForeign('app_id');
                $table->dropColumn('app_id');

             }  
        });

        Schema::table('gift_orders', function (Blueprint $table) {
             if(Schema::hasColumn('app', 'app_id')){

                $table->dropForeign('app_id');
                $table->dropIndex('app_object_type_index');
                $table->dropColumn('app_id');
             }
        });
        Schema::enableForeignKeyConstraints();

    }
}
