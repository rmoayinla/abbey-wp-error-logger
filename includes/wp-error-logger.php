<?php
/**
 * 
 * An Error logger class for Wordpress 
 * This class extends the Zend\Log\Logger class, the actual logging logic is performed in Zend\Log\Logger
 * 
 */
namespace includes;

if( !class_exists( "Zend\Log\Logger" ) ) wp_die( __( "This plugin requires Zend logger", "abbey-wp-error" ) );

if( class_exists( "WP_Error_Logger" ) ) return;

use \Zend\Log;
use \Zend\Log\Logger; 
use \Zend\Log\Writer\Stream;
use \Zend\Log\Formatter\JsonFormatter;


//date_default_timezone_set( "Africa/Lagos" );



class WP_Error_Logger implements Module_Interface{

	use Trait_Logger_Module; //import the Logger module trait //

	/**
	 * An instance of the logger class that will be used 
	 * since this class depends on Zend\Log\Logger, the logger is an instance of Zend\Log\Logger 
	 *
	 *@var null
	 */
	private $logger = null;

	/**
	 * Modules that will be extending the $logger class
	 * this class WP_Error_Logger only provides an interface with the $logger class
	 * module classes can change and set values from $logger class
	 * module classes must be an instance of includes\Logger_Module
	 *
	 *@var array 
	 */
	protected $modules = [];

	/**
	 * Instantiate the class and pass the $logger class
	 *@param 	$logger 	Zend\Log\Logger 
	 */
	public function __construct( Log\LoggerInterface $logger ){
		$this->logger = $logger;
	}

	/**
	 * Return an instance of the logger object being used 
	 *@return $logger 		an instance of  Zend\Log\Logger
	 */ 
	public function getLogger(){
		return $this->logger;
	}

	/**
	 * Alias method for the $logger->log method 
	 * events/errors can be logged by calling the WP_Error_Logger log method with just a message
	 * events/erros will only log when writers has been added before calling this function 
	 *@return: $this 		an instance of WP_Error_Logger 
	 */
	public function log( $message, $metas = [], $priority = 1 ){
		$this->getLogger()->log( $priority, $message, $metas );
		return $this;
	}

	/**
	 * Set the logger to null, calling the logger destroy method 
	 */
	public function __destroy(){
		$this->logger = null;
	}

	
}
