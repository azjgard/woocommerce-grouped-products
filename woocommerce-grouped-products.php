<?php
/**
 * Plugin Name:     Woocommerce Grouped Products
 * Plugin URI:      PLUGIN SITE HERE
 * Description:     PLUGIN DESCRIPTION HERE
 * Author:          YOUR NAME HERE
 * Author URI:      YOUR SITE HERE
 * Text Domain:     woocommerce-grouped-products
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Woocommerce_Grouped_Products
 */

function wcgp_get_quantity_options_by_sku( $sku ) {
	global $wpdb;

	$product_ids = $wpdb->get_results($wpdb->prepare(
	    "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_sku' AND meta_value LIKE '%s'", $sku.'-%'
    ));

	if ($product_ids) return $product_ids;

	return null;
}

function wcgp_get_product_quantity_by_sku( $sku ) {
    return explode('-', $sku)[1];
}

function wcgp_group_products() {
    // TODO: replace this with the SKU of the current product in the loop
    $product_sku = 392611989;

	$shop_url    = get_permalink(wc_get_page_id('shop'));
	$product_ids = wcgp_get_quantity_options_by_sku($product_sku);

	echo '<select class="wcgp-select">';
	echo '<option value selected>Select Qty/Pk</option>';

    foreach ($product_ids as $product_object) {
        // initialize the product variant
    	$product_id    = $product_object->post_id;
    	$product       = new WC_Product($product_id);

    	// get quantity and price of the product variant
    	$product_qty   = wcgp_get_product_quantity_by_sku($product->get_sku());
    	$product_price = $product->get_price();

    	// link to add the product to the cart
        $product_variant_link = $shop_url . '?add-to-cart=' . $product_id;
        $product_variant_text = $product_qty . ' - $' . money_format('%i', (int)$product_price);

        echo '<option value="' . $product_variant_link . '">' . $product_variant_text . '</option>';
    }

    echo '</select>';
}

// TODO: add JavaScript and CSS for styling
// Add dropdown to individual product pages
add_action('woocommerce_before_add_to_cart_button', 'wcgp_group_products');

// TODO: add JavaScript and CSS for styling
// TODO: find the proper action to add it in the right place
// Add dropdown to product archive page
add_action('woocommerce_after_shop_loop_item_title', 'wcgp_group_products');


