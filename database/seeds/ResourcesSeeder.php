<?php

use Illuminate\Database\Seeder;
use \App\Models\Resources\Battlegroup;
use \App\Models\Resources\Realm;
use \App\Models\Resources\Fraction;
use \App\Models\Resources\CharacterRace;
use \App\Models\Resources\CharacterClass;
use \App\Models\Resources\CharacterSpec;

/**
 * Seeds all common resources.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class ResourcesSeeder
 */
class ResourcesSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function run() {
		Battlegroup::seed();
		Realm::seed();
		Fraction::seed();
		CharacterRace::seed();
		CharacterClass::seed();
		CharacterSpec::seed();
	}
}
