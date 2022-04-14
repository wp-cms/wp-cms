<?php
/**
 * Customize API: WP_Customize_Image_Control class
 *
 * @package ClassicPress
 * @subpackage Customize
 * @since WP-4.4.0
 */

/**
 * Customize Image Control class.
 *
 * @since WP-3.4.0
 *
 * @see WP_Customize_Upload_Control
 */
class WP_Customize_Image_Control extends WP_Customize_Upload_Control {
	public $type = 'image';
	public $mime_type = 'image';
}
