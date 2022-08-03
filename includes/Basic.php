<?php

namespace MerryCode\ColorBasedProductImport;

use MerryCode\ColorBasedProductImport\Controllers\Admin\AdminController;
use MerryCode\ColorBasedProductImport\Controllers\FrontEnd\SwatchesController;

class Basic {

	public function __construct() {

		// Check to see if WooCommerce is active.
		$plugin_path = trailingslashit( WP_PLUGIN_DIR ) . 'woocommerce/woocommerce.php';

		if (
			in_array( $plugin_path, wp_get_active_and_valid_plugins() )
		) {
			//Initilize only if WooCommerce is active
			$this->auto_load();
			$this->register_controllers();

		} else {
			//Shows a warning notice if WooCommerce is inactive.
			add_action( 'admin_notices', array( $this, 'requirements_admin_notice' ) );
		}
	}

	public function auto_load() {
		//Including all controller classes
//		include plugin_dir_path( __DIR__ ) . 'core/AbstractController.php';
//		include plugin_dir_path( __DIR__ ) . 'core/AbstractView.php';
//		include plugin_dir_path( __DIR__ ) . 'core/AbstractModel.php';
//		include plugin_dir_path( __DIR__ ) . 'app/controllers/admin/AdminSettings.php';
//		include plugin_dir_path( __DIR__ ) . 'app/controllers/frontend/SwatchesController.php';
//		include plugin_dir_path( __DIR__ ) . 'app/views/admin/ImporterView.php';
//		include plugin_dir_path( __DIR__ ) . 'app/views/frontend/SwatchesView.php';
//		include plugin_dir_path( __DIR__ ) . 'app/views/admin/TermColorFieldView.php';
//		include plugin_dir_path( __DIR__ ) . 'app/models/ImportSettings.php';
//		include plugin_dir_path( __DIR__ ) . 'includes/ImportHelper.php';
//		include plugin_dir_path( __DIR__ ) . 'includes/MainHelper.php';
	}

	public function register_controllers() {
		// initializing all controllers
		new AdminController();
		new SwatchesController();
	}

	function requirements_admin_notice() {
		$class   = 'notice  notice-warning– ';
		$message = __( 'iShark Product Options Requires WooCommerce 6.2 or higher to work', 'color-based-product-import' );

		printf( '<div class="%1$sa"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $message ) );
	}

}
