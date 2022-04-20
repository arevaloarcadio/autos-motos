<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateMotoAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'moto_ads',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('ad_id')->unique();
                $table->uuid('make_id')->nullable();
                $table->string('custom_make')->nullable();
                $table->uuid('model_id')->nullable();
                $table->string('custom_model')->nullable();
                $table->uuid('fuel_type_id');
                $table->uuid('body_type_id');
                $table->uuid('transmission_type_id')->nullable();
                $table->uuid('drive_type_id')->nullable();

                $table->integer('first_registration_month');
                $table->integer('first_registration_year');
                $table->integer('inspection_valid_until_month')->nullable();
                $table->integer('inspection_valid_until_year')->nullable();
                $table->integer('last_customer_service_month')->nullable();
                $table->integer('last_customer_service_year')->nullable();
                $table->integer('owners')->nullable();
                $table->decimal('weight_kg', 8, 2)->nullable();
                $table->integer('engine_displacement')->nullable();
                $table->integer('mileage', false, true);
                $table->integer('power_kw')->nullable();
                $table->integer('gears')->nullable();
                $table->integer('cylinders')->nullable();
                $table->string('emission_class')->nullable();
                $table->decimal('fuel_consumption', 6, 2)->nullable();
                $table->decimal('co2_emissions', 6, 2)->nullable();
                $table->string('condition');
                $table->string('color');

                $table->decimal('price', 10, 2, true);
                $table->boolean('price_contains_vat')->default(false);

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
                $table->string('youtube_link')->nullable();
                $table->timestamps();

               /* $table->foreign('ad_id')
                      ->references('id')
                      ->on('ads')
                      ->onDelete('cascade');
                $table->foreign('make_id')
                      ->references('id')
                      ->on('makes');
                $table->foreign('model_id')
                      ->references('id')
                      ->on('makes');
                $table->foreign('fuel_type_id')
                      ->references('id')
                      ->on('car_fuel_types');
                $table->foreign('body_type_id')
                      ->references('id')
                      ->on('car_body_types');
                $table->foreign('transmission_type_id')
                      ->references('id')
                      ->on('car_transmission_types');
                $table->foreign('drive_type_id')
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
        Schema::dropIfExists('moto_ads');
    }
}
