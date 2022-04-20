<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAutoAdsTable23122020 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(
            'auto_ads',
            function (Blueprint $table) {
                $table->uuid('generation_id')->nullable()->change();
                $table->uuid('series_id')->nullable()->change();
                $table->uuid('trim_id')->nullable()->change();
                $table->uuid('ad_fuel_type_id')->nullable()->change();
                $table->string('additional_vehicle_info')->nullable();
                $table->integer('seats')->nullable();
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(
            'auto_ads',
            function (Blueprint $table) {
                $table->uuid('generation_id')->nullable(false)->change();
                $table->uuid('series_id')->nullable(false)->change();
                $table->uuid('trim_id')->nullable(false)->change();
                $table->uuid('ad_fuel_type_id')->nullable(false)->change();
                $table->dropColumn('additional_vehicle_info');
                $table->dropColumn('seats');
            }
        );
    }
}
