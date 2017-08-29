<?php
namespace includes;

use \Zend\Log\LoggerInterface;

abstract class Abstract_Logger_Module{
	
	/**
	 * An instance of the logger class that will be used to log errors 
	 *@var null
	 */
	protected $logger = null; 

	/**
	 * Abstract method for the class
	 * All classes extending this class must define a run method and what to run 
	 *@param 		$logger 	an instance of the $logger class 
	 */
	abstract function run( LoggerInterface $logger );

	/**
	 * Set the $logger instance 
	 *@param $logger 	an instance of  Logger Interface $logger 
	 *@return $this 	instance of this class 
	 */
	public function setLogger( LoggerInterface $logger ){
		$this->logger = $logger;
		return $this; //make method chainable //
	}

	/**
	 * Return the $logger instance 
	 *@return $logger 		the current logger instance being used
	 */
	public function getLogger(){
		return $this->logger;
	}

	/**
	 * Get all writers that have been added to the logger
	 *@return 	array 		array of writers
	 */
	public function getWriters(){
		return  ( $this->logger->getWriters()->count() > 0 )? $this->logger->getWriters()->toArray() : [];
	}


	/**
	 * Get all writter/logged errors from all writers
	 *@return array 	$log 		multi-dimensional array of logged data 
	 */
	public function getErrorLogs(){

 		if( empty( $this->getWriters() ) ) //throw exception if no writer is added yet //
 			throw new \Exception( "No writers found in Logger" );
 		$log = [];
 		foreach( $this->getWriters() as $writer ){
 			if( !method_exists( $writer, "getErrorEvents" ) ) continue; //skip if the method is not found //
 			$log[] = $writer->getErrorEvents();
 		}
 		return $log;
 	}

	/**
	 * Return a simple string representation of the class
	 *@return string 	the name of the current class
	 */
	public function toString(){
		return get_class( $this );
	}
}