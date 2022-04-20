<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateTruckAdOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'truck_ad_options',
            function (Blueprint $table) {
                $table->uuid('truck_ad_id');
                $table->uuid('option_id');
                $table->timestamps();

                /*$table->foreign('truck_ad_id')
                      ->references('id')
                      ->on('truck_ads')
                      ->onDelete('cascade');

                $table->foreign('option_id')
                      ->references('id')
                      ->on('auto_options');*/
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
        Schema::dropIfExists('truck_ad_options');
    }
}
