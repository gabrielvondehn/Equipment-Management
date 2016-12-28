<?php
/**
 * The file that defines the Item class.
 *
 * TODO: Nicer explanation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Equipment_Management
 * @subpackage Equipment_Management/includes
 */

namespace Equipment_Management\includes;

/**
 * The Item class.
 *
 * {@internal Missing description}
 *
 * @since      1.0.0
 * @package    Equipment_Management
 * @subpackage Equipment_Management/includes
 * @author     Gabriel von Dehn <email@example.com>
 */
class Item {

	/**
	 * Various information about this item.
	 *
	 * @since 1.0.0
	 * @access protected
	 * @var array
	 */
	protected $_args;

	/**
	 * The type of this item
	 *
	 * @since 1.0.0
	 * @access public
	 * @var Item_Type
	 */
	public $item_type;

	/**
	 * Constructor.
	 * A child class should call this and pass in it's corresponding $args.
	 *
	 * @since 1.0.0
	 * @access public
	 *
	 * @param array|string $args {
	 *     Array or string of arguments.
	 *
	 *     @type string The string identifying the type of this item.
	 * }
	 */
	public function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'type' => '',
		));

		$this->item_type = convert_to_item_type( $args['type'] );

		$this->_args = $args;
	}

	/**
	 * {@internal Missing description}
	 *
	 * @return boolean false on failior
	 */
	public function sync() {
		/* TO DO */
		return false;
	}

}

/**
 * Get the Item of the specified $id.
 *
 * @param string|int $id {@internal Missing Description}.
 * @return Item The item for the $id.
 */
function get_item( $id ) {
	// TODO: Logic.
	return null;
}
