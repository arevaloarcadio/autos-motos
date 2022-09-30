<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameColumnColorInMobileHomeAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mobile_home_ads', function (Blueprint $table) {
            $table->renameColumn('color_exterior', 'exterior_color');
            $table->renameColumn('color_interior', 'interior_color');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mobile_home_ads', function (Blueprint $table) {
            //
        });
    }
}
