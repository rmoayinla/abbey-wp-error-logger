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
if( !defined( 'ABSPATH' ) )	wp_die( __( "<p>This page cannot be accessed directly.</p>", "abbey-wp-error" ) );

if ( !defined( 'ABBEY_WP_ERROR_PLUGIN_DIR' ) ) 
	define( 'ABBEY_WP_ERROR_PLUGIN_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );

if ( !defined( 'ABBEY_WP_ERROR_PLUGIN_URL' ) ) 
	define( 'ABBEY_WP_ERROR_PLUGIN_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

require ABBEY_WP_ERROR_PLUGIN_DIR ."vendor/autoload.php";

require ABBEY_WP_ERROR_PLUGIN_DIR ."autoload.php";

global $wp_error_logger;
$logger = new includes\WP_Error_Logger( new Zend\Log\Logger() );
$wp_error_logger = $logger->getLogger();
$wp_error_logger->addWriter( "stream", 1, array( "stream" => ABBEY_WP_ERROR_PLUGIN_DIR."log.txt" ) );

$logger->addModule( new modules\Writer_Module() );
$logger->addModule( new modules\Json_Formatter() );
$logger->getModule( new modules\Writer_Module() )->run( $wp_error_logger );
$logger->getModule( new modules\Json_Formatter() )->run( $wp_error_logger );
$wp_error_logger->log( 1, "Logged message", array() );
print_r( $logger->getModule( new modules\Writer_Module() )->getErrorLogs() ) ;
echo "<br />";
echo json_last_error(); 


exit;
