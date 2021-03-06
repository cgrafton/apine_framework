<?php

class URL_Helper{
	
	private static $_instance;
	/**
	 * Server Domain Name
	 * @var string
	 */
	private $session_server;
	
	/**
	 * Path on the server
	 * @var string
	 */
	private $session_server_path;
	
	/**
	 * Script's path on the server
	 * @var string
	 */
	private $session_current_path;
	
	/**
	 * Script's name
	 * @var string
	 */
	private $session_current;
	
	/**
	 * Main Server's Domain Name
	 * @var string
	 */
	private $main_session_server;
	
	private function __construct(){
		
		// Set server address
		$protocol = (Request::is_https())?'https://':'http://';
		$this->session_server = $protocol . $_SERVER['SERVER_NAME'];
		$ar_domain = explode('.', $_SERVER['SERVER_NAME']);
		if(count($ar_domain) >= 3){
			$start = strlen($ar_domain[0]) + 1;
			$this->main_session_server = $protocol . substr($_SERVER['SERVER_NAME'], $start);
		}else{
			$this->main_session_server = $protocol . $_SERVER['SERVER_NAME'];
		}
		
		if((!Request::is_https()&&Request::get_request_port()!=80)||(Request::is_https()&&Request::get_request_port()!=443)){
			$this->session_server.=":".Request::get_request_port();
			$this->main_session_server.=":".Request::get_request_port();
		}
		
		// Set script name
		$this->session_current = $protocol . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'];
		// Set script path
		$this->session_current_path = $protocol . $_SERVER['SERVER_NAME'] . dirname($_SERVER['PHP_SELF']);
		// Set server path
		$this->session_server_path = realpath(dirname(dirname(__FILE__)) . '/..');
		
	}
	
	public static function get_instance(){
		
		if(!isset(self::$_instance)){
			self::$_instance = new static();
		}
		return self::$_instance;
		
	}
	
	/**
	 * Append a path to the current absolute path
	 * @param string $path
	 *        String to append
	 * @return string
	 */
	private static function write_url($path){
	
		return self::get_instance()->session_server . '/' . $path;
	
	}
	
	/**
	 * Append a path to the main domain absolute path
	 * @param string $path
	 *        String to append
	 * @return string
	 */
	private static function write_main_url($path){
	
		return self::get_instance()->main_session_server . '/' . $path;
	
	}
	
	/**
	 * Append a path to the current relative path
	 * @param string $path
	 *        String to append
	 * @return string
	 */
	private static function write_relative_path($path){
	
		return self::get_instance()->session_current_path . '/' . $path;
	
	}
	
	/**
	 * Retrieve the http path to a ressource relative to site's root
	 * @param string $path
	 *        String to append
	 * @param boolean $add_arg
	 *        Whether to add language argument to path
	 * @return string
	 */
	public static function path($path){
		
		return self::write_url($path);
		
	}
	
	/**
	 * Retrieve the http path to a ressource relative to site's main
	 * domains's root
	 * @param string $path
	 *        String to append
	 * @param boolean $add_arg
	 *        Whether to add language argument to path
	 * @return string
	 */
	public static function main_path($path){
		
		return self::write_main_url($path);
	
	}
	
	/**
	 * Retrieve the http path to a ressource relative to current
	 * ressource
	 * @param string $path
	 *        String to append
	 * @param string $add_arg
	 *        Whether to add language argument to path
	 * @return string
	 */
	public static function relative_path($path){
	
		return self::write_relative_path($path);
	
	}
	
	/**
	 * Get current absolute path
	 * @return string
	 */
	public static function get_current_path(){
	
		return self::get_instance()->session_current;
	
	}
	
	/**
	 * Get current absolute server path
	 * @return string
	 */
	public static function get_server_path(){
	
		return self::get_instance()->session_server_path;
	
	}
}