<?php

class Equipment_Management_Shortcodes {
    
    public $shortcodes = array();
   
    public function __construct() {
        array_push($this->shortcodes, new Equipment_Management_Shortcode("equip_ls", array($this, "list_all")));
        array_push($this->shortcodes, new Equipment_Management_Shortcode(
                "equip_show", array($this, "show_id"), array("id" => "")));
    }
    
    public function list_all() {
        
    }
    
    public function show_id( $attrs ) {
        require_once( 'equip_show/shortcode.php' );
        $id = $attrs['id'];
        $item = Equipment_Management_Item::create_item($id);
        return equipment_management_show_id_shortcode($item);
    }
}