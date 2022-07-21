<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacteristicPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characteristic_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('vehicle_ads');
            $table->integer('rental_ads');
            $table->integer('promotion_month');
            $table->integer('front_page_promotion');
            $table->integer('video_a_day');
            $table->integer('mechanics_rental_ads');
            $table->uuid('plan_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('characteristic_plans');
    }
}
