<?php

use Illuminate\Database\Seeder;
use \App\Models\Website\Setting;

/**
 * Seeds all website settings.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class SettingsSeeder
 */
class SettingsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function run() {
		// battle.net API authentication credentials
		Setting::createModel('battle_net_client_id', 'rhrjd3sjyqukpqfwkqjvw38xanf74mcx');
		Setting::createModel('battle_net_client_secret', 'e7aKnmt3XSkcKBHkWTjfbhDxDhdHjY9p');
	}
}
