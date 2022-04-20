<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateAdImageVersionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ad_image_versions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ad_image_id');
            $table->string('name');
            $table->string('path');
            $table->timestamps();
    
            /*$table->foreign('ad_image_id')
                  ->references('id')
                  ->on('ad_images')
                  ->onDelete('cascade');*/
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ad_image_versions');
    }
}
