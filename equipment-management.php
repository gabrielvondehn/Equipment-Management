<?php
/*
 * Plugin Name: Equipment Management
 * Version: 1.0
 * Plugin URI: http://www.hughlashbrooke.com/
 * Description: This is your starter template for your next WordPress plugin.
 * Author: Hugh Lashbrooke
 * Author URI: http://www.hughlashbrooke.com/
 * Requires at least: 4.0
 * Tested up to: 4.0
 *
 * Text Domain: equipment-management
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author Hugh Lashbrooke
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load plugin class files
require_once( 'includes/class-equipment-management.php' );
require_once( 'includes/class-equipment-management-settings.php' );

// Load plugin libraries
require_once( 'includes/lib/class-equipment-management-admin-api.php' );
require_once( 'includes/lib/class-equipment-management-post-type.php' );
require_once( 'includes/lib/class-equipment-management-taxonomy.php' );

/**
 * Returns the main instance of Equipment_Management to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object Equipment_Management
 */
function Equipment_Management () {
	$instance = Equipment_Management::instance( __FILE__, '1.0.0' );

	if ( is_null( $instance->settings ) ) {
		$instance->settings = Equipment_Management_Settings::instance( $instance );
	}

	return $instance;
}

Equipment_Management();
