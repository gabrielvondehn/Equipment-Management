<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if ( ! defined( 'EQUIPMENT_MANAGEMENT_VERSION' ) ) die( 'No script kiddies allowed' );

class Equipment_Management_Database_API {
    
    /**
     * An assoziative array of the table name slugs to the prefixed table names.
     * @var 	array
     * @access  private
     * @since 	1.0.0
     */
    private $table_names;
    
    /**
     * An assoziative array of the table name slugs to the table structure.
     * @var array 
     * @access private
     * @since 1.0.0
     */
    private $table_structure;
    
    /**
     * The version of the database.
     * @var     string
     * @access  private
     * @since   1.0.0
     */
    private $database_version;
    
    public function __construct( $table_names, $table_structure, $version ) {
        global $wpdb;
        
        foreach( $table_names as $name ) {
            array_push($this->table_names, ($wpdb->prefix).$name);
        }
        
        $this->table_structure = $table_structure;
        $this->version = $version;
        
        // If the database is outdated, perform an update
        if( $this->version != get_option(EQUIPMENT_MANAGEMENT_DATABASE_VERSION_OPTION) ) {
            update_database();
        }
        
    }
    
    /**
     * Pareses a JSON string to  table struture; see table_structure.json
     * @param type $json
     * @return array
     */
    public static function parse_table_structure( $json ) {
        $struct_obj = json_decode( $json, true, 10 );
        
        //var_dump($struct_obj);
        
        $result_structure = array();
        
        foreach( $struct_obj as $table => $struct ) {
            
            $table_struct = array();
            
            foreach( $struct as $col => $properties ) {
                
                $column = array();
                
                $column['name'] = $col;
                
                foreach( $properties as $property => $value ) {
                    
                    $column[$property] = $value;
                }
                
                array_push( $table_struct, $column );
            }
            
            $result_structure[$table] = $table_struct;
        }
        
        return $result_structure;
    }
    
    
    private function update_database() {
        
    }
    
    /**
     * Generates SQL for a given table using the $this->table_structure
     * 
     * @param string $table Slug of the desired table SQL
     * @return string The SQL for that table.
     */
    private function generate_SQL_from_table_structure( $table ) {
        
        global $wpdb;
        
        $sql = "CREATE TABLE ".$this->table_names[ $table ]." (\n";
        
        $columns = $this->table_structure[ $table ];
        
        $uqpk = null; // The Unique Primary Key.
                
        foreach( $columns as $column ) {
            $sql.= $column['name']." ";
            $sql.= $column['type']." ";
            
            if ($column['uq pk'] == true) {
                $uqpk = $column;
            }

            if ($column['un'] == true) {
                $sql.="UNSIGNED ";
            }

            if ($column['nn'] == true) {
                $sql.= "NOT NULL ";
            }

            if ($column['ai'] == true) {
                $sql.= "AUTO_INCREMENT ";
            }

            if ($column['default'] != null) {
                $sql.= "DEFAULT ".$column['default'];
            }
            
            $sql.= ",\n";
        }
        
        if( $uqpk != null ) {
            $sql.= "PRIMARY KEY (".$column['type']."),";
            $sql.= "UNIQUE KEY ".$column['type']." (".$column['type'].")";
        }
        
        $sql.= ") ";
        $sql.= $wpdb->get_charset_collate();
        return $sql;
    }
}