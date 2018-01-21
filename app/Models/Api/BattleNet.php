<?php

namespace App\Models\Api;

use App\Models\Website\Setting;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Str;

/**
 * Battle.net API wrapper.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class BattleNet
 * @package App\Models\Api
 */
class BattleNet {

	/**
	 * Stores the OAuth access token.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 * @see https://dev.battle.net/docs/read/oauth#ClientCredentialsFlow
	 */
	protected $oAuthAccessToken = '';

	/**
	 * BattleNetApi constructor.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param bool $useOAuth Flag if the access token should be loaded automatically.
	 */
	public function __construct(bool $useOAuth) {
		// check if the access token should be automatically loaded or not
		if (!$useOAuth) {
			// do not autoload the access token
			return;
		}
		// try to fetch the latest access token from the cache
		if ($this->getAccessTokenFromCache()) {
			// access token received from cache
			return;
		}
		// fetch a new access token from the API
		if ($this->requestNewAccessToken()) {
			// new access token received from api
			return;
		}
		// invalid access token
	}

	/**
	 * Restores the latest OAuth access token from the cache.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	private function getAccessTokenFromCache() {
		// read the latest access token from the cache and update the property
		$this->oAuthAccessToken = trim(\Cache::get('bnet-oauth-access-token', ''));
		// checks if the access token was successfully read
		return Str::length($this->oAuthAccessToken) > 0;
	}

	/**
	 * Request a new OAuth access token from the battle.net OAuth API.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public function requestNewAccessToken() {
		// reset the current access token
		$this->oAuthAccessToken = '';
		// build the url to request a new access token
		$url = sprintf('https://eu.battle.net/oauth/token?grant_type=client_credentials&client_id=%s&client_secret=%s',
			Setting::ApiClientId()->value, Setting::ApiClientSecret()->value);
		// send the request and read its successful response
		$response = $this->sendOAuthRequest($url);
		// check if the response contains an access token
		if (!array_key_exists('access_token', $response)) {
			return false;
		}
		// update the access token property
		$this->oAuthAccessToken = trim($response['access_token']);
		// set the expiration day to 30 days from now
		\Cache::put('bnet-oauth-access-token', $this->oAuthAccessToken, Carbon::now()->addDays(30));
		return true;
	}

	/**
	 * Sends a custom GET request to the specified url and returns its response as array.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $url Custom url to return its response as array on success.
	 * @return array
	 */
	public function sendOAuthRequest($url) {
		// check if the url has already some GET parameters
		if (stripos($url, '?') !== false) {
			$url .= '&';
		} else {
			$url .= '?';
		}
		// append the locale and access token
		$url .= sprintf('locale=en_GB&access_token=%s', $this->oAuthAccessToken);
		// create a new guzzle connection to the foreign api
		$client = new Client();
		// send the request and receive the response
		try {
			$response = $client->get($url);
		} catch(\Exception $exception) {
			\Log::error($exception->getMessage());
			return [];
		}
		// verify if the request was successful
		if (intval($response->getStatusCode()) !== 200) {
			\Log::error(strval($response->getBody()));
			return [];
		}
		// try to convert the response to a JSON array
		try {
			$responseArray = json_decode(strval($response->getBody()), true);
			return is_array($responseArray) ? $responseArray : [];
		} catch (\Exception $exception) {
			\Log::error($exception->getMessage());
			return [];
		}
	}

	/**
	 * Sends a custom GET request to the specified battle.net API path and returns its response as array.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $requestPath The relative path to the api.
	 * @param array $getParams Optional collection of additional get parameters.
	 * @return array
	 */
	public function sendRequest($requestPath, array $getParams = []) {
		// build the absolute url to the api
		$url = sprintf('https://eu.api.battle.net/wow/%s?locale=en_GB&apikey=%s', $requestPath, Setting::ApiClientId()->value);
		// append additional get parameters if set
		if (count($getParams) > 0) {
			foreach($getParams as $key => $value) {
				$url .= sprintf('&%s=%s', trim($key), trim($value));
			}
		}
		// create a new guzzle connection to the foreign api
		$client = new Client();
		// send the request and receive the response
		try {
			$response = $client->get($url);
		} catch(\Exception $exception) {
			\Log::error($exception->getMessage());
			return [];
		}
		// verify if the request was successful
		if (intval($response->getStatusCode()) !== 200) {
			\Log::error(strval($response->getBody()));
			return [];
		}
		// try to convert the response to a JSON array
		try {
			$responseArray = json_decode(strval($response->getBody()), true);
			return is_array($responseArray) ? $responseArray : [];
		} catch (\Exception $exception) {
			\Log::error($exception->getMessage());
			return [];
		}
	}
}
