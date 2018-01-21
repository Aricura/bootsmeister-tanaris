<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Creates the database table to store all available character races.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CreateCharacterRacesTable
 */
class CreateCharacterRacesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function up() {
		Schema::create('character_races', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedTinyInteger('fraction_id')->index();
			$table->unsignedInteger('mask')->unique();
			$table->string('name')->index();
			$table->timestamp('created_at')->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function down() {
		Schema::drop('character_races');
	}
}