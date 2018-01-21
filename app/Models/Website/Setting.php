<?php

namespace App\Models\Website;

use App\Models\Model;
use Illuminate\Support\Str;

/**
 * Model representing a single setting of this website.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class Setting
 * @package App\Models\Website
 * @property integer id
 * @property string meta_key
 * @property string|int|float meta_value
 * @property string|int|float value
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 */
class Setting extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'settings';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['meta_key', 'meta_value'];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = [
		'meta_key' => 'string',
		'meta_value' => 'string'
	];

	/**
	 * Wrapper to return the value of this setting.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return float|int|string
	 */
	public function getValueAttribute() {
		return $this->meta_value;
	}

	/**
	 * Updates the value of this settings key.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string|int|float $value The new value of this settings key.
	 * @return bool
	 */
	public function store($value) {
		return $this->fill(['meta_value' => $value])->save();
	}

	/**
	 * Returns the first setting found which matches the specified unique meta key.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $key The unique meta key to find its settings value.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Website\Setting
	 */
	private static function findByKey(string $key) {
		return self::query()->firstOrNew(['meta_key' => Str::lower($key)]);
	}

	/**
	 * Creates or updates the setting identified by the specified unique key.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.
	 * @param string $key The unique settings key to create or update a setting.
	 * @param string|int|float $value The new value of this settings key.
	 * @return \App\Models\Website\Setting|\Illuminate\Database\Eloquent\Model
	 */
	public static function createModel(string $key, $value) {
		// try to load an existing setting
		$setting = self::findByKey($key);
		// update / set its value and save it
		$setting->store($value);
		// return the new / updated settings model
		return $setting;
	}

	/**
	 * Return the battle.net client id setting.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \App\Models\Website\Setting|\Illuminate\Database\Eloquent\Model
	 */
	public static function ApiClientId() {
		return self::findByKey('battle_net_client_id');
	}

	/**
	 * Return the battle.net client secret setting.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \App\Models\Website\Setting|\Illuminate\Database\Eloquent\Model
	 */
	public static function ApiClientSecret() {
		return self::findByKey('battle_net_client_secret');
	}

	/**
	 * Flag if both battle.net API credentials are set or not.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public static function hasApiCredentials() {
		return Str::length(self::ApiClientId()->meta_value) > 0 && Str::length(self::ApiClientSecret()->meta_value) > 0;
	}
}