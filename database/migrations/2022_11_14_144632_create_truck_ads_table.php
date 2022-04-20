<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateTruckAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'truck_ads',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->uuid('ad_id')->unique();
                $table->uuid('make_id')->nullable();
                $table->string('custom_make')->nullable();
                $table->string('model');
                $table->string('truck_type');
                $table->uuid('fuel_type_id')->nullable();
                $table->uuid('vehicle_category_id');
                $table->uuid('transmission_type_id')->nullable();

                $table->string('cab')->nullable();
                $table->integer('construction_year')->nullable();
                $table->integer('first_registration_month')->nullable();
                $table->integer('first_registration_year')->nullable();
                $table->integer('inspection_valid_until_month')->nullable();
                $table->integer('inspection_valid_until_year')->nullable();
                $table->integer('owners')->nullable();
                $table->decimal('construction_height_mm', 8, 2)->nullable();
                $table->decimal('lifting_height_mm', 8, 2)->nullable();
                $table->decimal('lifting_capacity_kg_m', 8, 2)->nullable();
                $table->decimal('permanent_total_weight_kg', 8, 2)->nullable();
                $table->decimal('allowed_pulling_weight_kg', 8, 2)->nullable();
                $table->decimal('payload_kg', 8, 2)->nullable();
                $table->decimal('max_weight_allowed_kg', 8, 2)->nullable();
                $table->decimal('empty_weight_kg', 8, 2)->nullable();
                $table->decimal('loading_space_length_mm', 8, 2)->nullable();
                $table->decimal('loading_space_width_mm', 8, 2)->nullable();
                $table->decimal('loading_space_height_mm', 8, 2)->nullable();
                $table->decimal('loading_volume_m3', 8, 2)->nullable();
                $table->decimal('load_capacity_kg', 8, 2)->nullable();
                $table->decimal('operating_weight_kg', 8, 2)->nullable();
                $table->integer('operating_hours', false, true)->nullable();
                $table->integer('axes')->nullable();
                $table->string('wheel_formula')->nullable();
                $table->string('hydraulic_system')->nullable();
                $table->integer('seats')->nullable();
                $table->integer('mileage', false, true);
                $table->integer('power_kw')->nullable();
                $table->string('emission_class')->nullable();
                $table->decimal('fuel_consumption', 6, 2)->nullable();
                $table->decimal('co2_emissions', 6, 2)->nullable();
                $table->string('condition');
                $table->string('interior_color')->nullable();
                $table->string('exterior_color')->nullable();

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
                $table->foreign('vehicle_category_id')
                      ->references('id')
                      ->on('vehicle_categories');
                $table->foreign('fuel_type_id')
                      ->references('id')
                      ->on('car_fuel_types');
                $table->foreign('transmission_type_id')
                      ->references('id')
                      ->on('car_transmission_types');
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
        Schema::dropIfExists('truck_ads');
    }
}
