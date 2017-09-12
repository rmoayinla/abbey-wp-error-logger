<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Formatter;


use Exception;
use DateTime;

class JsonFormatter implements FormatterInterface
{



    /**
     * Format specifier for DateTime objects in event data (default: ISO 8601)
     *
     * @see http://php.net/manual/en/function.date.php
     * @var string
     */
    protected $dateTimeFormat = self::DEFAULT_DATETIME_FORMAT;



    public function __construct( $options = null ){

        if( empty( $options ) ) return;

        if( !empty(  $options[ "datetimeformat" ] ) ) $this->dateTimeFormat = $options[ "datetimeformat" ];
    }
    /**
     * Formats data into a single line to be written by the writer.
     *
     * @param array $event event data
     * @return string formatted line to write to the log
     */
    public function format($event)
    {

        if (isset($event['timestamp']) && $event['timestamp'] instanceof DateTime) {
            $event['timestamp'] = $event['timestamp']->format($this->getDateTimeFormat());
        }

        $json[ $event[ "timestamp" ] ] = $event;

        return @json_encode( $json, 
                            JSON_UNESCAPED_SLASHES | 
                            JSON_UNESCAPED_UNICODE | 
                            JSON_NUMERIC_CHECK | 
                            JSON_PRESERVE_ZERO_FRACTION | 
                            JSON_PRETTY_PRINT
                            ).","; //add a comma to every formatted JSON //

    }

    /**
     * Deformat/Re-arrange the logged events for display
     * format method style the logged events for saving, this method style for displaying 
     * since this format encode to json, this method decode from json to arrays 
     *@return   array       a json decoded array 
     *@edited   this method is only added in my version 
     *@author:  Rabiu Mustapha 
     */
    public function deformat( $event ){

        /**
         * I just want to decode the event from json to php array, but it isnt that easy
         * based on how the files are saved, there are whitespaces and new lines which will render the json invalid
         * so we do some cleaning on the json before decoding 
         */
        //first, remove new lines and a trailing comma //
        $event = rtrim( trim( $event ), "," );

        //remove all white spaces before or after curly braces //
        $event = preg_replace( [ '/(\s})/','/(}\s)/','/(\s{)/','/({\s)/' ], [ '}', '}', '{', '{' ], $event );

        //replace }},{ with }, //
        $event = preg_replace( '/(\s}},\s{)/', '},', $event );
        
        //remove invalid characters and decode //
        return json_decode( preg_replace('/[\x00-\x1F\x80-\xFF\n\r]/', '',  $event ), true );
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
        $this->dateTimeFormat = (string) $dateTimeFormat;
        return $this;
    }

    /**
     * Normalizes given $data.
     *
     * @param mixed $data
     *
     * @return mixed
     */
    protected function normalize($data, $depth = 0)
    {
        if ($depth > 9) {
            return 'Over 9 levels deep, aborting normalization';
        }
        if (is_array($data) || $data instanceof \Traversable) {
            $normalized = [];
            $count = 1;
            foreach ($data as $key => $value) {
                if ($count++ >= 1000) {
                    $normalized['...'] = 'Over 1000 items, aborting normalization';
                    break;
                }
                $normalized[$key] = $this->normalize($value, $depth + 1);
            }
            return $normalized;
        }
        if ($data instanceof Exception) {
            return $this->normalizeException($data);
        }
        return $data;
    }

    /**
     * Normalizes given exception with its own stack trace
     *
     * @param \Throwable $e
     *
     * @return array
     */
    protected function normalizeException(\Throwable $e)
    {
        $data = [
            'class' => get_class($e),
            'message' => $e->getMessage(),
            'code' => $e->getCode(),
            'file' => $e->getFile().':'.$e->getLine(),
        ];
        $trace = $e->getTrace();
        foreach ($trace as $frame) {
            if (isset($frame['file'])) {
                $data['trace'][] = $frame['file'].':'.$frame['line'];
            } elseif (isset($frame['function']) && $frame['function'] === '{closure}') {
                // We should again normalize the frames, because it might contain invalid items
                $data['trace'][] = $frame['function'];
            } else {
                // We should again normalize the frames, because it might contain invalid items
                $data['trace'][] = $this->normalize($frame);
            }
        }
        if ($previous = $e->getPrevious()) {
            $data['previous'] = $this->normalizeException($previous);
        }
        return $data;
    }
}
