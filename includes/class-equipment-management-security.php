<?php

class Equipment_Management_Security {
    
    public $public_caps;
    
    public function __construct() {
        $this->setup_capabilities();
    }
    
    public function setup_capabilities() {
        $opn_capabilities = get_option( 'equipment_managment_public_capabilities' );
        
        if($opn_capabilities == false) {
            echo "opn_caps was false";
            $this->set_default_capabilities();
        } else {
            $this->public_caps = $opn_capabilities;
        }
    }
    
    /**
     * WARNING: Only currently adds capabilities, does not remove them
     * 
     * Contains an empty string if cap should be public. Not applicable to add_*, remove_* or edit_*
     * Else contains a comma seperated list of all roles with that capability.
     * 
     * @param boolean $sync Whether or not to sync the new abilities to the database
     */
    public function set_default_capabilities( $sync = true ) {
        $this->public_caps = array();
        
        $default_capabilities_json = file_get_contents( plugin_dir_url( __FILE__ ) . 
                "/equipment-management-default-capabilities.json" );
        
        $default_capabilities = json_decode($default_capabilities_json, true, 10);
        
        foreach( $default_capabilities as $cap => $roles ) {
            $roles = explode(",", $roles );
            foreach( $roles as $role ) {
                if( $role === "public" ) {
                    array_push($this->public_caps, $cap);
                    continue;
                }
                $wp_role = get_role( $role );
                $wp_role->add_cap( $cap );
            }
        }
        
        if( $sync ) {
            update_option( 'equipment_managment_public_capabilities', $this->public_caps );
        }
    }
    
    public function current_user_can( $cap ) {
        
        if( !empty($this->public_caps[$cap]) && $this->public_caps[$cap] === "public" ) {
            return true;
        } else {
            return is_user_logged_in() && current_user_can( $cap );
        }
    }
}