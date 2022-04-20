<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateAdModelsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(
			'ad_models',
			function (Blueprint $table) {
				$table->uuid('id')->primary();
				$table->string('name');
				$table->string('slug')->unique();
				$table->string('ad_type');
				$table->uuid('parent_id')->nullable();
				$table->uuid('ad_make_id');
				$table->timestamps();
				
				/*$table->foreign('ad_make_id')
				      ->references('id')
				      ->on('ad_makes');*/
			}
		);
		
		Schema::table(
			'ad_models',
			function (Blueprint $table) {
				$table->foreign('parent_id')
				      ->references('id')
				      ->on('ad_models');
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
		Schema::dropIfExists('ad_models');
	}
}
