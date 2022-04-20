<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAutoAdsTable122020 extends Migration
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
               // $table->dropForeign('auto_ads_ad_make_id_foreign');
                //$table->dropForeign('auto_ads_ad_model_id_foreign');
                $table->dropColumn(
                    [
                        'ad_make_id',
                        'ad_model_id',
                        'engine',
                    ]
                );
                $table->uuid('make_id');
                $table->uuid('model_id');
                $table->uuid('generation_id');
                $table->uuid('series_id');
                $table->uuid('trim_id');
                $table->uuid('equipment_id')->nullable();

                /*$table->foreign('make_id')
                      ->references('id')
                      ->on('makes');
                $table->foreign('model_id')
                      ->references('id')
                      ->on('models');
                $table->foreign('generation_id')
                      ->references('id')
                      ->on('generations');
                $table->foreign('series_id')
                      ->references('id')
                      ->on('series');
                $table->foreign('trim_id')
                      ->references('id')
                      ->on('trims');
                $table->foreign('equipment_id')
                      ->references('id')
                      ->on('equipment');*/
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
                /*$table->dropForeign('auto_ads_make_id_foreign');
                $table->dropForeign('auto_ads_model_id_foreign');
                $table->dropForeign('auto_ads_generation_id_foreign');
                $table->dropForeign('auto_ads_series_id_foreign');
                $table->dropForeign('auto_ads_trim_id_foreign');
                $table->dropForeign('auto_ads_equipment_id_foreign');*/
                $table->dropColumn(
                    [
                        'make_id',
                        'model_id',
                        'generation_id',
                        'series_id',
                        'trim_id',
                        'equipment_id',
                    ]
                );
                $table->uuid('ad_make_id');
                $table->uuid('ad_model_id');
                $table->string('engine')->nullable();

                /*$table->foreign('ad_make_id')
                      ->references('id')
                      ->on('ad_makes');
                $table->foreign('ad_model_id')
                      ->references('id')
                      ->on('ad_models');*/
            }
        );
    }
}
