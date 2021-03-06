<?php
/**
 * This file contains the user class
 * @author Tommy Teasdale <tteasdaleroads@gmail.com>
 * @package apine-framework
 * @subpackage system
 */

/**
 * Implementation of the database representation of users
 */
class ApineUser extends ApineEntityModel{

	/**
	 * User identifier in database
	 * @var integer
	 */
	protected $id;

	/**
	 * Username
	 * @var string
	 */
	protected $username;

	/**
	 * User encrypted password
	 * @var string
	 */
	protected $password;

	/**
	 * User permissions
	 * @var integer
	 */
	protected $type;
	
	/**
	 * User Group
	 * @var Liste[ApineUserGroup]
	 */
	protected $group;

	/**
	 * User email address
	 * @var string
	 */
	protected $email_address;

	/**
	 * Registration date's timestamp
	 * @var string
	 */
	protected $register_date;

	/**
	 * User class' constructor
	 * @param integer $a_id
	 *        User identifier
	 */
	public function __construct($a_id = null){

		$this->_initialize('apine_users', $a_id);
		if(!is_null($a_id)){
			$this->id = $a_id;
		}
	
	}

	/**
	 * Fetch user's identifier
	 * @return integer
	 */
	public function get_id(){

		if($this->loaded == 0){
			$this->load();
		}
		return $this->id;
	
	}

	/**
	 * Set user's id
	 * @param integer $a_id
	 *        User's identifier
	 */
	public function set_id($a_id){

		$this->id = $a_id;
		$this->_set_id($a_id);
		$this->_set_field('ID', $a_id);
	
	}

	/**
	 * Fetch user's username
	 * @return string
	 */
	public function get_username(){

		if($this->loaded == 0){
			$this->load();
		}
		return $this->username;
	
	}

	/**
	 * Set user's username
	 * @param string $a_name
	 *        User's username
	 */
	public function set_username($a_name){

		if($this->loaded == 0){
			$this->load();
		}
		$this->username = $a_name;
		$this->_set_field('username', $a_name);
	
	}

	/**
	 * Fetch user's encrypted password
	 * @return string
	 */
	public function get_password(){

		if($this->loaded == 0){
			$this->load();
		}
		return $this->password;
	
	}

	/**
	 * Set user's encrypted password
	 * @param string $a_pass
	 *        User's password
	 */
	public function set_password($a_pass){

		if($this->loaded == 0){
			$this->load();
		}
		$this->password = $a_pass;
		$this->_set_field('password', $a_pass);
	
	}

	/**
	 * Fetch user's permission level
	 * @return integer
	 */
	public function get_type(){

		if($this->loaded == 0){
			$this->load();
		}
		return $this->type;
	
	}

	/**
	 * Set user's permission level
	 * @param integer $a_type
	 *        User's permissions
	 */
	public function set_type($a_type){

		if($this->loaded == 0){
			$this->load();
		}
		$this->type = $a_type;
		$this->_set_field('type', $a_type);
	
	}
	
	/**
	 * Fetch user's group
	 * @return Group
	 */
	public function get_group(){
	
		if($this->loaded == 0){
			$this->group=ApineUserGroupFactory::create_by_user($this->id);;
		}
		return $this->group;
	
	}
	
	/**
	 * Set user's group
	 * @param <Liste[ApineUserGroup]> $a_group_list
	 *        List of User's groups
	 */
	public function set_group($a_group_list){
		
		if($this->loaded == 0){
			$this->load();
		}
	
		if(get_class($a_group_list) == 'Liste'){
			$valid=true;
			foreach ($a_group_list as $item){
				if(!get_class($item)=='ApineUserGroup'){
					$valid=false;
				}
			}
			if($valid){
				$this->group = $a_group_list;
			}
		}
	
	}

	/**
	 * Fetch user's email address
	 * @return string
	 */
	public function get_email_address(){

		if($this->loaded == 0){
			$this->load();
		}
		return $this->email_address;
	
	}

	/**
	 * Set user's email address
	 * @param string $a_email
	 *        User's email address
	 */
	public function set_email_address($a_email){

		if($this->loaded == 0){
			$this->load();
		}
		$this->email_address = $a_email;
		$this->_set_field('email', $a_email);
	
	}

	/**
	 * Fetch user's registration date
	 * @return string
	 */
	public function get_register_date(){

		if($this->loaded == 0){
			$this->load();
		}
		return date(Config::get('dateformat', 'datehour'), strtotime($this->register_date));
	
	}

	/**
	 * Set user's registration date
	 * @param string $a_timestamp
	 *        User's registration date
	 */
	public function set_register_date($a_timestamp){

		if($this->loaded == 0){
			$this->load();
		}
		$this->register_date = $a_timestamp;
		$this->_set_field('register', $a_timestamp);
	
	}
	
	/**
	 * @see ApineEntityInterface::load()
	 */
	public function load(){

		if(!is_null($this->id)){
			$this->username = $this->_get_field('username');
			$this->password = $this->_get_field('password');
			$this->type = $this->_get_field('type');
			$this->email_address = $this->_get_field('email');
			$this->register_date = $this->_get_field('register');
			$this->loaded = 1;
		}
	
	}

	/**
	 * @see ApineEntityInterface::save()
	 */
	public function save(){

		parent::_save();
		$this->set_id($this->_get_id());
		
		$db=new Database();
		$db->delete('apine_users_user_groups', array("id_user"=>$this->get_id()));
		
		foreach ($this->goup as $item){
			$db->insert('apine_users_user_groups', array("id_user"=>$this->get_id(), "id_group"=>$item->get_id()));
		}
	}

	/**
	 * @see ApineEntityInterface::delete()
	 */
	public function delete(){
		
		if($this->loaded == 0){
			$this->load();
		}
		
		$db=new Database();
		$db->delete('apine_users_user_groups', array("id_user"=>$this->get_id()));
		
		parent::_destroy();
	}

}
?>
