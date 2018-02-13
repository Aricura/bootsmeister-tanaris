<?php

namespace App\Console\Commands;

use App\Models\Guilds\Guild;
use Illuminate\Console\Command;

/**
 * Updates all guild information and their members.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class GuildUpdater
 * @package App\Console\Commands
 */
class GuildUpdater extends Command {

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'guild:updater';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Updates all guild information and their members.';

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
		/** @var Guild $guild */
		foreach(Guild::all() as $guild) {
			$this->info(sprintf('%s ...', $guild->name));

			$guild->updateFromBattleNet();
			$this->comment(' - Guild Data updated.');

			$guild->seedGuildMembers();
			$this->comment(' - Guild Members seeded.');

			/** @var \App\Models\Guilds\GuildMember $guildMember */
			foreach($guild->Members as $guildMember) {
				$guildMember->updateFromBattleNet();
				$this->comment(sprintf('   - %s updated.', $guildMember->name));
			}

			$this->info('done.');
		}
	}
}
