<?php

if ( ! defined( 'EQUIPMENT_MANAGEMENT_VERSION' ) ) die( 'No script kiddies allowed' );


class Equipment_Management_Database extends Equipment_Management_Database_API {
    
    public function __construct() {
        
        global $wpdb;
        
        $table_structure_json = file_get_contents(plugin_dir_url( __FILE__ ) . 
                "/equipment-management-database-table-structure.json" );
        
        $table_structure = Equipment_Management_Database_API::parse_table_structure($table_structure_json);
        
        parent::__construct (
                array(
                    "main_table" => ($wpdb->prefix)."equipment",
                    "use_table" => ($wpdb->prefix)."equipment_use",
                    "bundle_table" => ($wpdb->prefix)."equipemnt_bundle"
                ),
                $table_structure,
                EQUIPMENT_MANAGEMENT_DATABASE_VERSION
        );
    }
}