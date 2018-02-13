<?php

namespace App\Console\Commands;

use App\Models\Guilds\GuildMember;
use App\Models\Resources\CharacterSpec;
use App\Models\Resources\Realm;
use Illuminate\Console\Command;

/**
 * Adds a given guild member to the raid roster.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class MemberAdd
 * @package App\Console\Commands
 */
class MemberAdd extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'member:add {realm} {name} {spec}';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Adds a given guild member to the raid roster.';

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
		$realm = Realm::findByName($this->argument('realm'));
		if (null === $realm || !$realm->exists) {
			$this->warn('Realm unknown.');
			return;
		}

		$member = GuildMember::findByRealmAndName($realm, $this->argument('name'));
		if (null === $member || !$member->exists) {
			$this->warn('Member unknown.');
			return;
		}

		$spec = CharacterSpec::findByClassAndName($member->CharacterClass, $this->argument('spec'));
		if (null === $spec || !$spec->exists) {
			$this->warn('Spec unknown.');
			return;
		}

		$member->markAsRaidMember($spec);
		$this->info('done.');
	}
}
