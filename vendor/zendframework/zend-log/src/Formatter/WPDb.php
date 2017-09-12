<?php
/**
 * Zend formatter for WPDb writer 
 *
 * formats the $event to be able to be inserted to wp_posts table 
 * 
 * @link      
 * @copyright 
 * @license   
 */

namespace Zend\Log\Formatter;

use DateTime;
use Traversable;

class WPDb implements FormatterInterface
{
    /**
     * Format specifier for DateTime objects in event data (default: ISO 8601)
     *
     * @see http://php.net/manual/en/function.date.php
     * @var string
     */
    protected $dateTimeFormat = self::DEFAULT_DATETIME_FORMAT;

    /**
     * Class constructor
     *
     * @see http://php.net/manual/en/function.date.php
     * @param null|string $dateTimeFormat Format specifier for DateTime objects in event data
     */
    public function __construct($dateTimeFormat = null)
    {
        if ($dateTimeFormat instanceof Traversable) {
            $dateTimeFormat = iterator_to_array($dateTimeFormat);
        }

        if (is_array($dateTimeFormat)) {
            $dateTimeFormat = isset($dateTimeFormat['dateTimeFormat'])? $dateTimeFormat['dateTimeFormat'] : null;
        }

        if (null !== $dateTimeFormat) {
            $this->setDateTimeFormat($dateTimeFormat);
        }
    }

    /**
     * Formats data to be written by the writer.
     *
     * @param array $event event data
     * @return array
     */
    public function format($event)
    {
        global $post;

        //clone the passed $events //
        $wp_event = $event;

        //empty the $event to return, we will be creating a new event to this container //
        $event = [];

        /**
         * Create the timestamp to use, timestamp will be saved as post_date to match WPDB writer
         */
        if (isset($wp_event['timestamp']) && $wp_event['timestamp'] instanceof DateTime) 
            $event[ 'post_date' ] = $wp_event['timestamp']->format($this->getDateTimeFormat());
        
        /**
         * Create the post_title to use, the tile can be checked from extra[ 'title']
         * or if WP_Event processor is used, check extra['postTitle']
         */
        if( !empty( $wp_event[ 'extra' ][ 'postTitle' ] ) )
            $event[ 'post_title' ] = $wp_event[ 'extra' ][ 'postTitle' ];
        

        //copy the message index to post_content //
        $event[ 'post_content' ] = $wp_event[ 'message' ];

        //copy the priorityName to log_type, this will serve as post term //
        $event[ 'log_type' ] = $wp_event[ 'priorityName' ];

        if( !empty( $wp_event[ 'extra' ][ 'postID' ] ) )
            $event[ 'post_parent' ] = $post->ID;

        
        if( isset( $wp_event[ 'extra' ][ 'meta' ] ) )
            $event[ 'extra' ][ 'meta' ] = $wp_event[ 'meta' ];
        
        $event[ 'post_title' ] = !empty( $event[ 'post_title' ] ) ?: wp_trim_words( $event[ 'post_content' ], 33, '...' );


        return $event;
    }

    public function deformat( $event ){
        return $event;
    }

    /**
     * {@inheritDoc}
     */
    public function getDateTimeFormat()
    {
        return $this->dateTimeFormat;
    }

    /**
     * {@inheritDoc}
     */
    public function setDateTimeFormat($dateTimeFormat)
    {
        $this->dateTimeFormat = "r";
        return $this;
    }
}
