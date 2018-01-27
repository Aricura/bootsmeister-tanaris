<?php

namespace App\Models\Guilds;

use App\Models\Model;
use App\Models\Resources\Character;
use App\Models\Resources\CharacterClass;
use App\Models\Resources\CharacterRace;
use App\Models\Resources\CharacterSpec;
use App\Models\Resources\Realm;
use Illuminate\Support\Str;

/**
 * Model representing a single guild member.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class GuildMember
 * @package App\Models\Guilds
 * @property integer id
 * @property integer guild_id
 * @property integer realm_id
 * @property integer character_race_id
 * @property integer character_class_id
 * @property integer guild_rank
 * @property integer gender
 * @property string name
 * @property integer level
 * @property integer achievement_points
 * @property string thumbnail
 * @property float item_level_equipped
 * @property float item_level_total
 * @property \Carbon\Carbon last_modified
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 * @property \App\Models\Guilds\Guild Guild
 * @property Realm Realm
 * @property CharacterRace CharacterRace
 * @property CharacterClass CharacterClass
 * @property \App\Models\Guilds\RaidMember RaidMember
 */
class GuildMember extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'guild_members';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['guild_id', 'realm_id', 'character_race_id', 'character_class_id', 'guild_rank', 'gender', 'name', 'level', 'achievement_points', 'thumbnail', 'item_level_equipped', 'item_level_total', 'last_modified'];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = [
		'guild_id' => 'integer',
		'realm_id' => 'integer',
		'character_race_id' => 'integer',
		'character_class_id' => 'integer',
		'guild_rank' => 'integer',
		'gender' => 'integer',
		'name' => 'string',
		'level' => 'integer',
		'achievement_points' => 'integer',
		'thumbnail' => 'string',
		'item_level_equipped' => 'float',
		'item_level_total' => 'float'
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
	 * Relation to get the guild this member belongs to.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function Guild() {
		return $this->belongsTo(Guild::class, 'guild_id', 'id');
	}

	/**
	 * Relation to get the realm this character is at home.
	 * The realm may be different as the guild's realm if it's a connected realm (e.g. Guild on Nachtwache and member on Forscherliga).
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function Realm() {
		return $this->belongsTo(Realm::class, 'realm_id', 'id');
	}

	/**
	 * Relation to get the race of this member.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function CharacterRace() {
		return $this->belongsTo(CharacterRace::class, 'character_race_id', 'id');
	}

	/**
	 * Relation to get the class of this member.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function CharacterClass() {
		return $this->belongsTo(CharacterClass::class, 'character_class_id', 'id');
	}

	/**
	 * Relation to get the raid member connection of this guild member and its raid spec.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasOne
	 */
	public function RaidMember() {
		return $this->hasOne(RaidMember::class, 'guild_member_id', 'id');
	}

	/**
	 * Fetches all information about this guild member from the battle.net API and updates the guild member.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public function updateFromBattleNet() {
		// get all information for this guild member
		$character = new Character($this->Realm, $this->name);
		// check if the information were successfully fetched
		if (!$character->exists()) {
			return false;
		}
		// update the own attributes
		$this->item_level_equipped = $character->avg_item_level_equipped;
		$this->item_level_total = $character->avg_item_level_bag;
		// update the last modified timestamp only when set
		if ($character->last_modified !== null) {
			$this->last_modified = $character->last_modified;
		}
		return $this->save();
	}

	/**
	 * Marks this guild member as raid member and playing the specified spec as main.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Resources\CharacterSpec $characterSpec The main raid spec.
	 * @return \App\Models\Guilds\RaidMember|\Illuminate\Database\Eloquent\Model
	 */
	public function markAsRaidMember(CharacterSpec $characterSpec) {
		return RaidMember::createModel($this, $characterSpec);
	}

	/**
	 * Removes this guild member as raid member.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool|null
	 */
	public function removeAsRaidMember() {
		return RaidMember::findByGuildMember($this)->delete();
	}

	/**
	 * Returns the absolute path to the thumbnail of the character.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getThumbnail() {
		return sprintf('https://render-eu.worldofwarcraft.com/character/%s', $this->thumbnail);
	}

	/**
	 * Returns the absolute path to the thumbnail of the character.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getProfilePicture() {
		return str_replace('avatar', 'profilemain', $this->getThumbnail()) . '?alt=/wow/static/images/2d/profilemain/class/6-1.jpg';
	}

	/**
	 * Returns the absolute path to the thumbnail of the character.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getProfilePictureInset() {
		return str_replace('avatar', 'inset', $this->getThumbnail()) . '?alt=/wow/static/images/2d/profilemain/class/6-1.jpg';
	}

	/**
	 * Returns the absolute url to the wow armory of this character.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getArmoryLink() {
		return Str::lower(sprintf('https://worldofwarcraft.com/de-de/character/%s/%s', $this->Realm->name, $this->name));
	}

	/**
	 * Returns the absolute url to the wow progress of this character.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getWoWProgressLink() {
		return Str::lower(sprintf('http://www.wowprogress.com/character/eu/%s/%s', $this->Realm->name, $this->name));
	}

	/**
	 * Deletes this guild member and all its information.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool|null
	 */
	public function delete() {
		// delete the raid member information
		$this->RaidMember()->delete();
		// delete this guild member
		return parent::delete();
	}

	/**
	 * Returns the first guild member found which matches the specified name within the specified realm.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Resources\Realm $realm The realm to find the guild member.
	 * @param string $name The name of the guild member.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Guilds\GuildMember
	 */
	public static function findByRealmAndName(Realm $realm, string $name) {
		// build a query to find the guild member by binary name to avoid duplicates (e.g. ó and o)
		$query = sprintf('SELECT id FROM guild_members WHERE binary name = \'%s\' AND realm_id = %d LIMIT 0,1;', $name, $realm->getKey());
		// execute the query and fetch all rows
		$rows = \DB::select($query);
		// check if a record was found ans extract its id
		if (count($rows) === 1 && property_exists($rows[0], 'id')) {
			return self::query()->find($rows[0]->id);
		}
		// no guild member found
		return new self();
	}

	/**
	 * Returns the first guild member found which matches the specified name within the specified guild.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param Guild $guild The guild to find the guild member.
	 * @param string $name The name of the guild member.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Guilds\GuildMember
	 */
	public static function findByGuildAndName(Guild $guild, string $name) {
		// build a query to find the guild member by binary name to avoid duplicates (e.g. ó and o)
		$query = sprintf('SELECT id FROM guild_members WHERE binary name = \'%s\' AND guild_id = %d LIMIT 0,1;', $name, $guild->getKey());
		// execute the query and fetch all rows
		$rows = \DB::select($query);
		// check if a record was found ans extract its id
		if (count($rows) === 1 && property_exists($rows[0], 'id')) {
			return self::query()->find($rows[0]->id);
		}
		// no guild member found
		return new self();
	}
}