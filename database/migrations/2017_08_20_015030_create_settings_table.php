<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Creates the database table to store all website settings.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CreateSettingsTable
 */
class CreateSettingsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function up() {
		Schema::create('settings', function(Blueprint $table) {
			$table->increments('id');
			$table->string('meta_key')->unique();
			$table->string('meta_value')->nullable();
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function down() {
		Schema::drop('settings');
	}
}