<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateAdsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'ads',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('slug')->unique();
                $table->string('title');
                $table->text('description');
                $table->integer('status')->default(0);
                $table->string('type');
                $table->boolean('is_featured')->default(false);
                $table->uuid('user_id');
                $table->uuid('market_id');
                $table->timestamps();
                
                /*$table->foreign('user_id')
                      ->references('id')
                      ->on('users');
                $table->foreign('market_id')
                      ->references('id')
                      ->on('markets');*/
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
        Schema::dropIfExists('ads');
    }
}
