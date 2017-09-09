<?php
namespace modules;

use \includes\Abstract_Logger_Module;
use \Zend\Log\LoggerInterface;
use \Zend\Log\Formatter\JsonFormatter;

class Formatter_Module extends Abstract_Logger_Module{
	
	/**
	 * The active formatter to use by writers
	 * @var: object|string 
	 */
	protected $formatter = null; 

	protected $dateTimeFormat = null;

	public function __construct( $formatter= null, $options = [] ){
		if( !empty( $formatter ) ) $this->addFormatter( $formatter, $options );
	}

	/**
	 * Run the module methods, all classes that extend Abstract_Logger_Module must have a run method
	 * set the logger and add a formatter for our writers or for a particular writer 
	 */
	public function run( LoggerInterface $logger ){
 		
 		$this->setLogger( $logger ); //
 		
 		$this->addToFormatter(); //call the addFormatter method //

 		
 	}

 	/**
 	 * Add formatter for writers 
 	 * Formatter format/style how events are logged and how they are retreieved/displayed
 	 *@see 	Zend\Log\formatter 		to see individual formatters 
 	 *@param: 	$formatter 		string|object 	a formatter instance or string 
 	 			$writer 		string|object 	a writer to add the format to
 	 			$options 		array 			a formatter options 
 	 *@return 	$this 			object 			instance of this class 
 	 *
 	 */
 	private function addToFormatter(){

 		$writers = $formatter = $formatter_writer = null; 
 		$writers = $this->getWriters();
 		if( empty( $writers ) ) //throw exception if there is no writer //
 			throw new \Exception( __( "No writer added yet", "abbey-wp-error" ) );

 		if( empty( $this->formatter ) ) //if there is no formatter added yet //
 			$this->addFormatter( new JsonFormatter() ); //set a default formatter//
 		
 		$formatter = array_shift( $this->formatter ); //set the formatter as the active formatter //

 		if( !empty( $this->dateTimeFormat ) ) {
 			$formatter[ "options" ][ "datetimeformat" ] = $this->dateTimeFormat;
 			if( is_string( $formatter[ "formatter" ] ) ) 
 				$formatter[ "formatter" ] = $this->logger->getWriters()->current()->setFormatter( $formatter[ "formatter" ], [] )->getFormatter();
 			$formatter[ "formatter" ]->setDateTimeFormat( $this->dateTimeFormat );
 		}

 		if( !empty( $formatter[ "writer" ] ) ){ // if a writer is passed //
 			//get the writer instance from string or object //
 			$writer = is_string( $writer ) ? $this->logger->writerPlugin( $writer, $options ) : $writer;

 			//if the $writer is already added to the logger, set formatter to this formatter //
 			if( in_array( $formatter[ "writer" ], $writers ) ) 
 				$writer->setFormatter( $formatter[ "formatter" ], $formatter[ "options" ] );

 			return $this; //make this class chainable //
 		}
 		/** If no writer is passed, just loop through all writers and add it */
 		foreach( $this->getWriters() as $writers ){
 			$writers->setFormatter( $formatter[ "formatter" ], $formatter[ "options" ] );
 		}

 		return $this; //make this class chainable //

 	}

 	/**
 	 * the public method for adding formatter to the class formatter container
 	 * formatter can be added for all writers or for a specific writer only
 	 *@return: $this 		instance of this class 
 	 */
 	public function addFormatter( $formatter, $options = [], $writer = null ){
 		$this->formatter[] = [ "formatter" => $formatter, "options" => $options, "writer" => $writer ];
 		return $this; //make method chainable 
 	}


 	/**
 	 * Set date time format for the logger 
 	 *@return $this 	instance of this class 
 	 */
 	public function setDateTimeFormat( $format ){
 		
 		$this->dateTimeFormat = $format;

 		return $this; //make method chainable //

 	}


}