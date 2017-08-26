<?php

spl_autoload_register( function( $class ){

	$class = str_replace( [ "\\", "_" ], [ "/", "-" ], $class );

	if( file_exists( ABBEY_WP_ERROR_PLUGIN_DIR.$class.".php" ) )
		require ABBEY_WP_ERROR_PLUGIN_DIR.$class.".php";
} );
