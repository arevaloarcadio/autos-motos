<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateModelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'models',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('name');
                $table->string('slug')->unique();
                $table->uuid('make_id');
                $table->boolean('is_active')->default(true);
                $table->string('ad_type');
                $table->integer('external_id')->nullable()->unique();
                $table->timestamp('external_updated_at')->nullable();
                $table->timestamps();

               /* $table->foreign('make_id')
                      ->references('id')
                      ->on('makes')
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
        Schema::dropIfExists('models');
    }
}
