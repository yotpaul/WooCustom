<?

function wc_yotpo_get_orderstatus($setting) {
	// Custom status support for Yotpo/WooCom By Paul Glushak
	// Updates & stuff: https://github.com/yotpaul/WooCustom
	// ---
  // $setting is current order status as saved in the settings
  // usage: add function to wc-yotpo-settings.php, change HTML from the usual hardcoded stuff and call the function wc_yotpo_get_orderstatus($yotpo_settings['yotpo_order_status'])
  // see example below
	$statuses = wc_get_order_statuses();
	if (!array_key_exists($setting,$statuses)) {
		wc_yotpo_display_message('Warning - The current Custom Order Status setting ('.$setting.') does <strong>NOT</strong> exist in your WooCommerce instance. <br>It is recommended that you update your settings to avoid missing orders!', true);
	} elseif (is_null($setting) || empty($setting)) {
		wc_yotpo_display_message('Warning - The current Custom Order Status setting is <strong>NULL</strong>. <br>It is recommended that you update your order status setting to avoid missing orders!', true);
	}
	$html = '<select name="yotpo_order_status" class="yotpo-order-status" id="order-status">';
	foreach ($statuses as $k => $v) {
		$html .= '<option value="'.$k.'"'.selected($k, $setting, false).'>'.$v.'</option>';
	}
	$html .= '</select>';
	return $html;
}

// Example:
// <tr valign='top'>
// 	<th scope='row'><div>Order Status:</div></th>
// 	<td>
// 		".wc_yotpo_get_orderstatus($yotpo_settings['yotpo_order_status'])."
// 	</td>
// </tr>

?>
