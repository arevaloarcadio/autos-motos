<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * @author Dragos Becsan <dragos@coolfulsoft.com>
 */
class CreateAdMakesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create(
			'ad_makes',
			function (Blueprint $table) {
				$table->uuid('id')->primary();
				$table->string('name');
				$table->string('slug')->unique();
				$table->string('ad_type');
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
		Schema::dropIfExists('ad_makes');
	}
}
