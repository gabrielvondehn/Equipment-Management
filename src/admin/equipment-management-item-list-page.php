<?php
/**
 * The main list view of items.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package Equipment_Management
 * @subpackage Equipment_Management/admin
 */

namespace Equipment_Management\admin;

// TODO: Nonce checks!
?>

<div class="wrap">
	<h1><?php esc_html_e( 'Equipment', 'equipment-management' ); ?>
		<a href="<?php echo esc_url( admin_url( 'admin.php?page=equipment-add' ) ); ?>" class="page-title-action"><?php esc_html_e( 'Add Equipment', 'equipment management' ); ?></a>
	</h1><?php
	// TODO: Check for events and give user feedback!
	/* Load List Table class file */
	require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/class-item-list-table.php';

	/* Aliasing List Table */
	use Equipment_Management\admin\includes\Item_List_Table;

	$list_table = new Item_List_Table();
?>
	<form method="post"><?php
		$list_table->prepare_items();
		$list_table->display();
	?>
	</form>
</div>
