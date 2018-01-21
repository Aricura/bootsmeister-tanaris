<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Creates the database table to store all available character classes.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CreateCharacterClassesTable
 */
class CreateCharacterClassesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function up() {
		Schema::create('character_classes', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('mask')->unique();
			$table->string('name')->unique();
			$table->string('power_type');
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
		Schema::drop('character_classes');
	}
}