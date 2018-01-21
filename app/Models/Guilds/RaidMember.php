<?php

namespace App\Models\Guilds;

use App\Models\Model;
use App\Models\Resources\CharacterSpec;

/**
 * Model representing a single guild member as raid member and his/her raid spec.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class RaidMember
 * @package App\Models\Guilds
 * @property integer id
 * @property integer guild_member_id
 * @property integer character_spec_id
 * @property \Carbon\Carbon created_at
 * @property \Carbon\Carbon updated_at
 * @property \App\Models\Guilds\GuildMember GuildMember
 * @property \App\Models\Resources\CharacterSpec Spec
 */
class RaidMember extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'raid_members';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['guild_member_id', 'character_spec_id'];

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = [
		'guild_member_id' => 'integer',
		'character_spec_id' => 'integer'
	];

	/**
	 * Relation to get the guild member.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function GuildMember() {
		return $this->belongsTo(GuildMember::class, 'guild_member_id', 'id');
	}

	/**
	 * Relation to get the raid spec of the connected guild member.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function Spec() {
		return $this->belongsTo(CharacterSpec::class, 'character_spec_id', 'id');
	}

	/**
	 * Finds the raid member by its unique connected guild member.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Guilds\GuildMember $guildMember The guild member to get its raid information.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Guilds\RaidMember
	 */
	public static function findByGuildMember(GuildMember $guildMember) {
		return self::query()->firstOrNew(['guild_member_id' => $guildMember->getKey()]);
	}

	/**
	 * Creates or updates the raid member information for this guild member.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Guilds\GuildMember $guildMember The guild member to set / update its raid spec.
	 * @param \App\Models\Resources\CharacterSpec $characterSpec The raid spec of this guild member.
	 * @return \App\Models\Guilds\RaidMember|\Illuminate\Database\Eloquent\Model
	 */
	public static function createModel(GuildMember $guildMember, CharacterSpec $characterSpec) {
		// try to find an existing raid member information for this guild member
		$raidMember = self::findByGuildMember($guildMember);
		// update the raid spec
		$raidMember->character_spec_id = $characterSpec->getKey();
		$raidMember->save();
		// return the raid member information
		return $raidMember;
	}
}