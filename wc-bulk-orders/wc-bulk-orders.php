<?php
/*
Plugin Name: Bulk Orders for WooCommerce
Description: This plugin lets you create bulk WooCommerce orders.
Version: 0.0.1
Author: Khyati Gala
Author URI: https://profiles.wordpress.org/galakhyati
*/
class Woo_Bulk_Orders{
	public function __construct() {
		add_action( 'admin_menu', array(&$this, 'woo_create_menu' ) );
		add_action( 'bkap_create_orders', array( &$this, 'create_orders' ));
		add_action( 'init', array( 'Woo_Bulk_Orders', 'wbo_load_ajax' ) );
	}

	//Register ajax
	public static function wbo_load_ajax() {
		if ( !is_user_logged_in() ){
			add_action( 'wp_ajax_nopriv_create_bulk_orders', array( 'Woo_Bulk_Orders', 'create_bulk_orders' ) );
		} else{
			add_action( 'wp_ajax_create_bulk_orders',  array( 'Woo_Bulk_Orders', 'create_bulk_orders' ) );
		}
	}

	//Create a new menu
	public static function woo_create_menu(){
		add_menu_page( 'Create Bulk WooCommerce Orders', 'Bulk Orders', 'manage_woocommerce','create-bulk-orders', array( 'Woo_Bulk_Orders', 'bulk_order_button_show' ), $icon_url = '', 75 );
		//add_submenu_page( string $parent_slug, string $page_title, string $menu_title, string $capability, string $menu_slug, callable $function = '' )
	}

	//Capture the number of orders to be created
	public static function bulk_order_button_show(){
		?>
		<div class="wrap">
			<h1 class=wp-heading-inline>Create Bulk WooCommerce Orders</h1>
		</div>
		<p>Enter the number of WooCommerce Orders you want to create</p>
		<table class ="form-table">
		<tbody>
		<tr valign="top">
		<th>
		<label for="order-number">Number of Orders</label>
		</th>
		<td>
		<input type="text" name="order-number">
		</td>
		</tr>
		</tbody>
		</table>
		<table>
		<button id="btn-click" name="button" class = "button-primary woocommerce-save-button">Create Orders</button>
		</table>
		<div style="margin-left:30px;display: none;" id = "create-orders">Orders are getting created...</div>
		<div style="margin-left:30px;display: none;" id = "orders-created">Orders are created.</div>
		<span></span>
        <script>
        jQuery( "#btn-click" ).click(function(){
        	if ( jQuery( "input:first" ).val( ) ) {
        		//jQuery( "span" ).text( "Orders Created" ).show();
        		var data = {
					order_number: jQuery( "input:first" ).val(),
					action: "create_bulk_orders",
				};
				jQuery("#create-orders").show();
				jQuery.post( '<?php echo get_admin_url() . 'admin-ajax.php'; ?>', data, function( response ) {
					//alert(response);\
					jQuery("#create-orders").hide();
					//alert("Orders Created.");
					jQuery("#orders-created").show();
					jQuery( "input:first" ).val("");
				});
        	}
        });
        </script>
        <?php
    }

	public static function create_bulk_orders(){

		global $woocommerce;

		$order_id = $_POST['order_number'];

		$shipping_address = array(
			'first_name' => 'Khyati',
			'last_name'  => 'gala',
			'company'    => 'Tyche Softwares',
			'email'      => 'khyati@tychesoftwares.com',
			'phone'      => '123-123-123',
			'address_1'  => '123 Main Woo st.',
			'address_2'  => '100',
			'city'       => 'San Francisco',
			'state'      => 'Ca',
			'postcode'   => '92121',
			'country'    => 'US'
		);
		$billing_address = array(
			'first_name' => 'Khyati',
			'last_name'  => 'Gala',
			'company'    => 'Tyche Softwares',
			'email'      => 'khyati@tychesoftwares.com',
			'phone'      => '8454811783',
			'address_1'  => 'Borivali West',
			'address_2'  => '',
			'city'       => 'Mumbai',
			'state'      => 'MH',
			'postcode'   => '400094',
			'country'    => 'IN'
		);
		$number = $_POST['order_number'];
		//echo "Number of records to add: " . $number; 
		for ($i = 0; $i < $number; $i++ ){
			// Now we create the order
			$order = wc_create_order();
			
			// The add_product() function below is located in /plugins/woocommerce/includes/abstracts/abstract_wc_order.php
			$order->add_product( wc_get_product( 9 ), 2); // Use the product IDs to add
			
			// Set addresses
			$order->set_address( $billing_address, 'billing' );
			$order->set_address( $shipping_address, 'shipping' );
			
			// Set payment gateway
			$payment_gateways = WC()->payment_gateways->payment_gateways();
			$order->set_payment_method( $payment_gateways['bacs'] );

			// Calculate totals
			$order->calculate_totals();
			$order->update_status( 'completed', 'Order created dynamically - ', TRUE);
		}
	}
}
$bkaporder = new Woo_Bulk_Orders();