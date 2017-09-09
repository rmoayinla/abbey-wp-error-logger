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
		
		if( empty( $module ) ) return array_shift( $this->modules ); //return the first module if none is passed //

		if( !in_array( $module, $this->modules ) && !array_key_exists( $module->toString(), $this->modules ) )
			throw new \Exception( __( "Module cannot be found", "abbey-wp-error" ) );
		
		return $this->modules[ $module->toString() ];
	} 

	/**
	 * Run added modules or an indivdual module
	 * Modules are run just by calling the run method on the module object 
	 */
	public function runModule( Abstract_Logger_Module $module = null ){
		$module_to_run = null;
		if( !empty( $module ) ){
			if( $module_to_run = $this->getModule( $module ) )
				return $module_to_run->run( $this->logger );
			return;
		}
		
		if( empty( $this->modules ) || !is_array( $this->modules ) ) 
			throw new \Exception( __( "Module have not been added yet", "abbey-wp-error" ) );
		
		foreach( $this->modules as $modules ){
			$modules->run( $this->logger );
		}
	}

}