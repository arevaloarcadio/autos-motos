<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateCarFuelTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(
            'car_fuel_types',
            function (Blueprint $table) {
                $table->uuid('id')->primary();
                $table->string('internal_name')->unique();
                $table->string('slug')->unique();
                $table->string('external_name')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('car_fuel_types');
    }
}
