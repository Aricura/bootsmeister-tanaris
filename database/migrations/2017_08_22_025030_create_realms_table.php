<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Creates the database table to store all realms of all battlegroups.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CreateRealmsTable
 */
class CreateRealmsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function up() {
		Schema::create('realms', function(Blueprint $table) {
			$table->increments('id');
			$table->unsignedSmallInteger('battlegroup_id')->index();
			$table->string('slug')->unique();
			$table->string('name')->index();
			$table->string('type');
			$table->string('population');
			$table->string('locale');
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
		Schema::drop('realms');
	}
}