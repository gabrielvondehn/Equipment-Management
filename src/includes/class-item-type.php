<?php
/**
 * The file that defines the Item_Type class.
 *
 * TODO: Nicer Explanation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Equipment_Management
 * @subpackage Equipment_Management/includes
 */

namespace Equipment_Management\includes;

/**
 * The Item_Type class.
 *
 * {@internal Missing description}
 *
 * @since      1.0.0
 * @package    Equipment_Management
 * @subpackage Equipment_Management/includes
 * @author     Gabriel von Dehn <email@example.com>
 */
class Item_Type {

	/**
	 * Slug identifying this item type
	 *
	 * @access public
	 * @since 1.0.0
	 * @var string $slug
	 */
	public $slug;

	/**
	 * Singular label used for this item type
	 *
	 * @access public
	 * @since 1.0.0
	 * @var string $singular
	 */
	public $singular;

	/**
	 * Plural label used for this item type
	 *
	 * @access public
	 * @since 1.0.0
	 * @var string $plural
	 */
	public $plural;

	/**
	 * Constructor.
	 *
	 * @since   1.0.0
	 * @param   string $item_type The slug of this item type.
	 * @param   array  $args {@internal Missing description}.
	 */
	protected function __construct( $item_type, $args ) {
		$this->slug = $item_type;
	}

	/**
	 * {@internal Missing description}.
	 *
	 * @since   1.0.0
	 * @param   array $args {
	 *     Array of arguments.
	 *
	 *     @type string slug     The slug for this item type
	 *     @type string singular The singular display name.
	 *     @type string plural   The plural display name.
	 * }
	 */
	public function set_pros( $args ) {
		$args = wp_parse_args( $args, array(
			'slug'     => '',
			'singular' => '',
			'plural'   => '',

		));

		$args = apply_filters( '', $value );
	}

}

/**
 * TODO: Documentation.
 *
 * @param string $slug {@internal Missing description}.
 */
function convert_to_item_type( $slug ) {
	// TODO: Logic!
}
