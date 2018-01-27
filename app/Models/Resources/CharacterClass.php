<?php

namespace App\Models\Resources;

use App\Models\Api\BattleNet;
use App\Models\Guilds\GuildMember;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Model representing a single character class.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CharacterClass
 * @package App\Models\Resources
 * @property integer id
 * @property integer mask
 * @property string name
 * @property string power_type
 * @property \Carbon\Carbon created_at
 * @property string LocalizedName
 * @property string CssClassName
 * @property string Icon
 * @property \Illuminate\Database\Eloquent\Collection Specs
 * @property \Illuminate\Database\Eloquent\Collection GuildMembers
 */
class CharacterClass extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'character_classes';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['mask', 'name', 'power_type', 'created_at'];

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
		'mask' => 'integer',
		'name' => 'string',
		'power_type' => 'string'
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
	 * Relation to get all specs of this class.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function Specs() {
		return $this->hasMany(CharacterSpec::class, 'character_class_id', 'id');
	}

	/**
	 * Relation to get all guild members which are part of this class.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function GuildMembers() {
		return $this->hasMany(GuildMember::class, 'character_class_id', 'id');
	}

	/**
	 * Returns the localized name of this class.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getLocalizedNameAttribute() {
		// check if a localized version exists
		if (!\Lang::has(sprintf('resources/classes.%s.name', $this->name), \App::getLocale())) {
			// return the internal name as fallback
			return Str::ucfirst($this->name);
		}
		// return the localized name
		return trans(sprintf('resources/classes.%s.name', $this->name), [], \App::getLocale());
	}

	/**
	 * Returns the css class name to style any element in the CI color of this class.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getCssClassNameAttribute() {
		return Str::lower($this->name);
	}

	/**
	 * Returns the absolute file path to the wow class icon of this class.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getIconUrl() {
		return sprintf('/images/icons/classes/%s.png', str_replace(' ', '-', Str::lower($this->name)));
	}

	/**
	 * Deletes this class and all connected specs + guild members.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool|null
	 */
	public function delete() {
		// delete all specs of this class
		$this->Specs()->delete();
		// delete all guild members of this class
		$this->GuildMembers()->delete();
		// delete this character class
		return parent::delete();
	}

	/**
	 * Returns the first class found which matches its unique mask.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param int $mask The unique mask of the race.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Resources\CharacterClass
	 */
	public static function findByMask(int $mask) {
		return self::query()->firstOrNew(['mask' => $mask]);
	}

	/**
	 * Returns the unique class found by name.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $name The unique name of the class.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Resources\CharacterClass
	 */
	public static function findByName(string $name) {
		return self::query()->firstOrNew(['name' => $name]);
	}

	/**
	 * Creates or updates an existing character class.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param int $mask The unique mask of the class.
	 * @param string $name The unique name of the class.
	 * @param string $powerType The power type of this class.
	 * @return \App\Models\Resources\CharacterClass|\Illuminate\Database\Eloquent\Model
	 */
	public static function createModel(int $mask, string $name, string $powerType) {
		// try to load an existing class by its unique mask
		$characterClass = self::findByMask($mask);
		// return the existing class if found by mask
		if ($characterClass->exists) {
			return $characterClass;
		}
		// try to load an existing class by its name
		$characterClass = self::findByName($name);
		// set the unique mask if unknown
		if (!$characterClass->exists) {
			$characterClass->mask = $mask;
			$characterClass->power_type = $powerType;
			$characterClass->created_at = new Carbon();
			$characterClass->save();
		}
		// return the existing / new character class
		return $characterClass;
	}

	/**
	 * Seeds all character classes from the battle.net API.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public static function seed() {
		// create a new connection to the battle.net API
		$battleNet = new BattleNet(false);
		// fetch all classes
		$response = $battleNet->sendRequest('data/character/classes');
		// check if classes received
		if (!array_key_exists('classes', $response)) {
			return false;
		}
		// iterate through each class
		foreach($response['classes'] as $characterClass) {
			// check if the class is valid
			if (!is_array($characterClass) || !array_key_exists('mask', $characterClass) || !array_key_exists('powerType', $characterClass) || !array_key_exists('name', $characterClass)) {
				continue;
			}
			// store / update the class
			self::createModel($characterClass['mask'], $characterClass['name'], $characterClass['powerType']);
		}
		// classes are successfully fetched / synchronized
		return true;
	}
}