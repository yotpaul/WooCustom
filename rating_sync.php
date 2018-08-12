<?php

// Rating sync for Yotpo/WooCom By Paul Glushak
// Updates & stuff: https://github.com/yotpaul/WooCustom
// ---
// This will sync product ratings daily which will allow you to use rating sort in WooCommerce

// wc_yotpo.php +------------------->
// --- new action - add at top
add_action('rating_sync_hook', 'wc_yotpo_update_product_ratings');

// --- new default setting
//  'rating_sync' => false

// --- new functions - add at bottom
function wc_yotpo_get_product_rating($product) {
	$settings = get_option('yotpo_settings', wc_yotpo_get_degault_settings());
	$app_key = $settings['app_key'];
	$url = 'https://api.yotpo.com/products/'.$app_key.'/'.$product.'/bottomline';
	$json = (get_headers($url)[0] == "HTTP/1.1 200 OK") ? file_get_contents($url) : null;
	if (!is_null($json)) {$data = json_decode($json);} else { return 0; }
	if (!is_null($data) && $data->status->code == 200) {
		return $data->response->bottomline->average_score ?: 0;
	}
}

function wc_yotpo_update_product_ratings(){
	$args = array(
		'post_type' => 'product',
		'post_status'    => 'publish',
		'posts_per_page'    => -1
	);
	$products = new WP_Query( $args );
	if ( $products->have_posts() ) {
		while ( $products->have_posts() ) : $products->the_post();
		global $product;
		$product->set_average_rating(wc_yotpo_get_product_rating($product->get_id()));
		$data_store = $product->get_data_store();
		$data_store->update_average_rating($product);
		endwhile;
	}

// wc-yotpo-settings.php +------------------->
// --- new setting html
<tr valign='top'>
  <th scope='row'><label for='rating_sync'>Sync product ratings</label></th>
  <td><input type='checkbox' name='rating_sync' value='1' " . checked(1, $yotpo_settings['rating_sync'], false) . " /></td>
</tr>

// --- new setting process
//  'rating_sync' => isset($_POST['rating_sync']) ? true : false);

// --- setting actions - add after update_option('yotpo_settings', $new_settings);
	if ($new_settings['rating_sync'] == true && !wp_next_scheduled('rating_sync_hook')) {
		wp_schedule_event( time(), 'daily', 'rating_sync_hook' );
	} elseif ($new_settings['rating_sync'] == false && wp_next_scheduled('rating_sync_hook')) {
		wp_clear_scheduled_hook('rating_sync_hook');
	}

}
