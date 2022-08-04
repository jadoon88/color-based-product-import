<?php

namespace MerryCode\ColorBasedProductImport;

use MerryCode\ColorBasedProductImport\Configs\Main;
use MerryCode\ColorBasedProductImport\Controllers\Admin\AdminController;
use MerryCode\ColorBasedProductImport\Controllers\FrontEnd\SwatchesController;

class Basic
{
    public function __construct()
    {
        // check to see if WooCommerce is active.
        $plugin_path = trailingslashit(WP_PLUGIN_DIR) . 'woocommerce/woocommerce.php';

        if (
            in_array($plugin_path, wp_get_active_and_valid_plugins())
        ) {
            // initialise only if WooCommerce is active
            $this->registerControllers();
        } else {
            //Shows a warning notice if WooCommerce is inactive.
            add_action('admin_notices', array( $this, 'adminNoticeForRequirements' ));
        }
    }
    /**
     * Registers or loads all required controllers
     *
     * @return void
     */
    public function registerControllers() : void
    {
        new AdminController();
        new SwatchesController();
    }
    /**
     * Shows warning on the backend if WooCommerce is not installed or activated
     *
     * @return void
     */
    private function adminNoticeForRequirements() : void
    {
        $class   = 'notice  notice-warning– ';
        $message = __(
			'iShark Product Options Requires WooCommerce 6.2 or higher to work', 'color-based-product-import',
	        Main::TEXT_DOMAIN
        );
        printf('<div class="%1$sa"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
    }
}
