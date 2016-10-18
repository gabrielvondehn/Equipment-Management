<?php

/**
 * give an ID if the history is already in the database,
 * don'T give one if it isn't.
 */
class Equipment_Management_Item_History {
    
    /**
     *
     * @var array The history
     */
    public $history;
    
    /**
     *
     * @var int The associated item id
     */
    public $item_id;
    
    public function __construct( $history, $item_id ) {
        
        $this->item_id = $item_id;
        $this->set( $history );
    }
    
    public function set( $history ) {
        
        $this->history = array();
        
        foreach( $history as $entry ) {
            array_push($this->history, array(
                'ID'             => $entry['ID'],
                'used_by'        => $entry['used_by'],
                'amount_used'    => $entry['amount_used'],
                'date_used'      => $entry['date_used'],
                'date_back'      => $entry['date_back'],
                'usage_type'     => 'normal', // Not implemented yet
            ));
        }
    }
    
    public function sync() {
        
        global $equipment_management;
        global $wpdb;
        
        $table_name = $equipment_management->database->table_names['use_table'];
        
        foreach( $this->history as $entry ) {
            if( empty($entry['ID']) ) { // New history entry
                $wpdb->insert( $table_name,
                    array(
                        'equip_id'       => $this->item_id,
                        'used_by'        => $entry['used_by'],
                        'amount_used'    => $entry['amount_used'],
                        'date_used'      => $entry['date_used'].' 00:00:00', // We only store date in this object, but datetime in database
                        'date_back'      => $entry['date_back'].' 00:00:00', // We only store date in this object, but datetime in database
                        'usage_type'     => $entry['usage_type'],
                    ));
            } else {
                $wpdb->update( $table_name,
                    array(
                        'used_by'        => $entry['used_by'],
                        'amount_used'    => $entry['amount_used'],
                        'date_used'      => $entry['date_used'].' 00:00:00', // We only store date in this object, but datetime in database
                        'date_back'      => $entry['date_back'].' 00:00:00', // We only store date in this object, but datetime in database
                        'usage_type'     => $entry['usage_type'],
                    ), array(
                        'ID' => $entry['ID'],
                    ));
            }
        }
    }
}