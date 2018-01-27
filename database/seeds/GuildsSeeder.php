<?php

use Illuminate\Database\Seeder;
use \App\Models\Guilds\Guild;
use \App\Models\Resources\Realm;
use \App\Models\Resources\CharacterClass;
use \App\Models\Resources\CharacterSpec;

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


		// seed all raid members and their raid spec
		$raidMembers = [
			'Discostue' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Druid'), 'Guardian'),

			'Tenim' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Demon Hunter'), 'Havoc'),
			'Elcø' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Shaman'), 'Enhancement'),
			'Furyisimba' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Warrior'), 'Fury'),
			'Fenragon' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Paladin'), 'Retribution'),
			'Plutós' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Paladin'), 'Retribution'),

			'Wombocombo' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Priest'), 'Shadow'),
			'Théws' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Mage'), 'Fire'),
			'Yawsz' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Warlock'), 'Affliction'),

			'Aricura' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Druid'), 'Restoration'),
			'Destrower' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Druid'), 'Restoration'),
			'Linthandril' => CharacterSpec::findByClassAndName(CharacterClass::findByName('Monk'), 'Mistweaver'),
		];

		// loop through all raid members and add them
		foreach($raidMembers as $name => $spec) {
			\App\Models\Guilds\GuildMember::findByGuildAndName($guild, $name)->markAsRaidMember($spec);
		}
	}
}
