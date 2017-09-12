<?php
/**
 * Zend Log Writer for Pippings WP_Logging 
 *
 * this writer writes to WP posts table 
 * the DB writing and reading are handled by Pippins WP_Logging 
 * @see 
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Writer;

use Zend\Log\Logger;
use Zend\Log\WPlogging\Wp_Logging;
use Zend\Log\Formatter\WPDb as WPFormatter;

class WPDb extends AbstractWriter
{
    /**
     * Db adapter instance
     *
     * @var Adapter
     */
    protected $handler;

    /**
     * events that have been logged in the DB
     *
     *@var: array
     */
    private $events = [];

    /**
     * Constructor
     * pass the WP_Logging class which will handle database writing and reading 
     *
     */
    public function __construct( WP_Logging $wp_logger )
    {
        //set database handler //
        $this->handler = $wp_logger;

        //filter for WP_Logging to add Zend\Log priorities //
        add_filter( 'wp_log_types', array( $this, 'add_logger_priorities' ) );
    }

    /**
     * Remove reference to database adapter
     *
     * @return void
     */
    public function shutdown()
    {
        
    }

    /**
     * Write a message to the log.
     *
     * @param array $event event data
     * @return void
     * @throws Exception\RuntimeException
     */
    protected function doWrite(array $event)
    {
        $this->formatter = new WPFormatter();

        $events = $this->formatter->format($event);

        $extra = [];
        
        if( isset( $events['extra'] ) ){
            $extra = $events['extra'];
        }

        $this->handler->insert_log( $events, $extra );
    }

    /**
     * Add Zend\Log\Logger priorities to WP_Logging log_type
     * these log_types are a WP_Term for categorizing the logs saved in the WP_DB
     * @param: $types   array   default wp_log_types
     *@return: $types   array   added wp_log_types
     */
    public function add_logger_priorities( $types ){
        $logger = new Logger();
        $priorities = $logger->priorities;
        if( empty( $priorities ) ) return $types;
        foreach( $priorities as $priority ){
            array_push( $types, $priority  );
        } 
        return $types;
    }

    function getErrorEvents(){
        return $this->events;
    }

   
}
