function wc_yotpo_show_rs() {
	// Manual markup for Yotpo/WooCom By Paul Glushak
	// Updates & stuff: https://github.com/yotpaul/WooCustom
	// ---
	// usage example: if(is_product()) {add_action('woocommerce_before_single_product', 'wc_yotpo_show_rs', 5);}
	global $product;
	$product_data = wc_yotpo_get_product_data($product);
	$id = $product_data['id'];
	$title = $product_data['title'];
	$description = $product_data['description'];
	$availability = 'https://schema.org/' . ( $product->is_in_stock() ? 'InStock' : 'OutOfStock' );
	$image = $product_data['image-url'];
	$price = $product->get_price();
	$currency = get_woocommerce_currency();
	$sku = $product->get_sku();
	$yotpo_settings = get_option('yotpo_settings', wc_yotpo_get_degault_settings());
	$app_key = $yotpo_settings['app_key'];
	$url = 'https://api.yotpo.com/products/'.$app_key.'/'.$id.'/bottomline';
	$json = (get_headers($url)[0] == "HTTP/1.1 200 OK") ? file_get_contents($url) : null;
	if (!is_null($json)) {$data = json_decode($json);}
	if (!is_null($data) && $data->status->code == 200) {
		$avg = $data->response->bottomline->average_score ?: 0;
		$total = $data->response->bottomline->total_reviews ?: 0;
		$rs = '
			<script type="application/ld+json" class="y-richsnippet">
				{
				    "@context": "http://schema.org",
				    "@graph": [
				        {
				            "@type": "Product",
				            "name": "'.$title.'",
				            "image": "'.$image.'",
				            "sku": "'.$sku.'",
				            "itemCondition": "http://schema.org/NewCondition",
				            "description": "'.$description.'",
				            "offers": {
				                "@type": "Offer",
				                "availability": "'.$availability.'",
				                "price": "'.$price.'",
				                "priceCurrency": "'.$currency.'"
				            },
				            "aggregateRating": {
				                "@type": "AggregateRating",
				                "ratingValue": "'.$avg.'",
				                "reviewCount": "'.$total.'"
				            }
						}
					]
				}
			</script>';
		echo $rs;
	}
}
