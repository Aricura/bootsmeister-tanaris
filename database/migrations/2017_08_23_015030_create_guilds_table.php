<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Creates the database table to store all guilds this website is for.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CreateGuildsTable
 */
class CreateGuildsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function up() {
		Schema::create('guilds', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedSmallInteger('realm_id')->index();
			$table->unsignedTinyInteger('fraction_id')->index();
			$table->string('name')->index();
			$table->unsignedTinyInteger('level');
			$table->unsignedInteger('achievement_points');
			$table->timestamp('last_modified')->nullable();
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
		Schema::drop('guilds');
	}
}