<?php
namespace modules;

use \includes\Abstract_Logger_Module;
use \Zend\Log\LoggerInterface;
use \Zend\Log\Formatter\JsonFormatter;

class Json_Formatter extends Abstract_Logger_Module{
	
	/**
	 * The active formatter to use by writers
	 * @var: object|string 
	 */
	protected $formatter = null; 

	public function __construct( $formatter= null ){
		$this->formatter = $formatter;
	}

	/**
	 * Run the module methods, all classes that extend Abstract_Logger_Module must have a run method
	 * set the logger and add a formatter for our writers or for a particular writer 
	 */
	public function run( LoggerInterface $logger ){
 		$this->setLogger( $logger );
 		
 		if( empty( $this->formatter ) ){ //if there is no formatter added yet //
 			$this->formatter = new JsonFormatter(); //set a default formatter//
 			$this->addFormatter( $this->formatter ); //call the addFormatter method //
 		} 
 		
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
 	public function addFormatter( $formatter, $writer = null, $options = [] ){

 		if( empty( $this->getWriters() ) ) //throw exception if there is no writer //
 			throw new \Exception( __( "No writer added yet", "abbey-wp-error" ) );
 		
 		$this->formatter = $formatter; //set the formatter as the active formatter //

 		if( !empty( $writer ) ){ // if a writer is passed //
 			//get the writer instance from string or object //
 			$writer = is_string( $writer ) ? $this->writerPlugin( $writer, $options ) : $writer;

 			//if the $writer is already added to the logger, set formatter to this formatter //
 			if( in_array( $writer, $this->getWriters ) ) $writer->setFormatter( $formatter, $options );

 			return $this; //make this class chainable //
 		}
 		/** If no writer is passed, just loop through all writers and add it */
 		foreach( $this->getWriters() as $writers ){
 			$writers->setFormatter( $formatter, $options );
 		}

 		return $this; //make this class chainable 

 	}


}