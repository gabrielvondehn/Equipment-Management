<?php

class Equipment_Management_Security {
    
    private $capabilities;
    
    public function __construct() {
        $this->setup_abilities();
    }
    
    public function setup_abilities() {
        $opn_capabilities = get_option( 'equipment_managment_capabilities' );
        
        if($opn_capabilities == false) {
            $this->set_default_capabilities();
        } else {
            $this->capabilities = $opn_capabilities;
        }
    }
    
    /**
     * WARNING: Only currently adds capabilities, does not remove them
     * s
     * @param boolean $sync Whether or not to sync the new abilities to the database
     */
    public function set_default_capabilities( $sync = true ) {
        $this->capabilities = array (
            "view_id"                   => "",
            "view_post_id"              => "administrator",
            "view_date_added"           => "administrator",
            "view_equip_name"           => "",
            "view_equip_category"       => "subscriber",
            "view_equip_category_tags"  => "subscriber",
            "view_equip_specitication"  => "subscriber",
            "view_equip_application"    => "subscriber",
            "view_equip_notes"          => "subscriber",
            "view_equip_price"          => "editor",
            "view_date_bought"          => "editor",
            "view_bought_note"          => "editor",
            "view_equip_vendor"         => "",
            "view_vendor_item_id"       => "",
            "view_bundle"               => "editor",
            "view_equip_amount"         => "subscriber",
            "view_equip_in_use"         => "subscriber",
            "view_equip_history"        => "editor",
            "add_equip"                 => "administrator",
            "edit_equip"                => "administrator",
            "delete_equip"              => "administrator",
            "add_history"               => "contributor",
            "edit_history"              => "administrator",
            "delete_history"            => "administrator",
        );
        
        foreach( $this->capabilities as $cap => $role ) {
            if( $role !== "" ) {
                $wp_role = get_role( $role );
                $wp_role->add_cap( $cap );
            }
        }
        
        if( sync ) {
            update_option( "equipment_managment_capabilities", $this->capabilities );
        }
    }
    
    public function current_user_can( $cap ) {
        
        if( $this->capabilities[$cap] == "" ) {
            return true;
        } else {
            return is_user_logged_in() && current_user_can( $cap );
        }
    }
}