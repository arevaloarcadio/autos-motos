<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacteristicPromotionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characteristic_promotion_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('vehicle_ads');
            $table->integer('shop_ads');
            $table->integer('rental_ads');
            $table->integer('mechanic_ads');
            $table->integer('front_page_promotion');
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
        Schema::dropIfExists('characteristic_promotion_plans');
    }
}
