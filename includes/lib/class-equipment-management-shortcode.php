<?php

if ( ! defined( 'EQUIPMENT_MANAGEMENT_VERSION' ) ) die( 'No script kiddies allowed' );

class Equipment_Management_Shortcode {

	/**
	 * The name for the shortcode.
	 * @var 	string
	 * @access  public
	 * @since 	1.0.0
	 */
	public $shortcode;
        
        /**
         * The handler function for this shortcode. It will be passed the attributes,
         * with shortcode_atts already applied, as well as the content 'as is'.
         * @var         callable
         * @access  public
         * @since       1.0.0
         */
        public $callback;
        
        /**
         * The entire list of supported attributes and their defaults as in https://developer.wordpress.org/reference/functions/shortcode_atts/
         * @var         array
         * @access  public
         * @since       1.0.0
         */
        public $pairs;

	public function __construct ( $shortcode = '', $callback = '', $pairs = array() ) {

		if ( ! $shortcode || ! $callback ) return;

		// Shortcode type name and labels
		$this->shortcode = $shortcode;
		$this->callback = $callback;
		$this->pairs = $pairs;

		// Regsiter shortcode type
		add_shortcode( $shortcode , array( $this, 'run_shortcode' ) );
	}

	/**
	 * Run the shortcode
         * 
	 * @return void
	 */
	public function run_shortcode ( $atts, $content = '' ) {
                
            if( !empty($atts) ) {
		$atts = shortcode_atts( 
                        $this->pairs,
                        $atts,
                        $this->shortcode);
            }
            
            return call_user_func($this->callback, $atts, $content);
	}

}
