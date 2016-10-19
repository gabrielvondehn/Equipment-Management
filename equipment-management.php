<?php
/*
 * Plugin Name: Equipment Management
 * Version: 1.0
 * Plugin URI: https://github.com/GABRIELFILMSTUDIOS/Equipment-Management
 * Description: A plugin to manage Equipment
 * Author: Hugh Lashbrooke
 * Author URI: http://www.gabrielfilmstudios.wordpress.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: equipment-management
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Gabriel von Dehn
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'EQUIPMENT_MANAGEMENT_VERSION', '1.0.0');
define( 'EQUIPMENT_MANAGEMENT_DATABASE_VERSION', '1.0.4' );
define( 'EQUIPMENT_MANAGEMENT_DATABASE_VERSION_OPTION', 'equipment-management-database-version' );

// Load plugin libraries
require_once( 'includes/lib/class-equipment-management-admin-api.php' );
require_once( 'includes/lib/class-equipment-management-database-api.php' );
require_once( 'includes/lib/class-equipment-management-post-type.php' );
require_once( 'includes/lib/class-equipment-management-taxonomy.php' );
require_once( 'includes/lib/class-equipment-management-shortcode.php' );

// Load plugin class files
require_once( 'includes/class-equipment-management.php' );
require_once( 'includes/class-equipment-management-settings.php' );
require_once( 'includes/class-equipment-management-database.php' );
require_once( 'includes/class-equipment-management-security.php' );
require_once( 'includes/class-equipment-management-item.php' );
require_once( 'includes/class-equipment-management-item-history.php' );
require_once( 'includes/shortcodes/class-equipment-management-shortcodes.php' );


/**
 * Returns the main instance of Equipment_Management to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Equipment_Management
 */
function Equipment_Management () {
	$instance = Equipment_Management::instance( __FILE__, EQUIPMENT_MANAGEMENT_VERSION );
        
        global $equipment_management;
        $equipment_management = $instance;
        
	return $instance;
}

Equipment_Management();
