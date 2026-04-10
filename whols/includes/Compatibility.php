<?php
namespace Whols;

/**
 * Compatibility Class
 */
class Compatibility {
    /**
	 * Constructor
	 */
	public function __construct() {
		// Fibosearch compatibility
		add_filter('dgwt/wcas/search_query/args', array($this, 'fibosearch_search_query_args'), 10, 2);

		// Compatible with CURCY - Multi Currency by VillaTheme
		add_filter( 'whols_override_wholesale_price', array($this, 'curcy_override_wholesale_price'), 10, 2);

		// WooCommerce Product Export - fix empty quantities
		add_filter('woocommerce_product_export_meta_value', array($this, 'fix_export_quantity'), 10, 4);
    }

	public function fibosearch_search_query_args($args) {
		if( !whols_is_wholesaler() ) {
			$args['meta_query'][] = array(
				'key'     => '_whols_mark_this_product_as_wholesale_only',
				'value'   => 'yes',
				'compare' => '!=',
			);
		}
	
		return $args;
	}

	public function curcy_override_wholesale_price( $price_arr ) {
		$currentCurrencyRate = $this->curcy_get_currency_rate();

		if( $currentCurrencyRate ){
			if( !empty( $price_arr['price']) ){
				$price_arr['price'] = floatval($price_arr['price']) * $currentCurrencyRate;
			}
	
			if( !empty( $price_arr['min_price']) ){
				$price_arr['min_price'] = floatval($price_arr['min_price']) * $currentCurrencyRate;
			}
	
			if( !empty( $price_arr['max_price']) ){
				$price_arr['max_price'] = floatval($price_arr['max_price']) * $currentCurrencyRate;
			}
		}

		return $price_arr;
	}

	public function curcy_get_currency_rate(){
		$currentCurrencyRate = null;

		// For free version
		if(class_exists('\WOOMULTI_CURRENCY_F_Data')) {
			$multiCurrencySettings = \WOOMULTI_CURRENCY_F_Data::get_ins();
			$wmcCurrencies = $multiCurrencySettings->get_list_currencies();
			$currentCurrency = $multiCurrencySettings->get_current_currency();
			$currentCurrencyRate = floatval( $wmcCurrencies[ $currentCurrency ]['rate'] );
		}

		// For pro version
		if(class_exists('\WOOMULTI_CURRENCY_Data')) {
			$multiCurrencySettings = \WOOMULTI_CURRENCY_Data::get_ins();
			$wmcCurrencies = $multiCurrencySettings->get_list_currencies();
			$currentCurrency = $multiCurrencySettings->get_current_currency();
			$currentCurrencyRate = floatval( $wmcCurrencies[ $currentCurrency ]['rate'] );
		}

		return $currentCurrencyRate;
	}

	/**
	 * Fix empty quantities in wholesale pricing meta during export.
	 *
	 * @param mixed  $value   Meta value.
	 * @param object $meta    Meta object.
	 * @param object $product Product object.
	 * @param array  $row     Export row data.
	 * @return mixed
	 */
	public function fix_export_quantity($value, $meta, $product, $row) {
		if (empty($value)) {
			return $value;
		}

		// Type 1: Single role pricing (format: price:quantity)
		if ($meta->key === '_whols_price_type_1_properties') {
			$parts = explode(':', $value);
			if (count($parts) >= 1) {
				$price = $parts[0];
				$quantity = isset($parts[1]) && $parts[1] !== '' ? $parts[1] : '1';
				return $price . ':' . $quantity;
			}
		}

		// Type 2: Multiple role pricing (format: role:price:quantity;)
		if ($meta->key === '_whols_price_type_2_properties') {
			$rules = explode(';', $value);
			$fixed_rules = array();

			foreach ($rules as $rule) {
				$rule = trim($rule);
				if (empty($rule)) {
					continue;
				}

				$parts = explode(':', $rule);
				if (count($parts) >= 2) {
					$role = $parts[0];
					$price = $parts[1];
					$quantity = isset($parts[2]) && $parts[2] !== '' ? $parts[2] : '1';
					$fixed_rules[] = $role . ':' . $price . ':' . $quantity;
				}
			}

			return !empty($fixed_rules) ? implode(';', $fixed_rules) . ';' : $value;
		}

		return $value;
	}
}
