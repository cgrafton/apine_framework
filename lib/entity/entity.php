<?php
/**
 * Entity data mapper and basic entity class declaration
 * 
 * Exemple class for use of the entity data mapper
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage entity
 */

/**
 * This is the implementation of the data mapper
 * design patern.
 * @abstract
 *
 */
abstract class ApineEntityModel implements ApineEntityInterface{

	/**
	 * In-database entity identifier
	 * @var string
	 * @access private
	 */
	private $id;

	/**
	 * Entity Table Name
	 * @var string
	 * @access private
	 */
	private $table_name;

	/**
	 * Does Data mapper has loaded fields and values
	 * @var boolean
	 * @access private
	 */
	private $field_loaded = 0;

	/**
	 * Entity fields and values
	 * @var array
	 * @access private
	 */
	private $database_fields;

	/**
	 * Entity Modified fields and values
	 * @var array
	 * @access private
	 */
	private $modified_fields;

	/**
	 * Has fields been modified
	 * @var boolean
	 */
	private $modified;

	/**
	 * Does Entity has loaded loaded fields and
	 * values
	 * @var boolean
	 */
	protected $loaded = 0;
	// Methods
	/**
	* Fetch database fields and values for entity
	*/
	protected function _load(){

		$this->database_fields = ($this->id !== null)?ApineFactory::get_table_row($this->table_name, $this->id):null;
		$this->database_fields = $this->database_fields[0];
		$this->field_loaded = 1;
		if(sizeof($this->modified_fields) > 0){
			foreach($this->modified_fields as $key=>$values){
				$this->modified_fields[$key] = false;
			}
			$modified = 0;
		}

	}

	/**
	 * Mark entity has loaded
	 */
	protected function _force_loaded(){

		$this->field_loaded = 1;

	}

	/**
	 * Fetch a field's value
	 * @param string $a_field
	 *        Field name
	 * @return mixed
	 */
	protected function _get_field($a_field){
		// Load entity if not loaded yet
		if($this->field_loaded == 0)
			$this->_load();
		return $this->database_fields[$a_field];

	}

	/**
	 * Fetch all fields' names and values
	 * @return array
	 */
	protected function _get_all_fields(){
		// Load entity if not loaded yet
		if($this->field_loaded == 0)
			$this->_load();
		return $this->database_fields;

	}

	/**
	 * Get entity identifier
	 * @return string
	 */
	protected function _get_id(){

		return $this->id;

	}

	/**
	 * Set entity identifier
	 * @param string $id
	 *        Entity identifier
	 */
	protected function _set_id($id){

		$this->id = $id;

	}

	/**
	 * Get entity table name
	 * @return string
	 */
	protected function _get_table_name(){

		return $this->table_name;

	}

	/**
	 * Set entity table name
	 * @param string $table
	 *        Entity table name
	 */
	protected function _set_table_name($table){

		$this->table_name = $table;

	}

	/**
	 * Prepare Data mapper for user
	 * @param string $table_name
	 *        Entity Table name
	 * @param string $tuple_id
	 *        Entity identifier
	 */
	protected function _initialize($table_name, $tuple_id = null){

		$this->table_name = $table_name;
		$this->id = $tuple_id;

	}

	/**
	 * Fetch all fields' names and values
	 * @param string $field
	 *        Field name
	 * @param mixed $value
	 *        Field value
	 */
	protected function _set_field($field, $value){

		if($this->field_loaded == 0)
			$this->_load();
		$this->database_fields[$field] = $value;
		$this->modified = 1;
		$this->modified_fields[$field] = true;

	}

	/**
	 * Delete Entity from database
	 */
	protected function _destroy(){

		if($this->id){
			Factory::remove_table_row($this->table_name, array(
							'ID' => $this->id
			));
		}

	}

	/**
	 * Save Entity state to database
	 */
	protected function _save(){

		if($this->id === null){
			$this->field_loaded = 0;
		}
		if($this->field_loaded == 0){
			// This is a new entity
			$new_dbf = array();
			foreach($this->database_fields as $field=>$val){
				if(!is_numeric($field)){
					$new_dbf[$field] = $val;
				}
			}
			if(sizeof($new_dbf) > 0){
				$this->id = ApineFactory::set_table_row($this->table_name, $new_dbf);
			}
			/*$this->field_loaded = 1;
			 $this->loaded = 1;*/
			$this->_load();
		}else{
			// This is an already existing entity
			$arUpdate = array();
			foreach($this->database_fields as $key=>$value){
				if(!is_numeric($key)){
					if(isset($this->modified_fields[$key]) && $this->modified_fields[$key] == true){
						if($value == ""){
							$arUpdate[$key] = NULL;
						}else{
							$arUpdate[$key] = $value;
						}
					}
				}
			}
			ApineFactory::update_table_row($this->table_name, $arUpdate, array(
							'ID' => $this->id
			));
		}

	}

}


class ApineEntity extends ApineEntityModel{

	/**
	 * Entity constructor
	 * @param string $a_table
	 *        The table name on which the entity
	 *        is saved on
	 * @param string $a_id
	 *        identifier of the entity in the
	 *        table
	 */
	public function __construct($a_table = null, $a_id = null){

		if($a_table != null){
			$this->_initialize($a_table, $a_id);
			$this->loaded = true;
		}
	
	}

	/**
	 * Initialize the entity
	 * @param string $a_table        
	 * @param string $a_id        
	 */
	public function initialize($a_table, $a_id = null){

		if(!$this->loaded){
			$this->_initialize($a_table, $a_id);
		}else{
			if($a_id != null){
				$this->_set_id($a_id);
			}
			$this->_set_table_name($a_table);
		}
	
	}

	/**
	 * @see ApineEntityModel::_get_id()
	 */
	public function get_id(){

		return $this->_get_id();
	
	}

	/**
	 * @see ApineEntityModel::_set_id()
	 * @param string $a_id
	 *        Entity Identifier
	 */
	public function set_id($id){

		$this->_set_id($id);
	
	}

	/**
	 *
	 * @see ApineEntityModel::_get_field()
	 * @param string $a_field        
	 * @return mixed
	 */
	public function get_field($a_field){

		if($a_table != null){
			return $this->_get_field($a_field);
		}
	
	}

	/**
	 *
	 * @see ApineEntityModel::_set_field()
	 * @param string $a_field
	 *        Field Name
	 * @param mixed $a_value
	 *        Field Value
	 */
	public function set_field($a_field, $a_value){

		if($a_table != null){
			return $this->_set_field($a_field, $a_value);
		}
	
	}

	/**
	 *
	 * @see ApineEntityInterface::load()
	 */
	public function load(){

		$this->_load;
	
	}

	/**
	 *
	 * @see ApineEntityInterface::save()
	 */
	public function save(){

		$this->_save();
	
	}

	/**
	 *
	 * @see ApineEntityInterface::delete()
	 */
	public function delete(){

		$this->_destroy();
	
	}

}

