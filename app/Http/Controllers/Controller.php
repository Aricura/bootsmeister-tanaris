<?php

namespace App\Http\Controllers;

use App\Models\Guilds\RaidMember;
use GuzzleHttp\Client;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @author Stefan Herndler
 * @since 1.0.0
 * @class Controller
 * @package App\Http\Controllers
 */
class Controller extends BaseController {

	use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Controller constructor.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function __construct() {
		// get the current wow progress ranking form the server's cache
		$rank = (array)\Cache::get('wow-progress', []);

		// check if the cache is still valid
		if (!\count($rank)) {
			// create the base url for this guild
			$baseUrl = \sprintf(
				'https://www.wowprogress.com/guild/eu/thrall/%s',
				\str_replace(' ', '+', config('app.name'))
			);

			// fetch the new wow progress rank and update the cache
			$client = new Client();

			// send the request and receive the response
			try {
				$rank = json_decode(strval($client->get($baseUrl . '/json_rank')->getBody()), true);
				// append the base url to the rank
				$rank['base_url'] = $baseUrl;
			} catch (\Exception $exception) {
				\Log::error($exception->getMessage());
				$rank = [];
			}

			// update the cache
			\Cache::put('wow-progress', $rank);
		}

		// global variables shared with all views
		\View::share('guildRank', $rank);
	}

	/**
	 * Home page.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function home() {
		// fetch all raid members sorted by their role in raid
		$raidMemberCollection = [
			'tank' => RaidMember::getByRole('tank'),
			'melee' => RaidMember::getByRole('melee'),
			'range' => RaidMember::getByRole('range'),
			'heal' => RaidMember::getByRole('heal'),
		];

		// return the one pager view
		return view('app', [
			'raidMemberCollection' => $raidMemberCollection,
		]);
	}
}
