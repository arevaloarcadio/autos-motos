<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateAutoAdOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'auto_ad_options',
            function (Blueprint $table) {
                $table->uuid('auto_ad_id');
                $table->uuid('auto_option_id');
                $table->timestamps();
                
                /*$table->foreign('auto_ad_id')
                      ->references('id')
                      ->on('auto_ads')
                      ->onDelete('cascade');
                
                $table->foreign('auto_option_id')
                      ->references('id')
                      ->on('auto_options')
                      ->onDelete('cascade');*/
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
        Schema::dropIfExists('auto_ad_options');
    }
}
