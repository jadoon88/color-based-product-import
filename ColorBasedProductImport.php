<?php

/**
 * Plugin Name: Color Based Product Import
 * Plugin URI: https://merrycode.com
 * Description: Import product attributes using csv files and enable color swatches
 * Version: 1.0
 * Author: Umair Khan Jadoon
 * Author URI: https://www.merrycode.com/
 * Developer: MerryCode
 * Developer URI: https://www.merrycode.com/
 * Text Domain: color-based-product-import
 * Domain Path: /languages
 *
 *
 * Copyright: © 2009-2022 MerryCode.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

require 'vendor/autoload.php';

use MerryCode\ColorBasedProductImport\Basic;
use MerryCode\ColorBasedProductImport\Configs\Main;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('init', 'CBPIInit' );
register_activation_hook( __FILE__, 'CBPIActivate');

/**
 * Initializes the base class on init
 *
 * @return void
 */
function CBPIInit() : void
{
	new Basic();
}
/**
 * Creates the missing attribute on plugin activation
 *
 * @return void
 */
function CBPIActivate() : void
  {
    $attributes = wc_get_attribute_taxonomies();

    $slugs = wp_list_pluck( $attributes, 'attribute_name' );

    if ( ! in_array( Main::ATTRIBUTE_SLUG_COLOR_NO_PREFIX, $slugs ) ) {

        $args = array(
            'slug'    => Main::ATTRIBUTE_SLUG_COLOR_NO_PREFIX,
            'name'   => __( Main::ATTRIBUTE_SLUG_COLOR_NAME, Main::TEXT_DOMAIN ),
            'type'    => 'select',
            'orderby' => 'menu_order',
            'has_archives'  => false,
        );
      wc_create_attribute( $args );
    }
  }