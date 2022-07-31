<?php
/**
 * Custom implementation for PHP functions either missing from older PHP versions or not included by default.
 */

// If gettext isn't available
if ( ! function_exists( '_' ) ) {
	function _( $string ) {
		return $string;
	}
}
