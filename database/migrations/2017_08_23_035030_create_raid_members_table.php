<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Creates the database table to store all guild members which are part of the core raid team.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CreateRaidMembersTable
 */
class CreateRaidMembersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function up() {
		Schema::create('raid_members', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('guild_member_id')->unique();
			$table->unsignedSmallInteger('character_spec_id')->index();
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
		Schema::drop('raid_members');
	}
}