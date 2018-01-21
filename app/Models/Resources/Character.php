<?php

namespace App\Models\Resources;

use App\Models\Api\BattleNet;
use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Model to fetch all information about a single character from any EU realm.
 *
 * @author Stefan Herndler
 * @since 1.0.0
 * @class Character
 * @package App\Models\Resources
 * @property integer unenchanted_items
 * @property integer empty_sockets
 * @property integer artifact_traits
 * @property string artifact_weapon_slot
 * @property float avg_item_level_equipped
 * @property integer avg_item_level_bag
 * @property integer item_level_weapon
 * @property integer total_artifact_power
 * @property integer mythic_keystone_initiate
 * @property integer mythic_keystone_challenger
 * @property integer mythic_keystone_conqueror
 * @property integer mythic_keystone_master
 * @property \Carbon\Carbon last_modified
 */
class Character {

	/**
	 * The realm of the character.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var null|\App\Models\Resources\Realm
	 */
	private $realm = null;

	/**
	 * The name of the character.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var string
	 */
	private $name = '';

	/**
	 * Collection of all information fetched.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var array
	 */
	private $information = [];

	/**
	 * Flag if the character was found or not.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @var bool
	 */
	private $exists = false;

	/**
	 * Character constructor.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param \App\Models\Resources\Realm $realm The realm the character is at home.
	 * @param string $name The name of the character.
	 */
	public function __construct(Realm $realm, string $name) {
		// store the character's realm and name
		$this->realm = $realm;
		$this->name = $name;
		// fetch all information
		$this->exists = $this->requestBattleNet();
	}

	/**
	 * Flag if the character was found or not.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	public function exists() {
		return $this->exists;
	}

	/**
	 * Returns all information fetched about this character.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return array
	 */
	public function all() {
		return $this->information;
	}

	/**
	 * Returns the value of the requested information key or the specified default value if the key is unknown.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $key The requested information key.
	 * @param string|int|float $default Optional default value if key is unknown (default empty string).
	 * @return string|int|float|null
	 */
	public function get(string $key, $default = '') {
		return array_key_exists(Str::lower($key), $this->information) ? $this->information[Str::lower($key)] : $default;
	}

	/**
	 * Magic getter to get the value of an information by class property.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $key The class property name.
	 * @return float|int|string|null
	 */
	public function __get($key) {
		return $this->get($key, null);
	}

	/**
	 * Adds the specified information to this character.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param string $key The key name of the information.
	 * @param string|int|float $value The information value.
	 */
	private function add(string $key, $value) {
		$this->information[Str::lower($key)] = $value;
	}

	/**
	 * Fetches all standard information from the battle.net API without using OAuth.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @return bool
	 */
	private function requestBattleNet() {
		// create a new connection to the battle.net API without using OAuth
		$battleNet = new BattleNet(false);
		// fetch all character information
		$response = $battleNet->sendRequest(sprintf('character/%s/%s', $this->realm->name, $this->name), ['fields' => "audit,items,achievements"]);
		// check if information received
		if (!array_key_exists('audit', $response) || !is_array($response['audit']) || !count($response['audit'])
			|| !array_key_exists('items', $response) || !is_array($response['items']) || !count($response['items'])
			|| !array_key_exists('achievements', $response) || !is_array($response['achievements']) || !count($response['achievements'])) {
			return false;
		}
		// extract all information
		$this->getMissingEnchantmentsAndGems($response['audit']);
		$this->getArtifactTraitsInWeapon($response['items']);
		$this->getItemLevel($response['items']);
		$this->getAchievementCriteriaQuantities($response['achievements']);
		// set the last modified timestamp
		if (array_key_exists('lastModified', $response) && intval($response['lastModified']) > 0) {
			$this->add('last_modified', new Carbon(date('Y-m-d H:i:s', intval(intval($response['lastModified']) / 1000))));
		} else {
			$this->add('last_modified', null);
		}
		return true;
	}

	/**
	 * Extracts all missing enchantments and gems.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param array $audit The character's audit.
	 */
	private function getMissingEnchantmentsAndGems(array $audit) {
		// get the collection of all unenchanted items
		$unenchantedItems = array_key_exists('unenchantedItems', $audit) ? $audit['unenchantedItems'] : [];
		// remove invalid enchants which are not possible for items greater than 600 item level
		unset($unenchantedItems["4"]); // wrist
		unset($unenchantedItems["6"]); // waist
		unset($unenchantedItems["7"]); // legs
		unset($unenchantedItems["8"]); // feet
		unset($unenchantedItems["15"]); // main hand
		unset($unenchantedItems["16"]); // offhand
		// remove unnecessary missing enchants for the raid
		unset($unenchantedItems["2"]); // shoulder
		unset($unenchantedItems["9"]); // hands
		// add the information
		$this->add('unenchanted_items', count($unenchantedItems) > 0 ? count($unenchantedItems) : 0);
		$this->add('empty_sockets', array_key_exists('itemsWithEmptySockets', $audit) ? count($audit['itemsWithEmptySockets']) : 0);
	}

	/**
	 * Extract the number of traits in the currently equipped artifact weapon.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param array $items The character's currently equipped items.
	 */
	private function getArtifactTraitsInWeapon(array $items) {
		// temporary store the number of traits in the current artifact weapon
		$numberOfTraits = 0;
		// get the weapon which has the artifact traits (commonly main hand but can be off hand if tank or two-handed spec)
		$artifactWeaponSlot = array_key_exists('mainHand', $items) && array_key_exists('artifactTraits', $items['mainHand']) && count($items['mainHand']['artifactTraits']) > 0 ? "mainHand" : "";
		// check if main hand does not have artifact traits
		if (!Str::length($artifactWeaponSlot)) {
			// check the off hand
			$artifactWeaponSlot = array_key_exists('offHand', $items) && array_key_exists('artifactTraits', $items['offHand']) && count($items['offHand']['artifactTraits']) > 0 ? "offHand" : "";
		}
		// check if any artifact weapon was found
		if (Str::length($artifactWeaponSlot) > 0) {
			// iterate through all artifact traits
			foreach($items[$artifactWeaponSlot]['artifactTraits'] as $l_arr_Trait) {
				// add the rank of this trait
				$numberOfTraits += array_key_exists('rank', $l_arr_Trait) ? intval($l_arr_Trait['rank']) : 0;
			}
			// remove the number of relics because each adds an additional rank
			$numberOfTraits -= array_key_exists('relics', $items[$artifactWeaponSlot]) ? count($items[$artifactWeaponSlot]['relics']) : 0;
			// security - check that artifact traits aren't negative
			if ($numberOfTraits < 0) {
				$numberOfTraits = 0;
			}
		}
		// add the information
		$this->add('artifact_traits', $numberOfTraits);
		$this->add('artifact_weapon_slot', $artifactWeaponSlot);
	}

	/**
	 * Extract the item level equipped, in bag and of the artifact weapon.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param array $items The character's currently equipped items.
	 */
	private function getItemLevel(array $items) {
		// temporary store the total item level of all items
		$totalItemLevel = 0;
		// temporary store the number of items equipped
		$numberOfItemsEquipped = 0;
		// temporary store the item level of the main hand weapon
		$mainHandItemLevel = 0;
		// iterate through each item
		foreach($items as $slotName => $properties) {
			// check only real item slots
			if (!in_array($slotName, ['head','neck','shoulder','back','chest','wrist','hands','waist','legs','feet','finger1','finger2','trinket1','trinket2','mainHand','offHand'])
				|| !array_key_exists('itemLevel', $properties)) {
				continue;
			}
			// sum up the item level of this slot and increase the number of items equipped
			$totalItemLevel += intval($properties['itemLevel']);
			$numberOfItemsEquipped++;
			// store the item level of the main hand or off hand weapon (only of the first one that appears in the array)
			if ($slotName == "mainHand" || $slotName == "offHand") {
				$mainHandItemLevel = intval($properties['itemLevel']);
			}
		}
		// simulate the off hand if not found as blizzard does it too (simulated double main hand weapon)
		if (!array_key_exists('offHand', $items) || !array_key_exists('itemLevel', $items['offHand']) || intval($items['offHand']['itemLevel']) === 0) {
			// character does not wear an off hand (e.g. restoration druid)
			if (Str::length($this->get('artifact_weapon_slot')) > 0) {
				// character wears an artifact weapon so we simulate a second one (off hand) with the same item level
				$totalItemLevel += intval($items[$this->get('artifact_weapon_slot')]['itemLevel']);
				$numberOfItemsEquipped++;
			}
		}
		// add the information
		$this->add('avg_item_level_equipped', $numberOfItemsEquipped > 0 ? doubleval($totalItemLevel / $numberOfItemsEquipped) : 0.0);
		$this->add('avg_item_level_bag', array_key_exists('averageItemLevel', $items) ? intval($items['averageItemLevel']) : 0);
		$this->add('item_level_weapon', $mainHandItemLevel);
	}

	/**
	 * Extract all achievement criteria quantities.
	 *
	 * @author Stefan Herndler
	 * @since 1.0.0
	 * @param array $achievements Collection of all achievements of this character.
	 */
	private function getAchievementCriteriaQuantities(array $achievements) {
		// collection of achievement criteria ids to get their quantity
		$achievementCriteriaIds = [
			'total_artifact_power' => 30103, // total artifact power gained
			'mythic_keystone_initiate' => 33096, // number of mythic keystones 2+
			'mythic_keystone_challenger' => 33097, // number of mythic keystones 5+
			'mythic_keystone_conqueror' => 33098, // number of mythic keystones 10+
			'mythic_keystone_master' => 32028, // number of mythic keystones 15+
			// 'mythic_keystone_eoa_runs' => 32353, // number of mythic keystone runs in EOA
			// 'mythic_keystone_cos_runs' => 32354, // number of mythic keystone runs in COS
			// 'mythic_keystone_arc_runs' => 32355, // number of mythic keystone runs in ARC
			// 'mythic_keystone_brh_runs'=> 32356, // number of mythic keystone runs in BRH
			// 'mythic_keystone_mos_runs' => 32357, // number of mythic keystone runs in MOS
			// 'mythic_keystone_hov_runs' => 32358, // number of mythic keystone runs in HOV
			// 'mythic_keystone_dht_runs' => 32359, // number of mythic keystone runs in DHT
			// 'mythic_keystone_nl_runs' => 32360, // number of mythic keystone runs in NL
			// 'mythic_keystone_votw_runs' => 32361 // number of mythic keystone runs in VOTW
		];
		// iterate through all criteria and fetch their index
		foreach($achievementCriteriaIds as $key => $criteriaId) {
			// get the index
			$l_int_Index = array_search($criteriaId, $achievements['criteria']);
			// check if index found
			if ($l_int_Index === false) {
				// reset the value of the current key to not store the criteria index
				$this->add($key, 0);
				continue;
			}
			// store the criteria quality by its id
			$this->add($key, intval($achievements['criteriaQuantity'][$l_int_Index]));
		}
	}
}