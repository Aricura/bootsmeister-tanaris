<?php

namespace App\Console\Commands;

use App\Models\Guilds\GuildMember;
use App\Models\Resources\Realm;
use Illuminate\Console\Command;

/**
 * Removes the given guild member from the raid roster.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class MemberRemove
 * @package App\Console\Commands
 */
class MemberRemove extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'member:remove {realm} {name}';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Removes a given guild member from the raid roster.';

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

		$member->removeAsRaidMember();
		$this->info('done.');
	}
}
