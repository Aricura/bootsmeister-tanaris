<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Creates the database table to store all battlegroups.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CreateBattlegroupsTable
 */
class CreateBattlegroupsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function up() {
		Schema::create('battlegroups', function(Blueprint $table) {
			$table->increments('id');
			$table->string('slug')->unique();
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
		Schema::drop('battlegroups');
	}
}