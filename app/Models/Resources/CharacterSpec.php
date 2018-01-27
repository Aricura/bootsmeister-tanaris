<?php

namespace App\Models\Resources;

use App\Models\Guilds\RaidMember;
use App\Models\Model;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Model representing a single spec of a character class.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class CharacterSpec
 * @package App\Models\Resources
 * @property integer id
 * @property integer character_class_id
 * @property string name
 * @property string role
 * @property string background_image
 * @property string icon
 * @property \Carbon\Carbon created_at
 * @property string LocalizedName
 * @property string RoleName
 * @property \App\Models\Resources\CharacterClass CharacterClass
 * @property \Illuminate\Database\Eloquent\Collection RaidMembers
 */
class CharacterSpec extends Model {

	/**
	 * The table associated with the model.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	protected $table = 'character_specs';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	protected $fillable = ['character_class_id', 'name', 'role', 'background_image', 'icon', 'created_at'];

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
		'character_class_id' => 'integer',
		'name' => 'string',
		'role' => 'string',
		'background_image' => 'string',
		'icon' => 'string'
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
	 * Relation to get the character class this spec belongs to.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function CharacterClass() {
		return $this->belongsTo(CharacterClass::class, 'character_class_id', 'id');
	}

	/**
	 * Relation to get all raid members of this spec.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return \Illuminate\Database\Eloquent\Relations\HasMany
	 */
	public function RaidMembers() {
		return $this->hasMany(RaidMember::class, 'character_spec_id', 'id');
	}

	/**
	 * Returns the localized name of this spec.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getLocalizedNameAttribute() {
		// check if a localized version exists
		if (!\Lang::has(sprintf('resources/classes.%s.specs.%s.name', $this->CharacterClass->name, $this->name), \App::getLocale())) {
			// return the internal spec name as fallback
			return Str::ucfirst($this->name);
		}
		// return the localized spec name
		return trans(sprintf('resources/classes.%s.specs.%s.name', $this->CharacterClass->name, $this->name), [], \App::getLocale());
	}

	/**
	 * Returns the localized name of this role.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return string
	 */
	public function getRoleNameAttribute() {
		// check if a localized version exists
		if (!\Lang::has(sprintf('resources/roles.%s.name', $this->role), \App::getLocale())) {
			// return the internal role name as fallback
			return Str::ucfirst($this->role);
		}
		// return the localized role name
		return trans(sprintf('resources/roles.%s.name', $this->role), [], \App::getLocale());
	}

	/**
	 * Deletes this spec and all connected raid members.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool|null
	 */
	public function delete() {
		// delete all raid members of this spec
		$this->RaidMembers()->delete();
		// delete this character spec
		return parent::delete();
	}

	/**
	 * Returns the unique spec found by name within the specified class.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Resources\CharacterClass $characterClass The class to find its unique spec within it.
	 * @param string $name The unique name of the spec within the specified class.
	 * @return \Illuminate\Database\Eloquent\Model|\App\Models\Resources\CharacterSpec
	 */
	public static function findByClassAndName(CharacterClass $characterClass, string $name) {
		return self::query()->firstOrNew(['character_class_id' => $characterClass->getKey(), 'name' => $name]);
	}

	/**
	 * Creates or updates an existing character spec.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Resources\CharacterClass $characterClass The character class this spec belongs to.
	 * @param string $name The name of the spec.
	 * @param string $backgroundImage The background image of the spec.
	 * @param string $icon The icon of the spec.
	 * @return \App\Models\Resources\CharacterSpec|\Illuminate\Database\Eloquent\Model
	 */
	public static function createModel(CharacterClass $characterClass, string $name, string $backgroundImage, string $icon) {
		// try to load an existing spec by class and name
		$characterSpec = self::findByClassAndName($characterClass, $name);
		// update all attributes
		$characterSpec->role = self::detectRoleByClassAndSpecName($characterClass, $name);
		$characterSpec->background_image = $backgroundImage;
		$characterSpec->icon = $icon;
		// set the current timestamp it this is a new spec and not an updated version
		if (!$characterSpec->exists) {
			$characterSpec->created_at = new Carbon();
		}
		// save all changes
		$characterSpec->save();
		// return the new / updated spec
		return $characterSpec;
	}

	/**
	 * Returns the role name for the specified spec name.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Resources\CharacterClass $characterClass The character class this spec belongs to.
	 * @param string $name The name of the spec to get its role in a raid.
	 * @return string
	 */
	public static function detectRoleByClassAndSpecName(CharacterClass $characterClass, string $name) {
		// get the lower case name of the spec and class
		$characterSpecClassName = Str::lower(sprintf('%s %s', $name, $characterClass->name));
		// the role depends on the name of the spec and class
		switch($characterSpecClassName) {
			// tanks
			case 'blood death knight':
			case 'vengeance demon hunter':
			case 'guardian druid':
			case 'brewmaster monk':
			case 'protection paladin':
			case 'protection warrior':
				return 'tank';

			// melee dps
			case 'frost death knight':
			case 'unholy death knight':
			case 'havoc demon hunter':
			case 'feral druid':
			case 'survival hunter':
			case 'windwalker monk':
			case 'retribution paladin':
			case 'assassination rogue':
			case 'outlaw rogue':
			case 'subtlety rogue':
			case 'enhancement shaman':
			case 'arms warrior':
			case 'fury warrior':
				return 'melee';

			// range dps
			case 'balance druid':
			case 'beast mastery hunter':
			case 'marksmanship hunter':
			case 'arcane mage':
			case 'fire mage':
			case 'frost mage':
			case 'shadow priest':
			case 'elemental shaman':
			case 'affliction warlock':
			case 'demonology warlock':
			case 'destruction warlock':
				return 'range';

			// healer
			case 'restoration druid':
			case 'mistweaver monk':
			case 'holy paladin':
			case 'discipline priest':
			case 'holy priest':
			case 'restoration shaman':
				return 'heal';
		}

		// use tank as fallback if the spec name is unknown
		\Log::warning(sprintf('Unknown role for: %s', $characterSpecClassName));
		return 'tank';
	}

	/**
	 * Seeds all character specs.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 */
	public static function seed() {
		// death knight
		$class = CharacterClass::findByName('Death Knight');
		self::createModel($class, 'Blood', 'bg-deathknight-blood', 'spell_deathknight_bloodpresence');
		self::createModel($class, 'Unholy', 'bg-deathknight-unholy', 'spell_deathknight_unholypresence');
		self::createModel($class, 'Frost', 'bg-deathknight-frost', 'spell_deathknight_frostpresence');

		// demon hunter
		$class = CharacterClass::findByName('Demon Hunter');
		self::createModel($class, 'Havoc', 'bg-rogue-subtlety', 'ability_demonhunter_specdps');
		self::createModel($class, 'Vengeance', 'bg-warlock-demonology', 'ability_demonhunter_spectank');

		// druid
		$class = CharacterClass::findByName('Druid');
		self::createModel($class, 'Guardian', 'bg-druid-bear', 'ability_racial_bearform');
		self::createModel($class, 'Balance', 'bg-druid-balance', 'spell_nature_starfall');
		self::createModel($class, 'Restoration', 'bg-druid-restoration', 'spell_nature_healingtouch');
		self::createModel($class, 'Feral', 'bg-druid-cat', 'ability_druid_catform');

		// hunter
		$class = CharacterClass::findByName('Hunter');
		self::createModel($class, 'Marksmanship', 'bg-hunter-marksman', 'ability_hunter_focusedaim');
		self::createModel($class, 'Beast Mastery', 'bg-hunter-beastmaster', 'ability_hunter_bestialdiscipline');
		self::createModel($class, 'Survival', 'bg-hunter-survival', 'ability_hunter_camouflage');

		// mage
		$class = CharacterClass::findByName('Mage');
		self::createModel($class, 'Fire', 'bg-mage-fire', 'spell_fire_firebolt02');
		self::createModel($class, 'Frost', 'bg-mage-frost', 'spell_frost_frostbolt02');
		self::createModel($class, 'Arcane', 'bg-mage-arcane', 'spell_holy_magicalsentry');

		// monk
		$class = CharacterClass::findByName('Monk');
		self::createModel($class, 'Windwalker', 'bg-monk-battledancer', 'spell_monk_windwalker_spec');
		self::createModel($class, 'Mistweaver', 'bg-monk-mistweaver', 'spell_monk_mistweaver_spec');
		self::createModel($class, 'Brewmaster', 'bg-monk-brewmaster', 'spell_monk_brewmaster_spec');

		// paladin
		$class = CharacterClass::findByName('Paladin');
		self::createModel($class, 'Protection', 'bg-paladin-protection', 'ability_paladin_shieldofthetemplar');
		self::createModel($class, 'Retribution', 'bg-paladin-retribution', 'spell_holy_auraoflight');
		self::createModel($class, 'Holy', 'bg-paladin-holy', 'spell_holy_holybolt');

		// priest
		$class = CharacterClass::findByName('Priest');
		self::createModel($class, 'Holy', 'bg-priest-holy', 'spell_holy_guardianspirit');
		self::createModel($class, 'Shadow', 'bg-priest-shadow', 'spell_shadow_shadowwordpain');
		self::createModel($class, 'Discipline', 'bg-priest-discipline', 'spell_holy_powerwordshield');

		// rogue
		$class = CharacterClass::findByName('Rogue');
		self::createModel($class, 'Subtlety', 'bg-rogue-subtlety', 'ability_stealth');
		self::createModel($class, 'Assassination', 'bg-rogue-assassination', 'ability_rogue_deadlybrew');
		self::createModel($class, 'Outlaw', 'bg-rogue-combat', 'inv_sword_30');

		// shaman
		$class = CharacterClass::findByName('Shaman');
		self::createModel($class, 'Enhancement', 'bg-shaman-enhancement', 'spell_shaman_improvedstormstrike');
		self::createModel($class, 'Restoration', 'bg-shaman-restoration', 'spell_nature_magicimmunity');
		self::createModel($class, 'Elemental', 'bg-shaman-elemental', 'spell_nature_lightning');

		// warlock
		$class = CharacterClass::findByName('Warlock');
		self::createModel($class, 'Affliction', 'bg-warlock-affliction', 'spell_shadow_deathcoil');
		self::createModel($class, 'Demonology', 'bg-warlock-demonology', 'spell_shadow_metamorphosis');
		self::createModel($class, 'Destruction', 'bg-warlock-destruction', 'spell_shadow_rainoffire');

		// warrior
		$class = CharacterClass::findByName('Warrior');
		self::createModel($class, 'Fury', 'bg-warrior-fury', 'ability_warrior_innerrage');
		self::createModel($class, 'Protection', 'bg-warrior-protection', 'ability_warrior_defensivestance');
		self::createModel($class, 'Arms', 'bg-warrior-arms', 'ability_warrior_savageblow');
	}
}