<?php
/**
 * Fired during plugin activation and during a plugin update
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Equipment_Management
 * @subpackage Equipment_Management/includes
 */

namespace Equipment_Management\includes;

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Equipment_Management
 * @subpackage Equipment_Management/includes
 * @author     Your Name <email@example.com>
 */
class Updater {

	/**
	 * Sets up the database. Is called during plugin activation and updates.
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function update() {

	}

	/**
	 * Get the SQL to create the Database tables.
	 *
	 * @since 1.0.0
	 *
	 * @global $wpdb The database abstraction object built into WordPress.
	 */
	private function get_db_schema() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$eqmn_type_table = ($wpdb->prefix).'eqmn_type';
		$eqmn_item_table = ($wpdb->prefix).'eqmn_item';
		$eqmn_item_meta_table = ($wpdb->prefix).'eqmn_item_meta';

		$tables = "CREATE TABLE 'equipment_management'.'wp_eqmn_type' (
'id' INT NOT NULL AUTO_INCREMENT,
'slug' VARCHAR(45) NOT NULL,
'table_structure' TEXT NOT NULL,
'options' TEXT NOT NULL,
PRIMARY KEY ('id'),
UNIQUE KEY 'id' ('id'),
UNIQUE KEY 'slug' ('slug'));
CREATE TABLE 'equipment_management'.'wp_eqmn_item' (
'item_id' BIGINT(20) UNSIGNED NOT NULL,
'item_type' INT NOT NULL,
PRIMARY KEY ('item_id'),
UNIQUE KEY 'item_id' ('item_id'));
CREATE TABLE 'equipment_management'.'wp_eqmn_item_meta' (
'meta_id' BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
'item_id' BIGINT(20) UNSIGNED NOT NULL,
'meta_name' VARCHAR(191) NOT NULL,
'meta_value' TEXT NOT NULL,
PRIMARY KEY ('meta_id'),
UNIQUE INDEX 'meta_id' ('meta_id'),
KEY 'item_id' ('item_id'),
KEY 'meta_name' ('meta_name'));";
	}

}
