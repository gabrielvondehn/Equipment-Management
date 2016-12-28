<?php
/**
 * The file that defines the Item_List_Table class.
 *
 * A class that defines a List Table for displaying Items in the main view in
 * the admin screen.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Equipment_Management
 * @subpackage Equipment_Management/admin/includes
 */

namespace Equipment_Management\admin\includes;

/* Require super class */
require_once plugin_dir_path( dirname( dirname( __FILE__ ) ) ) . 'admin/includes/class-list-table.php';

/**
 * Subclass to List_Table.
 *
 * This class is used to display a List Table of Items.
 *
 * @since      1.0.0
 * @package    Equipment_Management
 * @subpackage Equipment_Management/admin/includes
 * @author     Your Name <email@example.com>
 */
class Item_List_Table extends List_Table {

	/**
	 * Constructor
	 *
	 * @param array $args Arguments to give to the constructor.
	 */
	public function __construct( $args = array() ) {
		parent::__construct(
			array(
				'singular' => __( 'Item', 'equipment-management' ),
				'plural' => __( 'Items', 'equipment-management' ),
				'ajax' => false,
			)
		);
	}

	/**
	 * Defining bulk actions.
	 *
	 * @param string $which Required as per suberclass.
	 */
	protected function bulk_actions( $which = '' ) {
		$actions = array(
			'delete' => __( 'Delete Permanently', 'equipment-management' ),
		);

		return $actions;
	}

	/**
	 * Returns columns for this List Table
	 */
	function get_columns() {
		$cols = array(
			'cb' => '<input type="checkbox" />',
			'id' => __( 'Test', 'equipment-management' ),
			'name' => __( 'Name', 'equipment-management' ),
			'category' => __( 'Category', 'equipment-management' ),
			'amount_availiable' => __( 'Amount availiable', 'equipment-management' ),
		);

		return $cols;
	}

	/**
	 * Function rendering the checkboxes column.
	 *
	 * @param Equipment_Management\includes\Item $item The Item the column is rendered for.
	 */
	public function column_cb( $item ) {
		// TODO: Check for permissions!
		// TODO: Actual ID!
		$id = rand();
		$title = 'Bla';
		?>
		<label class="screen-reader-text" for="cb-select-<?php echo esc_attr( $id ); ?>"><?php
		printf( esc_html__( 'Select %s', 'equipment-management' ), esc_html( $title ) );
		?></label>
		<input id="cb-select-<?php echo esc_attr( $id ); ?>" type="checkbox" name="item[]" value="<?php echo esc_attr( $id ); ?>" />
		<?php
	}

	/**
	 * Renders the column for columns without a special function.
	 *
	 * @param Equipment_Management\includes\Item $item The Item the column is rendered for.
	 * @param string                             $column_name The name of the column.
	 */
	public function column_default( $item, $column_name ) {
		return $item;
	}

	/**
	 * Prepare items for output.
	 *
	 * @see List_Table->prepare_items
	 */
	function prepare_items() {

		$per_page = 5;

		$columns = $this->get_columns();
		$hidden = array();
		$sortable = array();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		// TODO: Real data and sorting!
		$data = array(
			'a',
			'b',
			'c',
			'd',
			'e',
			'f',
			'g',
			'h',
		);

		$total_items = count( $data );

		$current_page = $this->get_pagenum();

		$data = array_slice( $data, (($current_page - 1) * $per_page), $per_page );

		/* Sorted and paginated data */
		$this->items = $data;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page' => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
			)
		);
	}

}
