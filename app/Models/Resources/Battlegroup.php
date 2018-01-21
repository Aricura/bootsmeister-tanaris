<?php

namespace App\Models\Resources;

use App\Models\Api\BattleNet;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Model representing a single battlegroup as combination of multiple realms.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class Battlegroup
 * @package App\Models\Resources
 * @property integer id
 * @property string slug
 * @property string name
 * @property Carbon created_at
 * @property string LocalizedName
 * @property \Illuminate\Database\Eloquent\Collection Realms
 */
class Battlegroup extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'battlegroups';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['slug', 'name', 'created_at'];

	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var bool
	 */
	public $timestamps = false;

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = [
		'slug' => 'string',
		'name' => 'string'
	];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $dates = ['created_at'];

	/**
	 * Relation to get all realms of this battlegroup.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function Realms() {
		return $this->hasMany(Realm::class, 'battlegroup_id', 'id');
	}

	/**
	 * Returns the localized name of this battlegroup.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getLocalizedNameAttribute() {
		// check if a localized version exists
		if (!\Lang::has(sprintf('resources/battlegroups.%s.name', $this->slug), \App::getLocale())) {
			// return the internal name as fallback
			return Str::ucfirst($this->name);
		}
		// return the localized name
		return trans(sprintf('resources/battlegroups.%s.name', $this->slug), [], \App::getLocale());
	}

	/**
	 * Deletes this battlegroup and all connected realms.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool|null
	 */
	public function delete() {
		// delete all realms of this battlegroup
		$this->Realms()->delete();
		// delete this battlegroup
		return parent::delete();
	}

	/**
	 * Returns the unique battlegroup identified by its unique slug.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $slug The unique slug to find a single battlegroup.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Resources\Battlegroup
	 */
	public static function findBySlug(string $slug) {
		return self::query()->firstOrNew(['slug' => Str::lower($slug)]);
	}

	/**
	 * Returns the unique battlegroup identified by its unique name.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $name The unique name to find a single battlegroup.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Resources\Battlegroup
	 */
	public static function findByName(string $name) {
		return self::query()->firstOrNew(['name' => $name]);
	}

	/**
	 * Creates or updates an existing battlegroup.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $slug The unique slug of the new battlegroup.
	 * @param string $name The internal name (english) of the battlegroup.
	 * @return \App\Models\Resources\Battlegroup|\Illuminate\Database\Eloquent\Model
	 */
	public static function createModel(string $slug, string $name) {
		// try to load an existing battlegroup by its unique slug
		$battleGroup = self::findBySlug($slug);
		// update its attributes
		$battleGroup->name = $name;
		// set the current timestamp it this is a new battlegroup and not an updated version
		if (!$battleGroup->exists) {
			$battleGroup->created_at = new Carbon();
		}
		// save all changes
		$battleGroup->save();
		// return the new / updated battlegroup
		return $battleGroup;
	}

	/**
	 * Seeds all battlegroups from the battle.net API.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public static function seed() {
		// create a new connection to the battle.net API
		$battleNet = new BattleNet(false);
		// fetch all battlegroups
		$response = $battleNet->sendRequest('data/battlegroups/');
		// check if battlegroups received
		if (!array_key_exists('battlegroups', $response)) {
			return false;
		}
		// iterate through each battlegroup
		foreach($response['battlegroups'] as $battleGroup) {
			// check if the battlegroup is valid
			if (!is_array($battleGroup) || !array_key_exists('slug', $battleGroup) || !array_key_exists('name', $battleGroup)) {
				continue;
			}
			// store / update the battlegroup
			self::createModel($battleGroup['slug'], $battleGroup['name']);
		}
		// battlegroups are successfully fetched / synchronized
		return true;
	}
}