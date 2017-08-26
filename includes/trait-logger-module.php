<?php
/**
 *
 * A logger module trait that provides methods for adding, getting and running modules 
 *
 *
 */

namespace includes;

trait Trait_Logger_Module{
	/**
	 * Add modules that will extend the $logger
	 * each module is an instance of includes\Logger_module
	 * modules can directly call $logger methods e.g. addWriter etc
	 *@return 	Wp_Error_Logger 
	 */
	public function addModule( Abstract_Logger_Module $module ){
		$this->modules[ $module->toString() ] = $module;
		return $this;
	}

	public function getModule( Abstract_Logger_Module $module = null ){
		
		if( empty( $module ) ) return $this->modules; //return array of all modules if none is passed //

		if( !in_array( $module, $this->modules ) && !array_key_exists( $module->toString(), $this->modules ) )
			throw new \Exception( __( "Module cannot be found", "abbey-wp-error" ) );
		
		return $this->modules[ $module->toString() ];
	} 

	public function runModule( Abstract_Logger_Module $module = null ){

	}
}