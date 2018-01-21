<?php

namespace App\Http\Controllers;

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
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

	/**
	 * Controller constructor.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public function __construct() {
		// get wow progression of guid
		$client = new Client();
		// send the request and receive the response
		try {
			$response = $client->get("https://www.wowprogress.com/guild/eu/thrall/Bootsmeister+Tanaris+eV/json_rank");
			$rank = json_decode(strval($response->getBody()), true);
		} catch(\Exception $exception) {
			\Log::error($exception->getMessage());
			$rank = [];
		}
		// global variables shared with all views
		\View::share('guildRank', json_decode(json_encode($rank)));
	}

	/**
	 * Home page.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
    public function home() {
    	// build all slides
		$slides = [
			[
				'src' => '/images/background/antorus-heroic-clear.jpg',
				'caption' => 'Bootsmeister Tanaris eV',
				'body' => '15.01.18 Antorus Heroic clear.',
			],
		];

		return view('pages.home', ['slides' => $slides]);
	}

	/**
	 * Team page.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function team() {
		return view('pages.team');
	}

	/**
	 * Progression page.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function progress() {
		return view('pages.progress');
	}
}
