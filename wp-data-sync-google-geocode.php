<?php
/**
 * Plugin Name: WP Data Sync - Google GeoCode Extension
 * Plugin URI:  https://wpdatasync.com/products/
 * Description: Integrates Google GeoCode with WP Data Sync
 * Version:     1.0.0
 * Author:      WP Data Sync
 * Author URI:  https://wpdatasync.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-data-sync-google-geocode
 * Domain Path: /languages
 *
 * Package:     WP_DataSync_Google_GeoCode
*/

namespace WP_DataSync\GoogleGeoCode;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'WP_DATA_SYNC_GOOGLE_GEOCODE_VERSION', '1.0.0' );

foreach ( glob( plugin_dir_path( __FILE__ ) . 'includes/**/*.php' ) as $file ) {
	require_once $file;
}

/**
 * Runs after all other WP data is processed.
 */

add_action( 'wp_data_sync_integration_google_geocode', function( $post_id, $values ) {

	$geocode = Inc\GeoCode::instance();
	$geocode->set_properties( $post_id, $values );
	$geocode->save();

}, 20, 2 );
