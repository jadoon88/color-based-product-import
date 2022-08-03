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

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

add_action('init', 'is_product_options_init');
 //Adding activation hook
 register_activation_hook( __FILE__, 'ishark_product_options_on_activate' );


function is_product_options_init()
{
    //Initializing the base class
	new Basic();
}
function ishark_product_options_on_activate()
  {
    //Adding Color attribute for products 
    $attributes = wc_get_attribute_taxonomies();

    $slugs = wp_list_pluck( $attributes, 'attribute_name' );

    if ( ! in_array( 'color', $slugs ) ) {

        $args = array(
            'slug'    => 'color',
            'name'   => __( 'Colors', 'color-based-product-import' ),
            'type'    => 'select',
            'orderby' => 'menu_order',
            'has_archives'  => false,
        );

        $result = wc_create_attribute( $args );

    }
  }