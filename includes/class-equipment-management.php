<?php

if ( ! defined( 'EQUIPMENT_MANAGEMENT_VERSION' ) ) die( 'No script kiddies allowed' );

class Equipment_Management {

	/**
	 * The single instance of Equipment_Management.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;
        
        /**
         * Database class object
         * @var     object
         * @access  public
         * @since   1.0.0
         */
        public $database = null;
        
        /**
         *
         * @var     object
         * @access  public
         * @since   1.0.0 
         */
        public $security = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;
        
        /**
         * 
         */
        public $shortcodes;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'equipment_management';
                
		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

                // Load the database
                $this->database = new Equipment_Management_Database();
                
                //Load the security
                $this->security = new Equipment_Management_Security();
                
                $this->shortcodes = new Equipment_Management_Shortcodes();
                
                $this->register_post_type("eqmn_item", "Equipment", "Equipment", "", 
                        array(
                            'rewrite' => array('slug' => 'id'),
                            'menu_icon' => 'dashicons-archive',
                            'menu_position' => 2,
                            'supports' => array('title'),
                            'hierarchical' => false,
                            
//                            'public' => false,
//                            'show_ui' => false,
//                            'show_in_nav_menus' => false,
//                            'show_in_menu' => false,
//                            'show_in_admin_bar' => false,
                        )
                );
                
                $this->register_eqmn_item_post_type_filters();
                
		// Load API for generic admin functions
		if ( is_admin() ) {
			$this->admin = new Equipment_Management_Admin_API();
		}

		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()
        
        /**
         * Register all Hooks and Filter needed for customisation of the eqmn_item pages
         */
        public function register_eqmn_item_post_type_filters() {
            
            // In part courtesy of yoast.com and wpbeginner.com
            // See https://yoast.com/dev-blog/custom-post-type-snippets/
            
            // BEGIN Edit Equipment screen
            
            // Change the columns for the edit Equipment screen
            add_filter( "manage_eqmn_item_posts_columns", function ( $cols ) {
              $cols = array(
                'cb'                => '<input type="checkbox" />',
                'id'                => 'ID',
                'name'              => 'Name',
                'category'          => 'Kategorie',
                'amount_availiable' => 'Anzahl verfügbar',
              );
              return $cols;
            } );
            
            // Give new columns some content
            add_action( "manage_eqmn_item_posts_custom_column", function( $column, $post_id ) {
                
                $item = Equipment_Management_Item::create_item($post_id, "post");
                
                switch ( $column ) {
                    case "id":
                        echo $item->attrs['id'];
                        break;
                    case "name":
                        echo $item->attrs['name'];
                        break;
                    case "category":
                        echo $item->attrs['category'];
                        break;
                    case "amount_availiable":
                        echo $item->attrs['amount']; // TO DO: Availiable???
                        break;
                }
            }, 10, 2 );
            
            // Make columns sortable
            add_filter( "manage_edit-eqmn_item_sortable_columns", function () {
                return array(
                    'id'                => 'id',
                    'name'              => 'name',
                    'category'          => 'category',
                    'amount_availiable' => 'amount_availiable',
                );
            } );
            
            // Remove quick edit button
            add_filter('post_row_actions', function( $actions ) {
                unset($actions['inline hide-if-no-js']);
                return $actions;
            });
            
            // END Edit Equipment screen
            
            
            // BEGIN Post/Post-new Equipment screen
            
            add_action('edit_form_top', function() {
                wp_nonce_field( 'edit_equipment', 'equipment_mangagement_eq_nonce' );
            });
            
            // Change 'Enter title here' to 'Enter ID'
            add_filter('enter_title_here', function ( $title ){
                $screen = get_current_screen();
                
                if  ( 'eqmn_item' == $screen->post_type ) {
                     $title = 'Enter ID';
                }

                return $title;
            });
            
            // Remove the meta boxes
            add_action( 'do_meta_boxes', function () {
                remove_meta_box( 'commentstatusdiv', 'eqmn_item', 'normal' );
                remove_meta_box( 'commentsdiv', 'eqmn_item', 'normal' );
                // remove_meta_box( 'submitdiv', 'eqmn_item', 'side' );
                remove_meta_box( 'slugdiv', 'eqmn_item', 'normal' );
            });
            
            // Add custom meta boxes
            add_action( 'add_meta_boxes_eqmn_item', function($post) {
                
                $post_id = $post->ID;
                
                global $item;
                $item = Equipment_Management_Item::create_item($post_id, 'post');
                
                global $attrs;
                
                $attrs = array(
                    'date_added' => date('Y-m-d'), // To have a date added for new items
                    'name'           => '',
                    'category'       => '',
                    'category_tags'  => '',
                    'specification'  => '',
                    'application'    => '',
                    'notes'          => '',
                    'price'          => '',
                    'date_bought'    => '',
                    'bought_note'    => '',
                    'vendor'         => '',
                    'vendor_item_id' => '',
                    'amount'         => '',
                );
                
                if($item !== false) {
                    $attrs = $item->attrs;
                }
                
                
                
                // Add meta box for the equipment name
                add_meta_box(
                    'equipment_name',
                    'Name',
                    function() { global $attrs; ?>
<input type="text" id="eq_name" name="eq_name" value="<?php echo $attrs['name']; ?>">
<input type="hidden" id="eq_date_added" name="eq_date_added" value="<?php echo $attrs['date_added']; ?>">
                    <?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for the category this piece of equipment belongs to
                add_meta_box(
                    'equipment_category',
                    'Kategorie',
                    function() { global $attrs; ?>
<input type="text" id="eq_category" name="eq_category" value="<?php echo $attrs['category']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for the "Kategoriespezifische Angabe"
                add_meta_box(
                    'equipment_category_tags',
                    'Kategoriespezifische Angabe',
                    function() { global $attrs; ?>
<input type="text" id="eq_category_tags" name="eq_category_tags" value="<?php echo $attrs['category_tags']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for the specification of this piece of equipment
                add_meta_box(
                    'equipment_specification',
                    'Spezifikation',
                    function() { global $attrs; ?>
<input type="text" id="eq_specification" name="eq_specification" value="<?php echo $attrs['specification']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for the application of the equipment
                add_meta_box(
                    'equipment_application',
                    'Einsatz',
                    function() { global $attrs; ?>
<input type="text" id="eq_application" name="eq_application" value="<?php echo $attrs['application']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for enotes
                add_meta_box(
                    'equipment_notes',
                    'Notizen',
                    function() { global $attrs; ?>
<input type="text" id="eq_notes" name="eq_notes" value="<?php echo $attrs['notes']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for the price of this piece of equipment
                add_meta_box(
                    'equipment_price',
                    'Einzelpreis',
                    function() { global $attrs; ?>
<input type="number" step="0.01" id="eq_price" name="eq_price" value="<?php echo $attrs['price']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for the date it was bought
                add_meta_box(
                    'equipment_date_bought',
                    'Kaufdatum',
                    function() { global $attrs; ?>
<input type="date" id="eq_date_bought" name="eq_date_bought" value="<?php echo $attrs['date_bought']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for the bought note
                add_meta_box(
                    'equipment_bought_note',
                    'Kaufnotiz',
                    function() { global $attrs; ?>
<input type="text" id="eq_bought_note" name="eq_bought_note" value="<?php echo $attrs['bought_note']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for the vendor
                add_meta_box(
                    'equipment_vendor',
                    'Verkäufer',
                    function() { global $attrs; ?>
<input type="text" id="eq_vendor" name="eq_vendor" value="<?php echo $attrs['vendor']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for the if of this piece of equipment at the vendor
                add_meta_box(
                    'equipment_vendor_item_id',
                    'ID beim Verkäufer',
                    function() { global $attrs; ?>
<input type="number" step="1" id="eq_vendor_item_id" name="eq_vendor_item_id" value="<?php echo $attrs['vendor_item_id']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                 
                // Add meta box for the amount
                add_meta_box(
                    'equipment_amount',
                    'Menge',
                    function() { global $attrs; ?>
<input type="number" step="1" id="eq_amount" name="eq_amount" value="<?php echo $attrs['amount']; ?>">
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                    
                // Add a meta box for history
                add_meta_box(
                    'equipment_use',
                    'Benutzung',
                    function() { global $attrs; ?>
<table id="eq_use_table">
    <tr>
        <th>Benutzer</th>
        <th>Menge</th>
        <th>Start</th>
        <th>Ende</th>
    </tr>
    <?php
    // If this is a new item, all iteration over Equipment_Management_Item_History would fail
    if(isset($attrs['use'])) {
        foreach( $attrs['use']->history as $entry ) {
            ?>
    <tr id="<?php echo 'eq_use_row_'.$entry['ID']; ?>">
        <td><input type="text" id="<?php echo 'eq_use_used_by_'.$entry['ID']; ?>" name="<?php echo 'eq_use_used_by_'.$entry['ID']; ?>" value="<?php echo $entry['used_by']; ?>"></td>
        <td><input type="number" step="1" id="<?php echo 'eq_use_amount_used_'.$entry['ID']; ?>" name="<?php echo 'eq_use_amount_used_'.$entry['ID']; ?>" value="<?php echo $entry['amount_used']; ?>"></td>
        <td><input type="date" id="<?php echo 'eq_use_date_used_'.$entry['ID']; ?>" name="<?php echo 'eq_use_date_used_'.$entry['ID']; ?>" value="<?php echo $entry['date_used']; ?>"></td>
        <td><input type="date" id="<?php echo 'eq_use_date_back_'.$entry['ID']; ?>" name="<?php echo 'eq_use_date_back_'.$entry['ID']; ?>" value="<?php 
        if($entry['date_back'] != '1000-01-01') {
            echo $entry['date_back'];
        }
        ?>"></td>
    </tr>
    <?php
        }
    }
    ?>
</table>
<button id="eq_use_add_new" type="button">Neu...</button>
<?php
                    },
                    'eqmn_item',
                    'normal',
                    'default'
                );
                    
                wp_enqueue_script( $this->_token . '-add-item', esc_url( $this->assets_url ) . 'js/add-item' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
                    
                /*
                
                // Add meta box for saving the data
                add_meta_box(
                    'equipment_save',
                    'Speichern',
                    function() {//  How to: look default post types? input type submit? ?>
<input type="submit" id="eq_save" value="Speichern" class="button button-primary button-large">
<?php
                    },
                    'eqmn_item',
                    'side',
                    'default'
                );*/
                
            });
            
            // Changing the text of submit to display "Save"
            add_filter( 'gettext', function ($translation, $text) {
                if('eqmn_item' == get_post_type()) {
                    if($text == 'Publish') {
                        return 'Save';
                    }
                }
                return $translation;
            }, 10, 2);
            
            // Make those new inputs actually do something
            add_action( 'save_post_eqmn_item', function( $post_id, $post, $update ) {
                
                // verify if this is an auto save routine. 
                // If it is our form has not been submitted, so we dont want to do anything
                if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
                    wp_die('autosave');
                    //return;

                // TO DO: NONCE!!
                
                // Check permissions
                if ( 'page' == $post->post_type ) 
                {
                  if ( !current_user_can( 'edit_equip', $post_id ) )
                      wp_die('not permissions');
                      //return;
                }
                else
                {
                  if ( !current_user_can( 'edit_equip', $post_id ) )
                      wp_die('no permissions and wrong page');
                      //return;
                }
                
                $use = new Equipment_Management_Item_History( array(), $post->post_title );
                
                if (!(isset($_POST['eq_name']) && isset($_POST['eq_category']) &&
                        isset($_POST['eq_category_tags']) && isset($_POST['eq_specification'])
                        && isset($_POST['eq_application']) && isset($_POST['eq_notes'])
                        && isset($_POST['eq_price']) && isset($_POST['eq_date_bought'])
                        && isset($_POST['eq_bought_note']) && isset($_POST['eq_vendor'])
                        && isset($_POST['eq_vendor_item_id']) && isset($_POST['eq_amount'])) ) {
                    return;
                }
                
                $item_attr = array(
                    'id'             => $post->post_title,
                    'post_id'        => $post_id,
                    'date_added'     => $_POST['eq_date_added'],
                    'name'           => $_POST['eq_name'],
                    'category'       => $_POST['eq_category'],
                    'category_tags'  => $_POST['eq_category_tags'],
                    'specification'  => $_POST['eq_specification'],
                    'application'    => $_POST['eq_application'],
                    'notes'          => $_POST['eq_notes'],
                    'price'          => $_POST['eq_price'],
                    'date_bought'    => $_POST['eq_date_bought'],
                    'bought_note'    => $_POST['eq_bought_note'],
                    'vendor'         => $_POST['eq_vendor'],
                    'vendor_item_id' => $_POST['eq_vendor_item_id'],
                    'amount'         => $_POST['eq_amount'],
                    'use'            => $use,
                );
                
                $item = new Equipment_Management_Item( $item_attr );
                
                $item->sync();
            }, 10, 3);
            
            
            
            // END Post/Post-new Equipment screen
        }
        
	/**
	 * Wrapper function to register a new post type
	 * @param  string $post_type   Post type name
	 * @param  string $plural      Post type item plural name
	 * @param  string $single      Post type item single name
	 * @param  string $description Description of post type
	 * @return object              Post type class object
	 */
	public function register_post_type ( $post_type = '', $plural = '', $single = '', $description = '', $options = array() ) {

		if ( ! $post_type || ! $plural || ! $single ) return;

		$post_type = new Equipment_Management_Post_Type( $post_type, $plural, $single, $description, $options );

		return $post_type;
	}
        
	/**
	 * Wrapper function to register a new taxonomy
	 * @param  string $taxonomy   Taxonomy name
	 * @param  string $plural     Taxonomy single name
	 * @param  string $single     Taxonomy plural name
	 * @param  array  $post_types Post types to which this taxonomy applies
	 * @return object             Taxonomy class object
	 */
	public function register_taxonomy ( $taxonomy = '', $plural = '', $single = '', $post_types = array(), $taxonomy_args = array() ) {

		if ( ! $taxonomy || ! $plural || ! $single ) return;

		$taxonomy = new Equipment_Management_Taxonomy( $taxonomy, $plural, $single, $post_types, $taxonomy_args );

		return $taxonomy;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'equipment-management', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'equipment-management';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main Equipment_Management Instance
	 *
	 * Ensures only one instance of Equipment_Management is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Equipment_Management()
	 * @return Main Equipment_Management instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
