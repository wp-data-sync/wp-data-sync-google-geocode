<?php
/**
 * Delete Post
 *
 * Deletes coordinates from database when post is deleted.
 *
 * @since   1.0.0
 *
 * @package WP_DataSync_GoogleGeoCode
 */

namespace WP_DataSync\GoogleGeoCode\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'delete_post', function( $post_id ) {

	GeoCode::delete( $post_id );

} );