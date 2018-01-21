<?php

namespace App\Models\Resources;

use App\Models\Api\BattleNet;
use App\Models\Guilds\GuildMember;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Model representing a single character race.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CharacterRace
 * @package App\Models\Resources
 * @property integer id
 * @property integer fraction_id
 * @property integer mask
 * @property string name
 * @property \Carbon\Carbon created_at
 * @property string LocalizedName
 * @property \App\Models\Resources\Fraction Fraction
 * @property \Illuminate\Database\Eloquent\Collection GuildMembers
 */
class CharacterRace extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'character_races';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['fraction_id', 'mask', 'name', 'created_at'];

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
		'fraction_id' => 'integer',
		'mask' => 'integer',
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
	 * Relation to get the fraction this race belongs to.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function Fraction() {
		return $this->belongsTo(Fraction::class, 'fraction_id', 'id');
	}

	/**
	 * Relation to get the collection of all guild members of this race.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function GuildMembers() {
		return $this->hasMany(GuildMember::class, 'character_race_id', 'id');
	}

	/**
	 * Returns the localized name of this race.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getLocalizedNameAttribute() {
		// check if a localized version exists
		if (!\Lang::has(sprintf('resources/races.%s.name', $this->name), \App::getLocale())) {
			// return the internal name as fallback
			return Str::ucfirst($this->name);
		}
		// return the localized name
		return trans(sprintf('resources/races.%s.name', $this->name), [], \App::getLocale());
	}

	/**
	 * Deletes this race and all connected guild members.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool|null
	 */
	public function delete() {
		// delete all guild members of this race
		$this->GuildMembers()->delete();
		// delete this character race
		return parent::delete();
	}

	/**
	 * Returns the first race found which matches its unique mask.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param int $mask The unique mask of the race.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Resources\CharacterRace
	 */
	public static function findByMask(int $mask) {
		return self::query()->firstOrNew(['mask' => $mask]);
	}

	/**
	 * Returns the unique race found by name within the specified fraction.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Resources\Fraction $fraction The fraction to find its unique race within it.
	 * @param string $name The unique name of the race within the specified fraction.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Resources\CharacterRace
	 */
	public static function findByFractionAndName(Fraction $fraction, string $name) {
		return self::query()->firstOrNew(['fraction_id' => $fraction->getKey(), 'name' => $name]);
	}

	/**
	 * Creates or updates an existing character race.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Resources\Fraction $fraction The fraction this race belongs to.
	 * @param int $mask The unique mask of the race.
	 * @param string $name The name of the race which is unique within the fraction.
	 * @return \App\Models\Resources\CharacterRace|\Illuminate\Database\Eloquent\Model
	 */
	public static function createModel(Fraction $fraction, int $mask, string $name) {
		// try to load an existing race by its unique mask
		$characterRace = self::findByMask($mask);
		// return the existing race if found by mask
		if ($characterRace->exists) {
			return $characterRace;
		}
		// try to load an existing race by its fraction + name
		$characterRace = self::findByFractionAndName($fraction, $name);
		// set the unique mask if unknown
		if (!$characterRace->exists) {
			$characterRace->mask = $mask;
			$characterRace->created_at = new Carbon();
			$characterRace->save();
		}
		// return the existing / new character race
		return $characterRace;
	}

	/**
	 * Seeds all character races from the battle.net API.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public static function seed() {
		// create a new connection to the battle.net API
		$battleNet = new BattleNet(false);
		// fetch all races
		$response = $battleNet->sendRequest('data/character/races');
		// check if races received
		if (!array_key_exists('races', $response)) {
			return false;
		}
		// iterate through each race
		foreach($response['races'] as $characterRace) {
			// check if the race is valid
			if (!is_array($characterRace) || !array_key_exists('mask', $characterRace) || !array_key_exists('side', $characterRace) || !array_key_exists('name', $characterRace)) {
				continue;
			}
			// try to load the parent fraction by its unique name/side
			$fraction = Fraction::findBySlug($characterRace['side']);
			// check if the fraction is invalid
			if (!$fraction->exists) {
				continue;
			}
			// store / update the race
			self::createModel($fraction, $characterRace['mask'], $characterRace['name']);
		}
		// races are successfully fetched / synchronized
		return true;
	}
}