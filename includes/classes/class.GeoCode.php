<?php
/**
 * GeoCode
 *
 * Process Google GeoCode data
 *
 * @since   1.0.0
 *
 * @package WP_DataSync_GeoCode
 */

namespace WP_DataSync\GoogleGeoCode\Inc;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class GeoCode {

	/**
	 * @var int
	 */

	private $post_id;

	/**
	 * @var array
	 */

	private $values;

	/**
	 * @var array
	 */

	private $boxes = [];

	/**
	 * @var GeoCode
	 */

	public static $instance;

	/**
	 * GeoCode constructor.
	 */

	public function __construct() {
		self::$instance = $this;
	}

	/**
	 * @return GeoCode
	 */

	public static function instance() {

		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;

	}

	/**
	 * Set object properties.
	 *
	 * @param $post_id int
	 * @param $values     array
	 */

	public function set_properties( $post_id, $values ) {

		$this->post_id = $post_id;
		$this->values  = $values;

	}

	/**
	 * Save data.
	 */

	public function save() {

		$this->set_meta_values();
		$this->set_values();

	}

	/**
	 * Set post meta values.
	 */

	private function set_meta_values() {

		if ( isset( $this->values['post_meta'] ) && is_array( $this->values['post_meta'] ) ) {

			foreach ( $this->values['post_meta'] as $key => $value ) {
				update_post_meta( $this->post_id, $key, $value );
			}

		}

	}

	/**
	 * Set values.
	 */

	private function set_values() {

		if ( isset( $this->values['latitude'] ) && isset( $this->values['longitude'] ) ) {

			if ( $this->exists() ) {
				$this->update();
			}
			else {
				$this->insert();
			}

		}

	}

	/**
	 * DB table name.
	 *
	 * @return string
	 */

	public static function table_name() {

		global $wpdb;

		return $wpdb->prefix . 'data_sync_google_goecode';

	}

	/**
	 * Cretate DB table.
	 */

	public static function create_tables() {

		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $wpdb->get_charset_collate();
		$table_name      = self::table_name();

		$sql = "
			  CREATE TABLE IF NOT EXISTS $table_name (
  			  id bigint(20) NOT NULL AUTO_INCREMENT,
  			  post_id bigint(20) NOT NULL,
  			  latitude float(10,6) NOT NULL,
  			  longitude float(10,6) NOT NULL,
  			  PRIMARY KEY (id),
  			  KEY post_id (post_id),
  			  KEY latitude (latitude),
  			  KEY longitude (longitude)
			  ) $charset_collate;
		";

		dbDelta( $sql );

	}

	/**
	 * Exists.
	 *
	 * @return bool
	 */

	public function exists() {

		global $wpdb;

		$table_name = self::table_name();

		$exists = $wpdb->get_var( $wpdb->prepare(
			"
			SELECT post_id
			FROM $table_name
			WHERE post_id = %d
			",
			$this->post_id
		) );

		if ( null === $exists || is_wp_error( $exists ) ) {
			return FALSE;
		}

		return TRUE;

	}

	/**
	 * Insert coords
	 */

	public function insert() {

		global $wpdb;

		$wpdb->insert(
			self::table_name(),
            [
				'post_id'   => $this->post_id,
				'latitude'  => $this->values['latitude'],
				'longitude' => $this->values['longitude']
			]
		);

	}

	/**
	 * Update coords.
	 */

	public function update() {

		global $wpdb;

		$wpdb->update(
			self::table_name(),
			[
				'latitude'  => $this->values['latitude'],
				'longitude' => $this->values['longitude']
			],
			[ 'post_id'   => $this->post_id, ]
		);

	}

	/**
	 * Delete.
	 *
	 * @param $post_id int
	 */

	public static function delete( $post_id ) {

		global $wpdb;

		$wpdb->delete(
			self::table_name(),
			[ 'post_id'   => $post_id, ]
		);

	}

	/**
	 * Within radius weher statement.
	 *
	 * Use this weher statement to select posts within a radius of a latitude and longintude point.
	 *
	 * @param $latitude   float
	 * @param $longtitude float
	 * @param $radius     int
	 *
	 * @return string
	 */

	public static function within_radius_where( $latitude, $longtitude, $radius ) {

		global $wpdb;

		$table_name = self::table_name();

		if ( ! empty( $latitude ) && ! empty( $longtitude ) ) {

			$where .= " AND $wpdb->posts.ID IN (SELECT post_id FROM $table_name WHERE 
                ( 3959 * acos( cos( radians($latitude) ) 
                    * cos( radians( latitude ) ) 
                    * cos( radians( longitude ) 
                    - radians($longtitude) ) 
                    + sin( radians($latitude) ) 
                    * sin( radians( latitude ) ) ) ) <= $radius
                )";

		}

		$where .=
			"
			AND $wpdb->posts.ID 
			NOT IN (SELECT post_id FROM $table_name 
			WHERE latitude = 0 
			AND longitude = 0)
		";

		return $where;

	}

}
