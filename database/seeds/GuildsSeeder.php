<?php

use Illuminate\Database\Seeder;
use \App\Models\Guilds\Guild;
use \App\Models\Resources\Realm;

/**
 * Seeds all guilds.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class GuildsSeeder
 */
class GuildsSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function run() {
		// seed the first guild this website is for
		$guild = Guild::createModel(Realm::findBySlug('Thrall'), 'Bootsmeister Tanaris eV');
		// seed all its members of guild was stored
		if ($guild->exists) {
			// iterate through all guild members to update their detailed information
			/** @var \App\Models\Guilds\GuildMember $guildMember */
			foreach($guild->Members as $guildMember) {
				$guildMember->updateFromBattleNet();
			}
		}
	}
}
