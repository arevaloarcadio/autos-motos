<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateAutoAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'auto_ads',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('ad_id')->unique();
                $table->decimal('price', 10, 2, true);
                $table->uuid('car_make_id');
                $table->uuid('car_model_id');
                $table->uuid('car_generation_id');
                $table->uuid('car_spec_id');
                $table->uuid('car_fuel_type_id');
                $table->uuid('car_body_type_id');
                $table->uuid('car_transmission_type_id');
                $table->uuid('car_wheel_drive_type_id');
                $table->year('production_year');
                $table->string('vin')->nullable();
                $table->integer('doors')->nullable();
                $table->integer('mileage', false, true);
                $table->string('exterior_color');
                $table->string('interior_color');
                $table->string('condition');
                $table->uuid('dealer_id')->nullable();
                $table->uuid('dealer_show_room_id')->nullable();
                $table->string('first_name')->nullable();
                $table->string('last_name')->nullable();
                $table->string('email_address');
                $table->text('address');
                $table->string('zip_code');
                $table->string('city');
                $table->string('country');
                $table->string('mobile_number')->nullable();
                $table->string('landline_number')->nullable();
                $table->string('whatsapp_number')->nullable();
                $table->timestamps();

                /*$table->foreign('ad_id')
                      ->references('id')
                      ->on('ads')
                      ->onDelete('cascade');
                $table->foreign('car_make_id')
                      ->references('id')
                      ->on('car_makes');
                $table->foreign('car_model_id')
                      ->references('id')
                      ->on('car_models');
                $table->foreign('car_generation_id')
                      ->references('id')
                      ->on('car_generations');
                $table->foreign('car_spec_id')
                      ->references('id')
                      ->on('car_specs');
                $table->foreign('car_fuel_type_id')
                      ->references('id')
                      ->on('car_fuel_types');
                $table->foreign('car_body_type_id')
                      ->references('id')
                      ->on('car_body_types');
                $table->foreign('car_transmission_type_id')
                      ->references('id')
                      ->on('car_transmission_types');
                $table->foreign('car_wheel_drive_type_id')
                      ->references('id')
                      ->on('car_wheel_drive_types');
                $table->foreign('dealer_id')
                      ->references('id')
                      ->on('dealers');
                $table->foreign('dealer_show_room_id')
                      ->references('id')
                      ->on('dealer_show_rooms');*/
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
        Schema::dropIfExists('auto_ads');
    }
}
