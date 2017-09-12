<?php
/**
 *
 * A logger module class for adding writers to WP_Logger
 * this class extends Abstact_Logger_Module 
 *@see: includes/Abstract_Logger_Module 	to read the abstract class
 * writers are added and run in the run method, all modules must have a run method
 *@package: WP_Error_Logger wordpress plugin 
 *@category: modules 
 *
 */ 

namespace modules;

use \includes\Abstract_Logger_Module;
use \Zend\Log\LoggerInterface;

 class Writer_Module extends Abstract_Logger_Module{

 	/**
 	 * A writer container to store added writers 
 	 * writers that are added can be an object or a string 
 	 *@var: array 
 	 */
 	protected $writers = [];
 	
 	public function __construct( $writer = null, $options = array() ){
 		
 		if( !empty( $writer ) ) $this->addWriter( $writer, $options );
 		
 		//add a default mock writer to log events, //
 		// so that events will always be logged even when writer is not added//
 		$this->addWriter( "mock", array() );	
 	}

 	/**
 	 * Add writers to the writers array 
 	 * @return 	$this	instance of writer_module 
 	 * @param: 	string|object 	$writer 	writer name or instance of writer class
 	 * 			array 			$options 	array of options for the writer class
 	 */
 	public function addWriter( $writer, $options= [] ){
 		$writer_name = is_object( $writer ) ? get_class( $writer ) : $writer;
 		$this->writers[ $writer_name ] = [ "writer" => $writer, "options" => $options ];
 		return $this;
 	}

 	/**
 	 * Private method for adding writers to the Logger class 
 	 *@return $this 	instance of Writer_Module 
 	 */
 	private function addToWriter(){

 		if( empty( $this->writers ) ) return;
 			
 		$priority = 10;
 		foreach( $this->writers as $writer ){
 			$this->logger->addWriter( $writer[ "writer" ], $priority, $writer[ "options" ]  );
 			$priority .= 10;
 		}
 		return $this;
 	}

 	
 	/**
 	 * Fire method when this module is run 
 	 * when this module is run, the $logger is set and writers are added 
 	 */
 	public function run( LoggerInterface $logger ){
 		//set the $logger var to the passed $logger object //
 		$this->setLogger( $logger );

 		// call the addToWriter method to add writers to the $logger //
 		$this->addToWriter();
 	}
 }