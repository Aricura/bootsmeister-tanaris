<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Creates the database table to store all specs of all character classes.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CreateCharacterSpecsTable
 */
class CreateCharacterSpecsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function up() {
		Schema::create('character_specs', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedTinyInteger('character_class_id')->index();
			$table->string('name')->index();
			$table->enum('role', ['tank', 'melee', 'range', 'heal']);
			$table->string('background_image');
			$table->string('icon');
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
		Schema::drop('character_specs');
	}
}