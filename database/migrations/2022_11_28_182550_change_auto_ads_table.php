<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class ChangeAutoAdsTable extends Migration
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
               /* $table->dropForeign('auto_ads_car_make_id_foreign');
                $table->dropForeign('auto_ads_car_model_id_foreign');
                $table->dropForeign('auto_ads_car_generation_id_foreign');
                $table->dropForeign('auto_ads_car_spec_id_foreign');
                $table->dropForeign('auto_ads_car_body_type_id_foreign');
                $table->dropForeign('auto_ads_car_fuel_type_id_foreign');
                $table->dropForeign('auto_ads_car_transmission_type_id_foreign');
                $table->dropForeign('auto_ads_car_wheel_drive_type_id_foreign');*/
                $table->dropColumn(
                    [
                        'car_make_id',
                        'car_model_id',
                        'car_generation_id',
                        'car_spec_id',
                        'car_body_type_id',
                        'car_fuel_type_id',
                        'car_transmission_type_id',
                        'car_wheel_drive_type_id',
                        'production_year'
                    ]
                );
                $table->uuid('ad_make_id');
                $table->uuid('ad_model_id');
                $table->uuid('ad_fuel_type_id');
                $table->uuid('ad_body_type_id');
                $table->uuid('ad_transmission_type_id');
                $table->uuid('ad_drive_type_id');
                $table->integer('first_registration_month');
                $table->integer('first_registration_year');
                $table->string('engine')->nullable();
                $table->integer('engine_displacement')->nullable();
                $table->integer('power_hp')->nullable();
                $table->integer('owners')->nullable();
                $table->integer('inspection_valid_until_month')->nullable();
                $table->integer('inspection_valid_until_year')->nullable();

                /*$table->foreign('ad_make_id')
                      ->references('id')
                      ->on('ad_makes');
                $table->foreign('ad_model_id')
                      ->references('id')
                      ->on('ad_models');
                $table->foreign('ad_fuel_type_id')
                      ->references('id')
                      ->on('car_fuel_types');
                $table->foreign('ad_body_type_id')
                      ->references('id')
                      ->on('car_body_types');
                $table->foreign('ad_transmission_type_id')
                      ->references('id')
                      ->on('car_transmission_types');
                $table->foreign('ad_drive_type_id')
                      ->references('id')
                      ->on('car_wheel_drive_types');*/
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
        //
    }
}
