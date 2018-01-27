<?php

namespace App\Http\Controllers;

use App\Models\Guilds\RaidMember;
use App\Models\Resources\CharacterSpec;
use GuzzleHttp\Client;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

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


		// create a collection of all wheel items
		$wheelItems = [
			[
				'title' => 'Story',
				'body' => $this->getWheelBody('1'),
			],
			[
				'title' => 'Community',
				'body' => $this->getWheelBody('2'),
			],
			[
				'title' => 'Progress',
				'body' => $this->getWheelBody('3'),
			],
			[
				'title' => 'Abseits von Raids',
				'body' => $this->getWheelBody('4'),
			],
			[
				'title' => 'Epilog',
				'body' => $this->getWheelBody('5'),
			],
		];

		// return the one pager view
		return view('app', [
			'raidMemberCollection' => $raidMemberCollection,
			'wheelItems' => $wheelItems
		]);
	}

	/**
	 * Returns the file content of a wheel blade file.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $name
	 * @return bool|string
	 */
	protected function getWheelBody(string $name) {
		return view(sprintf('one-pager.wheels.%s', $name));
	}

	/**
	 * Receives a recruitment request and sends a mail to the guild officers.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param Request $request
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function recruitment(Request $request) {
		// create the validation rules
		$rules = [
			'bnet' => "required|string|min:3|max:50",
			'armory' => "required|string|min:30|max:70",
			'spec' => "required|in:character_specs,id",
			'exp' => "required|string|min:10|max:1000"
		];

		// create the validator and check if everything is fine
		$validator = $this->getValidationFactory()->make($request->all(), $rules);
		if ($validator->fails()) {
			return redirect()->to('/#recruitment')->withInput($request->all())->withErrors($validator->getMessageBag());
		}

		// get all data from the request
		$data = $request->except('token');
		// resolve and append the character spec
		$data['spec'] = CharacterSpec::query()->find($data['spec']);


			// send an email to the guild officers as the recruitment request was successful
		\Mail::send("emails.recruitment", $data, function($message) {
			/** @var \Illuminate\Mail\Message $message */
			$message
				->subject("Bootsmeister Tanaris eV Recruitment")
				->to("admin@aricura.com", "Stefan Herndler");
		});

		// check for errors
		if (count(\Mail::failures()) > 0) {
			// log them
			\Log::error("SEND RECRUITMENT MAIL: " . implode(" ", \Mail::failures()));
		}

		// return back as everything was successful and print a message
		return redirect()->to('/#recruitment')->withInput($request->all())->with('recruitment_successful', true);
	}
}
