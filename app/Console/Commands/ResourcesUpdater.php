<?php

namespace App\Console\Commands;

use App\Models\Resources\Battlegroup;
use App\Models\Resources\CharacterClass;
use App\Models\Resources\CharacterRace;
use App\Models\Resources\CharacterSpec;
use App\Models\Resources\Fraction;
use App\Models\Resources\Realm;
use Illuminate\Console\Command;

/**
 * Updates all generic World of Warcraft resources.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class ResourcesUpdater
 * @package App\Console\Commands
 */
class ResourcesUpdater extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'resources:updater';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates all generic World of Warcraft resources.';

	/**
	 * Create a new command instance.
	 */
	public function __construct() {
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 */
	public function handle() {
		Battlegroup::seed();
		$this->info('Battlegroups done.');

		Realm::seed();
		$this->info('Realms done.');

		Fraction::seed();
		$this->info('Fractions done.');

		CharacterRace::seed();
		$this->info('Character Races done.');

		CharacterClass::seed();
		$this->info('Character Classes done.');

		CharacterSpec::seed();
		$this->info('Character Specs done.');
	}
}
