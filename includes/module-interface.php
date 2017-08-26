<?php

namespace includes;

interface Module_Interface{
	
	public function addModule( Abstract_Logger_Module $module );

	public function getModule( Abstract_Logger_Module $module );

	public function runModule( Abstract_Logger_Module $module = null );
}