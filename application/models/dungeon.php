<?php

/**
 * Dungeon DataMapper Model
 *
 * @author		Simon Kort
 * @link		http://www.campaigncodex.com
 */
class Dungeon extends DataMapper {
	// Uncomment and edit these two if the class has a model name that
	//   doesn't convert properly using the inflector_helper.
	// var $model = 'dungeon';
	public $table = 'dungeon';

	// You can override the database connections with this option
	// var $db_params = 'db_config_name';

	// --------------------------------------------------------------------
	// Relationships
	//   Configure your relationships below
	// --------------------------------------------------------------------

	// Insert related models that Dungeon can have just one of.
	public $has_one = array();

	// Insert related models that Dungeon can have more than one of.
	public $has_many = array('floor');

	/* Relationship Examples
	 * For normal relationships, simply add the model name to the array:
	 *   $has_one = array('user'); // Dungeon has one User
	 *
	 * For complex relationships, such as having a Creator and Editor for
	 * Dungeon, use this form:
	 *   $has_one = array(
	 *   	'creator' => array(
	 *   		'class' => 'user',
	 *   		'other_field' => 'created_dungeon'
	 *   	)
	 *   );
	 *
	 * Don't forget to add 'created_dungeon' to User, with class set to
	 * 'dungeon', and the other_field set to 'creator'!
	 *
	 */
	 
	// Validation
	public $validation = array();

	// Default Ordering
	public $default_order_by = array('name', 'id' => 'desc');

	/**
	 * Constructor: calls parent constructor
	 */
    public function __construct($id = NULL)
	{
		parent::__construct($id);
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
}

/* End of file dungeon.php */
/* Location: ./application/models/dungeon.php */
