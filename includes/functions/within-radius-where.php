<?php
/**
 * Within Radius Where
 *
 * Use this weher statement to select posts within a radius of a latitude and longintude point.
 *
 * @since   1.0.0
 *
 * @package WP_DataSync_GoogleGeoCode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Within radius wehere statement.
 *
 * @param $latitude
 * @param $longtitude
 * @param $radius
 *
 * @return string
 */

function wp_data_sync_within_radius_where( $latitude, $longtitude, $radius ) {
	return WP_DataSync\GoogleGeoCode\Inc\GeoCode::within_radius_where( $latitude, $longtitude, $radius );
}