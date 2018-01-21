<?php

namespace App\Models\Guilds;

use App\Models\Api\BattleNet;
use App\Models\Model;
use App\Models\Resources\CharacterClass;
use App\Models\Resources\CharacterRace;
use App\Models\Resources\CharacterSpec;
use App\Models\Resources\Fraction;
use App\Models\Resources\Realm;
use Carbon\Carbon;

/**
 * Model representing a single guild this website belongs to.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class Guild
 * @package App\Models\Guilds
 * @property integer id
 * @property integer realm_id
 * @property integer fraction_id
 * @property string name
 * @property integer level
 * @property integer achievement_points
 * @property \Carbon\Carbon last_modified
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 * @property Realm Realm
 * @property Fraction Fraction
 * @property \Illuminate\Database\Eloquent\Collection Members
 */
class Guild extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'guilds';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['realm_id', 'fraction_id', 'name', 'level', 'achievement_points', 'last_modified'];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = [
		'realm_id' => 'integer',
		'fraction_id' => 'integer',
		'name' => 'string',
		'level' => 'integer',
		'achievement_points' => 'integer'
	];

	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $dates = ['last_modified'];

	/**
	 * Relation to get the realm where this guild is at home.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function Realm() {
		return $this->belongsTo(Realm::class, 'realm_id', 'id');
	}

	/**
	 * Relation to get the fraction this guild belongs to.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function Fraction() {
		return $this->belongsTo(Fraction::class, 'fraction_id', 'id');
	}

	/**
	 * Relation to get all guild members of this guild.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function Members() {
		return $this->hasMany(GuildMember::class, 'guild_id', 'id');
	}

	/**
	 * Updates all information of this guild by fetching the latest information from the battle.net API.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public function updateFromBattleNet() {
		// create a new connection to the battle.net API
		$battleNet = new BattleNet(false);
		// fetch all guild information
		$response = $battleNet->sendRequest(sprintf('guild/%s/%s', $this->Realm->name, $this->name));
		// check if information received
		if (!array_key_exists('lastModified', $response) || !array_key_exists('name', $response) || !array_key_exists('realm', $response) || !array_key_exists('level', $response) || !array_key_exists('side', $response) || !array_key_exists('achievementPoints', $response)) {
			return false;
		}
		// update all attributes of this guild
		// we do not update the realm as this is already set correctly
		$this->fraction_id = Fraction::findByIndex(intval($response['side']))->getKey();
		// we do update the name to get the correct spelling (capitalization)
		$this->name = $response['name'];
		$this->level = intval($response['level']);
		$this->achievement_points = intval($response['achievementPoints']);
		$this->last_modified = new Carbon(date('Y-m-d H:i:s', intval($response['lastModified']) / 1000));
		// store all changes
		return $this->save();
	}

	/**
	 * Seeds all guild members of this guild and further updates/pre-fills all available character specs according to the current spec of the guild member.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public function seedGuildMembers() {
		// create a new connection to the battle.net API
		$battleNet = new BattleNet(false);
		// fetch all guild members
		$response = $battleNet->sendRequest(sprintf('guild/%s/%s', $this->Realm->name, $this->name), ['fields' => 'members']);
		// check if guild members received
		if (!array_key_exists('members', $response) || !is_array($response['members']) || !count($response['members'])) {
			return false;
		}
		// iterate through each guild member
		foreach($response['members'] as $guildMember) {
			// check if the guild member is valid
			if (!is_array($guildMember) || !array_key_exists('character', $guildMember) || !array_key_exists('rank', $guildMember) || !is_array($guildMember['character']) || !count($guildMember['character'])) {
				continue;
			}
			// extract all character information of this guild member
			$character = $guildMember['character'];
			// check if all required attributes of the character / guild member exists
			if (!array_key_exists('name', $character) || !array_key_exists('realm', $character) || !array_key_exists('class', $character) || !array_key_exists('race', $character) || !array_key_exists('gender', $character) || !array_key_exists('level', $character) || !array_key_exists('achievementPoints', $character) || !array_key_exists('thumbnail', $character) || !array_key_exists('lastModified', $character)) {
				continue;
			}
			// try to find the realm by its name
			$realm = Realm::findByName($character['realm']);
			// try to find the realm by its unique slug if the name is unknown
			if (!$realm->exists) {
				$realm = Realm::findBySlug($character['realm']);
				// abort if the realm can not ne found
				if (!$realm->exists) {
					continue;
				}
			}
			// get the character's race
			/** @var CharacterRace $characterRace */
			$characterRace = CharacterRace::query()->findOrNew(intval($character['race']));
			// abort if the race is unknown
			if (!$characterRace->exists) {
				continue;
			}
			// get the character's class
			/** @var CharacterClass $characterClass */
			$characterClass = CharacterClass::query()->findOrNew(intval($character['class']));
			// abort if the class is unknown
			if (!$characterClass->exists) {
				continue;
			}
			// try to load an existing guild member by the guild and character name
			$newGuildMember = GuildMember::findByGuildAndName($this, $character['name']);
			// update all attributes of this guild member
			$newGuildMember->guild_id = $this->getKey();
			$newGuildMember->realm_id = $realm->getKey();
			$newGuildMember->character_race_id = $characterRace->getKey();
			$newGuildMember->character_class_id = $characterClass->getKey();
			$newGuildMember->guild_rank = intval($guildMember['rank']);
			$newGuildMember->gender = intval($character['gender']);
			$newGuildMember->name = $character['name'];
			$newGuildMember->level = intval($character['level']);
			$newGuildMember->achievement_points = intval($character['achievementPoints']);
			$newGuildMember->thumbnail = $character['thumbnail'];
			// check if last modified timestamp is set (may be zero for very old characters)
			if (intval($character['lastModified']) > 0) {
				$newGuildMember->last_modified = new Carbon(date('Y-m-d H:i:s', intval($character['lastModified']) / 1000));
			}
			$newGuildMember->save();
			// update the collection of character specs
			if (!array_key_exists('spec', $character) || !is_array($character['spec']) || !count($character['spec'])) {
				continue;
			}
			// extract all spec information
			$characterSpec = $character['spec'];
			// check if all required information of this spec exists
			if (!array_key_exists('name', $characterSpec) || !array_key_exists('backgroundImage', $characterSpec) || !array_key_exists('icon', $characterSpec)) {
				continue;
			}
			// create / update the character's spec
			CharacterSpec::createModel($characterClass, $characterSpec['name'], $characterSpec['backgroundImage'], $characterSpec['icon']);
		}

		// delete all guild members which were not updated after this seed (last updated 1 day ago or older)
		// the deletion will only be executed if at least 1 guild member was fetched by the battle.net API to avoid connection errors
		GuildMember::query()
			->where('guild_id', '=', $this->getKey())
			->where('updated_at', '<=', (new Carbon())->subDay(1))
			->delete();
		// guild members are successfully fetched / synchronized
		return true;
	}

	/**
	 * Deletes this guild and all its members.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool|null
	 */
	public function delete() {
		// delete all guild members of this guild
		$this->Members()->delete();
		// delete this guild
		return parent::delete();
	}

	/**
	 * Returns the first guild found which matches the specified name within the specified realm.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Resources\Realm $realm The realm to find the guild.
	 * @param string $name The name of the guild.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Guilds\Guild
	 */
	public static function findByRealmAndName(Realm $realm, string $name) {
		return self::query()->firstOrNew(['realm_id' => $realm->getKey(), 'name' => $name]);
	}

	/**
	 * Creates or updates a new guild.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Resources\Realm $realm The realm the guild has its home.
	 * @param string $name The name of the guild.
	 * @return \App\Models\Guilds\Guild|\Illuminate\Database\Eloquent\Model
	 */
	public static function createModel(Realm $realm, string $name) {
		// try to find an existing guild by realm and name
		$l_obj_Guild = self::findByRealmAndName($realm, $name);
		// fetch all information from the battle.net API
		if ($l_obj_Guild->updateFromBattleNet()) {
			// seed all guild members
			$l_obj_Guild->seedGuildMembers();
		}
		// return the newly created / updated guild
		return $l_obj_Guild;
	}
}