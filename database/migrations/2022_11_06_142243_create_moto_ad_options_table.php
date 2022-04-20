<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateMotoAdOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'moto_ad_options',
            function (Blueprint $table) {
                $table->uuid('moto_ad_id');
                $table->uuid('option_id');
                $table->timestamps();

               /* $table->foreign('moto_ad_id')
                      ->references('id')
                      ->on('moto_ads')
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
        Schema::dropIfExists('moto_ad_options');
    }
}
