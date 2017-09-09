<?php
/**
 *
 * Plugin Name: Abbey WP Error
 * Description: A wordpress plugin for handling WP core, plugin and theme errors, handles how errors are logged and displayed
 * Version: 0.1.1
 * Author: Rabiu Mustapha 
 * Text Domain: abbey-wp-error
 *
*/

//exit with an error message if this page is accessed directly or not within wordpress //
if( !defined( 'ABSPATH' ) )	
	wp_die( __( "<p>This page cannot be accessed directly.</p>", "abbey-wp-error" ) );

/**
 * Define constant that stores the plugin directory 
 */
if ( !defined( 'ABBEY_WP_ERROR_PLUGIN_DIR' ) ) 
	define( 'ABBEY_WP_ERROR_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

/**
 * Define constant that stores the plugin url 
 */
if ( !defined( 'ABBEY_WP_ERROR_PLUGIN_URL' ) ) 
	define( 'ABBEY_WP_ERROR_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

/**
 * Include the autoloader for zend library and other libraries from composer 
 */
require ABBEY_WP_ERROR_PLUGIN_DIR ."vendor/autoload.php";

/**
 * Include the autoloader for the plugin classes 
 */
require ABBEY_WP_ERROR_PLUGIN_DIR ."autoload.php";


/**
 * Initialize the globbal variables 
 * these variables store the logger objects and can be used in other plugins
 */
global $abbey_error_logger, $abbey_logger;

/** 
 * instantiate the plugin error logger class and pass the Zend logger as a dependency 
 * @see: includes/wp-error-logger.php
 */
	$abbey_logger = new includes\WP_Error_Logger( new Zend\Log\Logger() );

/**
 * Get and store the main zend logger object in a variable 
 * the zend logger methods i.e. log, addWriter are done on this var
 * @see: vendor/zend/log/logger.php
 */
	$abbey_error_logger = $abbey_logger->getLogger();



/**
 * Add default modules for the plugin logger
 * Note: modules does not apply to zend logger but only to WP_Error_Logger
 */
	// a default writer module, adds a Mock writer by default //
	$writer_module = new modules\Writer_Module();

	//add additional stream writer to the module //
	$writer_module->addWriter( "stream", array( "stream" => ABBEY_WP_ERROR_PLUGIN_DIR."log.txt" )  );

	//a default formatter module, this sets format for the stream writer //
	$formatter_module = new modules\Formatter_Module();

	// add a simple formatter //
	$formatter_module->addFormatter( "simple" );

	//set the datetimeformat for the formatters //
	$formatter_module->setDateTimeFormat( 'l jS, F, Y, h:i:s A T' );

/**
 * Add default modules for the logger 
 */
	$abbey_logger->addModule( $writer_module );
	$abbey_logger->addModule( $formatter_module );

	//add additional modules with this hook //
	do_action( "abbey_wp_error_modules", $abbey_logger );

/**
 * Run modules 
 */
	$abbey_logger->runModule();

$abbey_logger->log( "Logged message" );
	



