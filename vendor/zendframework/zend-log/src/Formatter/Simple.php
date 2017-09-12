<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Formatter;

use Traversable;
use Zend\Log\Exception;
use DateTime;

class Simple extends Base
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
     * @param null|string $format Format specifier for log messages
     * @param null|string $dateTimeFormat Format specifier for DateTime objects in event data
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($format = null, $dateTimeFormat = null)
    {
        if ($format instanceof Traversable) {
            $format = iterator_to_array($format);
        }

        if (is_array($format)) {
            $dateTimeFormat = isset($format['dateTimeFormat'])? $format['dateTimeFormat'] : null;
        }

        if (isset($format) && !is_string($format)) {
            throw new Exception\InvalidArgumentException('Format must be a string');
        }

        parent::__construct($dateTimeFormat);

    }

    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param array $event event data
     * @return string formatted line to write to the log
     */
    public function format($events)
    {
        if (isset($events['timestamp']) && $events['timestamp'] instanceof DateTime) {
            $events['timestamp'] = $events['timestamp']->format($this->getDateTimeFormat());
        }

        return $this->formatOutput( $events );
    }

    protected function formatOutput( $events, $level = null ){
        
        if( empty( $events ) ) return; //bail if the $event is empty //

        $output = []; //markup for the logg //

        if( empty( $level ) ) $level = 0; 

        $level++; //indicator showing how many time the function is called //

        if( $level <= 1 ) $output = $this->formatHeading( $events );
        
        $output[] = PHP_EOL;

        foreach( $events as $key => $event ){
            if( $level > 1 ) $output[] = "\t";

            if( is_string( $key ) )$output[] = "\t[ $key ]: ";
            elseif( is_int( $key ) ) $output[] = "\t( $key )";

            if( is_scalar( $event ) ){
                $output[] = "\t\t\t\t";
                if( $level > 1 ) $output[] = " ( ".gettype( $event )." ) ";
                $output[] = wordwrap( $event, 60, "\n" ).PHP_EOL;

            } elseif( is_array( $event ) ){
                if( !empty( $event ) ) $output[] = $this->formatOutput( $event, $level );

            } elseif( is_object( $event ) ){
                $output[] = $this->normalize( $event ).PHP_EOL;
            }
        }

        if( $level <= 1 ) $output[] = PHP_EOL."==========================".PHP_EOL.PHP_EOL;

        return implode( " ", $output );
    }

    protected function normalize( $data ){
        $data = parent::normalize( $data );
        return $data;
    }

    protected function formatHeading( $events ){
        $heading = [];
        $heading[] = "======================".PHP_EOL;
        $heading[ "title" ] = sprintf( 'LOG STARTED: %1$s (%2$s)!!!: logged %3$s', 
                                $events[ "priorityName" ], 
                                $events[ "priority" ], 
                                PHP_EOL
                            );
        if( function_exists( "apply_filters" ) ) $heading = apply_filters( "wp_simple_formatter_heading", $heading );
        return $heading;

    }

    public function setDateTimeFormat( $format ){
        $this->dateTimeFormat = (string) $format;
    }

    public function getDateTimeFormat(){
        return $this->dateTimeFormat;
    }
}
