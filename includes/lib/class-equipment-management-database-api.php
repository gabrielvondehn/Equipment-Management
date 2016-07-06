<?php

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
    
    /**
     * Constructs the object. $table_structure must have the names already prefixed
     * @param array $table_names
     * @param array $table_structure
     * @param string $version
     */
    public function __construct( $table_names, $table_structure, $version ) {
        global $wpdb;
        
        $this->table_names = $table_names;
        
        $this->table_structure = $table_structure;
        $this->version = $version;
        
        // If the database is outdated, perform an update
        if( $this->version != get_option( EQUIPMENT_MANAGEMENT_DATABASE_VERSION_OPTION ) ) {
            $this->update_database();
        }
        
    }
    
    /**
     * Generates SQL for a given table using the $this->table_structure
     * 
     * @param string $table Slug of the desired table SQL
     * @return string The SQL for that table.
     */
    private function generate_SQL_from_table_structure( $table_slug ) {
        
        global $wpdb;
        
        $sql = "CREATE TABLE ".$this->table_names[ $table_slug ]." (\n";
        
        $columns = $this->table_structure[ $table_slug ];
        
        $uqpk = null; // The Unique Primary Key.
                
        foreach( $columns as $column ) {
            $sql = $sql.$column['name']." ";
            $sql = $sql.$column['type']." ";
            
            if ($column['uq pk'] == true) {
                $uqpk = $column;
            }

            if ($column['un'] == true) {
                $sql = $sql."UNSIGNED ";
            }

            if ($column['nn'] == true) {
                $sql = $sql."NOT NULL ";
            }

            if ($column['ai'] == true) {
                $sql = $sql."AUTO_INCREMENT ";
            }

            if ($column['default'] != null) {
                $sql = $sql."DEFAULT ".$column['default'];
            }
            
            $sql = $sql.",\n";
        }
        
        if( $uqpk != null ) {
            $sql = $sql."PRIMARY KEY (".$uqpk['name']."),\n";
            $sql = $sql."UNIQUE KEY ".$uqpk['name']." (".$uqpk['name'].")";
        }
        
        $sql = $sql.") ";
        $sql = $sql.$wpdb->get_charset_collate().";";
        
        return $sql;
    }
    
    
    
    
    private function update_database() {
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        
        foreach( $this->table_names as $slug => $name ) {
            
            dbDelta( $this->generate_SQL_from_table_structure( $slug ) );
        }
        
        update_option( EQUIPMENT_MANAGEMENT_DATABASE_VERSION_OPTION, EQUIPMENT_MANAGEMENT_DATABASE_VERSION );
    }
    
    public function get_table_names() {
        return $this->table_names;
    }
    
    public function get_table_structure() {
        return $this->table_structure;
    }
    
    public function get_databse_version() {
        return $this->database_version;
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
    
    
}