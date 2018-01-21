<?php

namespace App\Models\Resources;

use App\Models\Api\BattleNet;
use App\Models\Guilds\Guild;
use App\Models\Guilds\GuildMember;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Model representing a single realm as part of a battlegroup.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class Realm
 * @package App\Models\Resources
 * @property integer id
 * @property integer battlegroup_id
 * @property string slug
 * @property string name
 * @property string type
 * @property string population
 * @property string locale
 * @property Carbon created_at
 * @property string LocalizedName
 * @property \App\Models\Resources\Battlegroup Battlegroup
 * @property \Illuminate\Database\Eloquent\Collection Guilds
 * @property \Illuminate\Database\Eloquent\Collection GuildMembers
 */
class Realm extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'realms';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['battlegroup_id', 'slug', 'name', 'type', 'population', 'locale', 'created_at'];

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
		'battlegroup_id' => 'integer',
		'slug' => 'string',
		'name' => 'string',
		'type' => 'string',
		'population' => 'string',
		'locale' => 'string'
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
	 * Relation to get the parent battlegroup this realm belongs to.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function Battlegroup() {
		return $this->belongsTo(Battlegroup::class, 'battlegroup_id', 'id');
	}

	/**
	 * Relation to get all guilds of this realm.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function Guilds() {
		return $this->hasMany(Guild::class, 'realm_id', 'id');
	}

	/**
	 * Collection to get all guild members of this realm.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function GuildMembers() {
		return $this->hasMany(GuildMember::class, 'realm_id', 'id');
	}

	/**
	 * Returns the localized name of this realm.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getLocalizedNameAttribute() {
		// check if a localized version exists
		if (!\Lang::has(sprintf('resources/realms.%s.name', $this->slug), \App::getLocale())) {
			// return the internal name as fallback
			return Str::ucfirst($this->name);
		}
		// return the localized name
		return trans(sprintf('resources/realms.%s.name', $this->slug), [], \App::getLocale());
	}

	/**
	 * Deletes this realm and all connected guilds and guild members.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool|null
	 */
	public function delete() {
		// delete all guilds of this realm
		$this->Guilds()->delete();
		// delete all guild members of this realm
		$this->GuildMembers()->delete();
		// delete this realm
		return parent::delete();
	}

	/**
	 * Returns the unique realm identified by its unique slug.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $slug The unique slug to find a single realm.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Resources\Realm
	 */
	public static function findBySlug(string $slug) {
		return self::query()->firstOrNew(['slug' => Str::lower($slug)]);
	}

	/**
	 * Returns the realm identified by its name.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $name The name to find a single realm.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Resources\Realm
	 */
	public static function findByName(string $name) {
		return self::query()->firstOrNew(['name' => $name]);
	}

	/**
	 * Creates or updates an existing realm.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param Battlegroup $battleGroup The parent battlegroup of this realm.
	 * @param string $slug The unique slug of the new realm.
	 * @param string $name The internal name (english) of the realm.
	 * @param string $type The type of this realm (pve, pvp, rp, ...).
	 * @param string $population The population index (low, medium, high).
	 * @param string $locale The localization of this realm (english, german, french, ...).
	 * @return \App\Models\Resources\Realm|\Illuminate\Database\Eloquent\Model
	 */
	public static function createModel(Battlegroup $battleGroup, string $slug, string $name, string $type, string $population, string $locale) {
		// try to load an existing realm by its unique slug
		$realm = self::findBySlug($slug);
		// update its attributes
		$realm->battlegroup_id = $battleGroup->getKey();
		$realm->name = $name;
		$realm->type = $type;
		$realm->population = $population;
		$realm->locale = $locale;
		// set the current timestamp it this is a new realm and not an updated version
		if (!$realm->exists) {
			$realm->created_at = new Carbon();
		}
		// save all changes
		$realm->save();
		// return the new / updated realm
		return $realm;
	}

	/**
	 * Seeds all realms from the battle.net API.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public static function seed() {
		// create a new connection to the battle.net API
		$battleNet = new BattleNet(false);
		// fetch all realms
		$response = $battleNet->sendRequest('realm/status');
		// check if realms received
		if (!array_key_exists('realms', $response)) {
			return false;
		}
		// iterate through each realm
		foreach($response['realms'] as $realm) {
			// check if the realm is valid
			if (!is_array($realm) || !array_key_exists('battlegroup', $realm) || !array_key_exists('slug', $realm) || !array_key_exists('name', $realm) || !array_key_exists('type', $realm) || !array_key_exists('population', $realm) || !array_key_exists('locale', $realm)) {
				continue;
			}
			// try to load the parent battlegroup by its unique slug
			$battleGroup = Battlegroup::findBySlug($realm['battlegroup']);
			// check if the battlegroup is invalid
			if (!$battleGroup->exists) {
				// try to load the parent battlegroup by its unique name
				$battleGroup = Battlegroup::findByName($realm['battlegroup']);
				// check if the battlegroup is still invalid
				if (!$battleGroup->exists) {
					continue;
				}
			}
			// store / update the realm
			self::createModel($battleGroup, $realm['slug'], $realm['name'], $realm['type'], $realm['population'], $realm['locale']);
		}
		// realms are successfully fetched / synchronized
		return true;
	}
}