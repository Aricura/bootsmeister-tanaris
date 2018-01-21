<?php

namespace App\Models\Resources;

use App\Models\Guilds\Guild;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Model representing a single fraction.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class Fraction
 * @package App\Models\Resources
 * @property integer id
 * @property string slug
 * @property \Carbon\Carbon created_at
 * @property string LocalizedName
 * @property \Illuminate\Database\Eloquent\Collection Races
 * @property \Illuminate\Database\Eloquent\Collection Guilds
 */
class Fraction extends Model {

	/**
	 * Fraction type enumeration.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	public const HORDE = 'horde';
	public const ALLIANCE = 'alliance';
	public const NEUTRAL = 'neutral';

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'fractions';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['slug', 'created_at'];

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
		'slug' => 'string'
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
	 * Relation to get all character races of this fractions.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function Races() {
		return $this->hasMany(CharacterRace::class, 'fraction_id', 'id');
	}

	/**
	 * Relation to get all guilds of this fraction.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function Guilds() {
		return $this->hasMany(Guild::class, 'fraction_id', 'id');
	}

	/**
	 * Returns the localized name of this fraction.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getLocalizedNameAttribute() {
		// check if a localized version exists
		if (!\Lang::has(sprintf('resources/fractions.%s.name', $this->slug), \App::getLocale())) {
			// return the internal name as fallback
			return Str::ucfirst($this->slug);
		}
		// return the localized name
		return trans(sprintf('resources/fractions.%s.name', $this->slug), [], \App::getLocale());
	}

	/**
	 * Deletes this fraction and all connected guilds and races.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool|null
	 */
	public function delete() {
		// delete all guilds of this fraction
		$this->Guilds()->delete();
		// delete all races of this fraction
		$this->Races()->delete();
		// delete this fraction
		return parent::delete();
	}

	/**
	 * Returns the unique fraction identified by its unique slug.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $slug The unique slug to find a single fraction.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Resources\Fraction
	 */
	public static function findBySlug(string $slug) {
		return self::query()->firstOrNew(['slug' => Str::lower($slug)]);
	}

	/**
	 * Returns the unique fraction identified by its unique index.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param int $index The unique index to find a single fraction.
	 * @return \App\Models\Resources\Fraction|\Illuminate\Database\Eloquent\Model
	 */
	public static function findByIndex(int $index) {
		return self::findBySlug($index === 1 ? self::HORDE : self::ALLIANCE);
	}

	/**
	 * Creates or updates an existing fraction.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $slug The unique slug of the new fraction.
	 * @return \App\Models\Resources\Fraction|\Illuminate\Database\Eloquent\Model
	 */
	public static function createModel(string $slug) {
		// try to load an existing fraction by its unique slug
		$fraction = self::findBySlug($slug);
		// set the current timestamp it this is a new fraction and not an updated version
		if (!$fraction->exists) {
			$fraction->created_at = new Carbon();
			$fraction->save();
		}
		// return the new / updated fraction
		return $fraction;
	}

	/**
	 * Seeds all fractions.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public static function seed() {
		self::createModel(self::ALLIANCE);
		self::createModel(self::HORDE);
		self::createModel(self::NEUTRAL);
		// fractions are successfully fetched / synchronized
		return true;
	}
}