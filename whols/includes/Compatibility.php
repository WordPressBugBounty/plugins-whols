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
}
