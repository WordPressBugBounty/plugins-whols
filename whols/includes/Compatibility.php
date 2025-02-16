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
}
