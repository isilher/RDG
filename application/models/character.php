<?php

/**
 * Character DataMapper Model
 *
 * @author		Simon Kort
 * @link		http://www.campaigncodex.com
 */
class Character extends DataMapper {
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'character';
	public $table = 'character';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that Character can have just one of.
	public $has_one = array(
			'player' => array(
 					'class' => 'account',
 					'other_field' => 'characters'),
			);

	// Insert related models that Character can have more than one of.
	public $has_many = array(
			'nodes' => array(
 					'class' => 'node',
 					'other_field' => 'character'),
			);
	 
	// Validation
	public $validation = array();

	// Default Ordering
	public $default_order_by = array('name', 'id' => 'desc');

	// CI object
	private $CI = NULL;
	
	/*
	 * Ability scores
	 */
	public $str = 0;
	public $dex = 0;
	public $con = 0;
	public $int = 0;
	public $wis = 0;
	public $cha = 0;
	
	/*
	 * Alignment
	 */
	public $alignment_morality = NULL;
	public $alignment_lawfulness = NULL;
	public $alignment = NULL;
	
	/*
	 * Vital statistics
	 */
	public $age = NULL;
	public $height = NULL;
	public $weight = NULL;
	public $eyes = NULL;
	public $hair = NULL;
	public $deity = NULL;
	public $homeland = NULL;
	public $gender = NULL;
	
	/*
	 * Race
	 */
	public $race = NULL;
	public $size = NULL;
	
	/*
	 * Classes
	 */
	public $level = 0;
	
	/*
	 * Armor class
	 */
	public $ac = 0;
	public $ac_touch = 0;
	public $ac_flatfooted = 0;
	public $ac_sources = array();
	
	/*
	 * Hitpoints, DR, resistances
	 */
	public $hp = 0;
	public $hp_current = 0;
	
	/*
	 * Initiative
	 */
	public $initiative = 0;
	
	// Set of Bonus objects derived from nodes
	public $bonusses = array();
	
	/**
	 * Constructor: calls parent constructor
	 */
    public function __construct($id = NULL)
	{
		parent::__construct($id);
		$this->CI =& get_instance();
		
		// Default stats to 0
		$this->str = new Node();
		$this->str->value = 0;
		$this->dex = new Node();
		$this->dex->value = 0;
		$this->con = new Node();
		$this->con->value = 0;
		$this->int = new Node();
		$this->int->value = 0;
		$this->wis = new Node();
		$this->wis->value = 0;
		$this->cha = new Node();
		$this->cha->value = 0;
    }

	// --------------------------------------------------------------------
	// Post Model Initialisation
	//   Add your own custom initialisation code to the Model
	// The parameter indicates if the current config was loaded from cache or not
	// --------------------------------------------------------------------
	public function post_model_init($from_cache = FALSE)
	{
	}
	
	public function __toString()
	{
		return $this->name;
	}
	
	/*
	 * Calculate methods
	 */
	public function calc()
	{
		$this->set_simple_stats();
		$this->calc_level();
		$this->calc_hitpoints();
		$this->calc_ac();
		
		$this->set_bonusses();
	}
	
	/*
	 * Calculate level
	 */
	public function calc_level()
	{
		$this->level = $this->nodes->where('type', 'level')->count();
		
		return $this->level;
	}
	
	/*
	 * Calculate hitpoints
	 */
	function calc_hitpoints()
	{
		$total = 0;
		$allowed_types = array('hp');
		
		$hp_nodes = $this->get_nodes($allowed_types);
		
		// iterate through nodes and add relevant ones to the mods array
		foreach($hp_nodes as $node)
		{
			// Seperate formulas from direct values
			if(is_numeric($node->value))
			{
				// Direct modifier
				$total += $node->value;
			}
			else
			{
				// TODO: Implement formulas
			}
		}
		
		// TODO: Add CON values
		
		$this->hp = $total;
		
		return $total;
	}
	
	/*
	 * Calculate armor class
	 */
	function calc_ac() {
		$allowed_types = array('ac');
		$bonustypes = $this->CI->config->item('bonustypes');
		
		$ac_nodes = $this->get_nodes($allowed_types);
		
		// Determine what bonusses to use
		$ac_types = array();
		
		foreach($ac_nodes as $node)
		{	
			// Add the node to the sources array
			$this->ac_sources[] = $node;
						
			switch($node->bonustype)
			{
				case 'dodge':
				case 'untyped':
					// These bonusses stack, always add them
					$node->used = TRUE;
					break;
					
				default:
					// These bonnusses do not stack
					if(!in_array($node->bonustype, $ac_types))
					{
						// Not used yet
						$ac_types[] = $node->bonustype;
						$node->used = TRUE;
					}
					else
					{
						// Already used
						foreach($this->ac_sources as $ac_source)
						{
							if($ac_source->used == TRUE && $ac_source->bonustype == $node->bonustype)
							{
								//echo 'comparing ' . $node .  ' with ' . $ac_source . '<br>';
								if($node->value > $ac_source->value)
								{
									// Use this bonus instead of the earlier used
									$ac_source->used = FALSE;
									$node->used = TRUE;
								}
							}
						}
					}
					break;
			}
		}

		// Determine the AC
		foreach($ac_nodes as $node)
		{
			if($node->used)
			{
				$this->ac += $node->value;
			}
		}
		
		// Determine flatfooted AC
		$this->ac_flatfooted = $this->ac;
		foreach($ac_nodes as $key => $node)
		{
			if($node->bonustype == 'dodge')
			{
				$this->ac_flatfooted -= $node->value;
			}
		}
		
		// Add the dex modifier
		$this->ac += $this->calc_mod($this->dex->value);
		$this->ac_sources[] = $this->dex;
		$this->dex->used = TRUE;
		
		// At this point full AC is done, now determine touch AC
		$this->ac_touch = $this->ac;
		foreach($ac_nodes as $key => $node)
		{
			if(in_array($node->bonustype, array('armor', 'shield', 'natural armor')) && $node->used)
			{
				$this->ac_touch -= $node->value;
			}
		}
		
		
		return $this->ac;
	}
	
	/*
	 * TEMPORARY FUNCTION TODO!
	 */
	public function calcMod($arg1, $arg2)
	{
		return rand(-4, 4);
	}
	
	public function calc_mod($value)
	{
		if(is_numeric($value))
		{
			return sprintf("%+d", round(($value / 2), 0, PHP_ROUND_HALF_DOWN) - 5);
		}
		else
		{
			return '<strong>Error:</strong> Base stat is not a value. Remember to pass only numeric values to calc_mod(), not nodes.';
		}
	}
	
	/*
	 * TEMPORARY FUNCTION TODO!
	*/
	public function get_bonus($bonustype)
	{
		$bonus = $this->nodes->where('bonustype', $bonustype)->get();
		
		// TODO: Determine actual bonus instead of first one retrieved
		
		return $bonus;
	}
	
	/**
	 * Set all bonusses
	 */
	public function set_bonusses()
	{
		// Go through all nodes to look for bonusses
		foreach($this->nodes as $node)
		{
			// If this node is a bonus
			if($node->bonustype !== NULL)
			{
				$this->bonusses[] = $node;
			}
		}		
		
// 		// Go through all nodes to look for bonusses
// 		foreach($this->nodes as $node)
// 		{
// 			// If this node is a bonus
// 			if($node->bonustype !== NULL)
// 			{
// 				$this->bonusses[] = new Bonus($node->value, $node->type, $node->bonustype, $node->parent->value, TRUE);
// 			}
// 		}
		
// 		// Add primary stat exceptions
// 		$this->bonusses[] = new Bonus($this->calc_mod($this->str->value), 'str_mod', 'untyped', 'Primary stat: Strength', TRUE);
// 		$this->bonusses[] = new Bonus($this->calc_mod($this->dex->value), 'dex_mod', 'untyped', 'Primary stat: Dexterity', TRUE);
// 		$this->bonusses[] = new Bonus($this->calc_mod($this->con->value), 'con_mod', 'untyped', 'Primary stat: Constitution', TRUE);
// 		$this->bonusses[] = new Bonus($this->calc_mod($this->int->value), 'int_mod', 'untyped', 'Primary stat: Intelligence', TRUE);
// 		$this->bonusses[] = new Bonus($this->calc_mod($this->wis->value), 'wis_mod', 'untyped', 'Primary stat: Willpower', TRUE);
// 		$this->bonusses[] = new Bonus($this->calc_mod($this->str->value), 'cha_mod', 'untyped', 'Primary stat: Charisma', TRUE);
		
// 		// Sort the bonusses array
// 		usort($this->bonusses, array('Character','sort_bonusses'));
	}
	
	private function set_simple_stats()
	{
		$this->CI->load->config('cc');
		
		$allowed_types = $this->CI->config->item('cc_stats');
		
		// iterate through nodes
		foreach($this->nodes as $node)
		{
			if(in_array($node->type, $allowed_types))
			{
				$nodetype = $node->type;
				$this->$nodetype = $node;
			}
		}

		// Combine alignment_lawfulness and alignment_morality into alignment TODO: See if this can be reworked
		$this->alignment = $this->alignment_lawfulness . ' ' . $this->alignment_morality;
	}
	
	/*
	 * Get a subset of nodes based on an array of allowed types.
	 */
	private function get_nodes($allowed_types)
	{
		// Array of references to relevant nodes
		$total = array();
		
		// Iterate through nodes and add relevant ones to the total array
		foreach($this->nodes as $node)
		{
			if(in_array($node->type, $allowed_types))
			{
				$total[$node->id] = $node;
			}
		}
		
		return $total;
	}
	
	/*
	 * Get a specific node or a array of nodes if there is more then one that matches the criteria.
	 */
	public function node($type)
	{	
		return $this->nodes->where('type', $type)->get();
	}
	
	/*
	 * Get all feats associated with this character.
	 */
	public function get_feats()
	{
		return $this->nodes->where('type', 'feat')->get();
	}
	
	/*
	 * Get all traits associated with this character.
	*/
	public function get_traits()
	{
		return $this->nodes->where('type', 'feat')->get();
	}

	/*
	 * Get all special abilities associated with this character.
	*/
	public function get_special()
	{
		return $this->nodes->where('type', 'feat')->get();
	}
	
	/*
	 * Get all gear associated with this character.
	*/
	public function get_gear()
	{
		return $this->nodes->where('type', 'item')->get();
	}

	public function sort_bonusses($a, $b)
	{
		$asort = $a->type . ' ' . $a->value . ', type: ' . $a->bonustype . ', from: ' . $a->source;
		$bsort = $b->type . ' ' . $b->value . ', type: ' . $b->bonustype . ', from: ' . $b->source;

		return strcmp($asort, $bsort);						
	} 
}

/* End of file character.php */
/* Location: ./application/models/character.php */
