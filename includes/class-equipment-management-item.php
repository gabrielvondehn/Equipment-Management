<?php

class Equipment_Management_Item {
    
    /**
     * @var array Attributes of this item
     */
    public $attrs;
    
    public function __construct( $attrs ) {
        
        $this->set( $attrs );
    }
    
    public function set( $attrs ) {
        
        $this->attrs = array(
            'id'             => $attrs['id'],
            'post_id'        => $attrs['post_id'],
            'date_added'     => $attrs['date_added'],
            'name'           => $attrs['name'],
            'category'       => $attrs['category'],
            'category_tags'  => $attrs['category_tags'],
            'specification'  => $attrs['specification'],
            'application'    => $attrs['application'],
            'notes'          => $attrs['notes'],
            'price'          => $attrs['price'],
            'date_bought'    => $attrs['date_bought'],
            'bought_note'    => $attrs['bought_note'],
            'vendor'         => $attrs['vendor'],
            'vendor_item_id' => $attrs['vendor_item_id'],
            'bundle'         => 'none', // Bundles are not implemented yet
            'amount'         => $attrs['amount'],
            'use'            => $attrs['use'],
        );
    }
    
    
    /**
     * 
     * @return boolean false on failior
     */
    public function sync() {
        
        global $equipment_management;
        global $wpdb;
        
        $table_name = $equipment_management->database->table_names['main_table'];
        $id = $this->attrs['equip_id'];
        
        // To avoid SQL Errors, go through all entries
        foreach( $this->attrs as $attr ) {
            if($attr === null) return false;
        }
        
        if( null == $wpdb->get_row("SELECT * FROM $table_name WHERE 'id=$id'") ) { // New entry
            $wpdb->insert( $table_name,
                array(
                    'id'             => $this->attrs['id'],
                    'post_id'        => $this->attrs['post_id'],
                    'date_added'     => $this->attrs['date_added'].' 00:00:00', // We only store date in this object, but datetime in database
                    'name'           => $this->attrs['name'],
                    'category'       => $this->attrs['category'],
                    'category_tags'  => $this->attrs['category_tags'],
                    'specification'  => $this->attrs['specification'],
                    'application'    => $this->attrs['application'],
                    'notes'          => $this->attrs['notes'],
                    'price'          => $this->attrs['price'],
                    'date_bought'    => $this->attrs['date_bought'].' 00:00:00', // We only store date in this object, but datetime in database
                    'bought_note'    => $this->attrs['bought_note'],
                    'vendor'         => $this->attrs['vendor'],
                    'vendor_item_id' => $this->attrs['vendor_item_id'],
                    'bundle'         => $this->attrs['bundle'],
                    'amount'         => $this->attrs['amount'],
                ));
        } else {
            $wpdb->update( $table_name,
                array(
                    'post_id'        => $this->attrs['post_id'],
                    'date_added'     => $this->attrs['date_added'],
                    'name'           => $this->attrs['name'],
                    'category'       => $this->attrs['category'],
                    'category_tags'  => $this->attrs['category_tags'],
                    'specification'  => $this->attrs['specification'],
                    'application'    => $this->attrs['application'],
                    'notes'          => $this->attrs['notes'],
                    'price'          => $this->attrs['price'],
                    'date_bought'    => $this->attrs['date_bought'],
                    'bought_note'    => $this->attrs['bought_note'],
                    'vendor'         => $this->attrs['vendor'],
                    'vendor_item_id' => $this->attrs['vendor_item_id'],
                    'bundle'         => $this->attrs['bundle'],
                    'amount'         => $this->attrs['amount'],
                ), array(
                    'equip_id' => $this->attrs['equip_id'],
                ));
        }
        
        $this->attrs['use']->sync();
    }
    
    /**
     * 
     * @global Equipment_Management $equipment_management
     * @global WPDB $wpdb
     * @param int $id
     * @param string $id_type "item"|"post"
     * @return \Equipment_Management_Item|boolean
     */
    public static function create_item( $id, $id_type = "item" ) {
        global $equipment_management;
        global $wpdb;
        
        $main_table_name = $equipment_management->database->table_names['main_table'];
        
        $query = "";
        
        switch ($id_type) {
            case "item":
                $query = "SELECT * FROM $main_table_name WHERE id=$id";
                break;

            case "post":
                $query = "SELECT * FROM $main_table_name WHERE post_id=$id";
                break;
        }
        
        $row = $wpdb->get_row($query, ARRAY_A);
        if($row == null) {
            return false;
        }
        
        $use_table_name = $equipment_management->database->table_names['use_table'];
        
        $use_rows = array();
        
        $item_id = $row['id'];
        
        for( $i = 0; ; $i++ ) {
            $curr_row = $wpdb->get_row("SELECT * FROM $use_table_name WHERE equip_id=$item_id", ARRAY_A, $i);
            if( $curr_row == null ) {
                break;
            }
            $curr_row['date_used'] = substr( $curr_row['date_used'], 0, 10 ); // Database stores the datetime, we only want date
            $curr_row['date_back'] = substr( $curr_row['date_back'], 0, 10 );
            array_push($use_rows, $curr_row);
        }
        
        $row['use'] = new Equipment_Management_Item_History($use_rows, $row['id']);
        $row['date_added'] = substr( $row['date_added'], 0, 10 ); // Database stores the datetime, we only want date
        $row['date_bought'] = substr( $row['date_bought'], 0, 10 );
        return new Equipment_Management_Item($row);
    }
}