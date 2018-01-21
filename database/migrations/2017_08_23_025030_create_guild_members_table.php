<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Creates the database table to store all guild members.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CreateGuildMembersTable
 */
class CreateGuildMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function up() {
		Schema::create('guild_members', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedTinyInteger('guild_id')->index();
			$table->unsignedSmallInteger('realm_id')->index();
			$table->unsignedTinyInteger('character_race_id')->index();
			$table->unsignedTinyInteger('character_class_id')->index();
			$table->unsignedTinyInteger('guild_rank')->index();
			$table->unsignedTinyInteger('gender');
			$table->string('name')->index();
			$table->unsignedSmallInteger('level')->index();
			$table->unsignedInteger('achievement_points')->index();
			$table->string('thumbnail');
			$table->float('item_level_equipped')->nullable();
			$table->float('item_level_total')->nullable();
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
		Schema::drop('guild_members');
	}
}