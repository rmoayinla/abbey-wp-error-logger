<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zend-log for the canonical source repository
 * @copyright Copyright (c) 2005-2016 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Log\Processor;

class WPEvent implements ProcessorInterface
{
    
    protected $ID = "";
    /**
     * Adds some wordpress info to the logged data
     * added infor are: post ID, request, post_type, query string, post title 
     * @param array $event event data
     * @return array event data
     */
    public function process(array $event)
    {
        global $wp_query, $post, $authordata, $wp;

        $current_user = null; 
        if( function_exists( 'wp_get_current_user' ) ) $current_user = wp_get_current_user();

        if (!isset($event['extra'])) {
            $event['extra'] = [];
        }

        if( !empty( $post ) ){
            $event['extra']['postID'] = $post->ID;
            $event['extra']['postType'] = $post->post_type;
            $event['extra']['postTitle'] = $post->post_title;
            $event[ 'extra' ][ 'permalink' ] = get_permalink();
        }

        if( !empty( $wp ) ){
            $event[ 'extra' ][ 'query_string' ] = $wp->query_string;
            $event[ 'extra' ][ 'request' ] = $wp->request;
        }
        
        if( !empty( $wp_query ) && $wp_query->is_archive ){
            $queried_object = get_queried_object();
            $event['extra']['archive'] = sprintf( '%1$s Archive: %2$s', 
                                                    $queried_object->name,
                                                    get_class( $queried_object )   
                                                );
        }

        if( $current_user instanceof \WP_User && is_user_logged_in() ){
            $event[ 'extra' ][ 'userName' ] = $current_user->user_login;
            $event[ 'extra' ][ 'userEmail' ] = $current_user->user_email;
            $event[ 'extra' ][ 'userID' ] = $current_user->ID;
        }

        if( empty( $post ) ){
            $url = home_url( add_query_arg( null, $wp->request ) );
            $event['extra']['postID'] = url_to_postid( $url );
            $event[ 'extra' ][ 'permalink' ] = $url;
        }

        if( function_exists( "apply_filters" ) ) $event = apply_filters( "Zend\Log\Process\WPEvent", $event );

        return $event;
    }

    

}
