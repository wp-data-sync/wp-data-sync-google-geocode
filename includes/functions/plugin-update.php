<?php
/**
 * Plugin Update
 *
 * Handles plugin update functions.
 *
 * @since   1.0.0
 *
 * @package WP_DataSync_GoogleGeoCode
 */

namespace WP_DataSync\GoogleGeoCode\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'init', function() {

	if ( WP_DATA_SYNC_GOOGLE_GEOCODE_VERSION !== get_option( 'WP_DATA_SYNC_GOOGLE_GEOCODE_VERSION' ) ) {

		GoogleGeoCode::create_tables();

		update_option( 'WP_DATA_SYNC_GOOGLE_GEOCODE_VERSION', WP_DATA_SYNC_GOOGLE_GEOCODE_VERSION );

	}

} );