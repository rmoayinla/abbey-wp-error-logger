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
     * added info are: post ID, request, post_type, query string, post title 
     * @param array $event event data
     * @return array event data
     */
    public function process(array $event)
    {
        global $wp_query, $post, $authordata, $wp; //wordpress global variables //

        $current_user = null; //the current user for this wordpress request //

        if( function_exists( 'wp_get_current_user' ) ) $current_user = wp_get_current_user();

        //add an extra index to the event if not present //
        if (!isset($event['extra'])) 
            $event['extra'] = [];
        
        //if the global $post is not empty, add some post information to the log //
        if( !empty( $post ) ){
            $event['extra']['postID'] = $post->ID; //post ID //
            $event['extra']['postType'] = $post->post_type; // post type e.g. post,page, news etc //
            $event['extra']['postTitle'] = $post->post_title; //post title //
            $event[ 'extra' ][ 'permalink' ] = get_permalink(); //post url //
        }

        // if the global $wp var is not empty, add some request info to the log //
        if( !empty( $wp ) ){
            $event[ 'extra' ][ 'query_string' ] = $wp->query_string; //the query string for the request //
            $event[ 'extra' ][ 'request' ] = $wp->request; //request info //
        }
        
        //if we are on an archive request e.g. author, category, add some archive info to the log //
        if( !empty( $wp_query ) && $wp_query->is_archive ){
            $queried_object = get_queried_object();
            $event['extra']['archive'] = sprintf( '%1$s Archive: %2$s', 
                                                    $queried_object->name,
                                                    get_class( $queried_object )   
                                                );
        }

        //if the user is logged in, add some user info //
        if( $current_user instanceof \WP_User && is_user_logged_in() ){
            $event[ 'extra' ][ 'userName' ] = $current_user->user_login; //current username //
            $event[ 'extra' ][ 'userEmail' ] = $current_user->user_email; //current user email address//
            $event[ 'extra' ][ 'userID' ] = $current_user->ID; //current user ID //
        }

        //if global $post var is not set yet, do some workaround //
        if( empty( $post ) ){
            $url = home_url( add_query_arg( null, $wp->request ) );
            $event['extra']['postID'] = url_to_postid( $url );
            $event[ 'extra' ][ 'permalink' ] = $url;
        }

        //Add a wp filter for the $event being returned, check if we are in wordpress first //
        if( function_exists( "apply_filters" ) ) $event = apply_filters( "Zend\Log\Process\WPEvent", $event );

        return $event;
    }

    

}
