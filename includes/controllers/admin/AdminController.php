<?php

namespace MerryCode\ColorBasedProductImport\Controllers\Admin;

use MerryCode\ColorBasedProductImport\Core\AbstractController;
use MerryCode\ColorBasedProductImport\Helpers\MainHelper;
use MerryCode\ColorBasedProductImport\Models\ImportSettings;
use MerryCode\ColorBasedProductImport\Views\Admin\ImporterView;
use \MerryCode\CoreHelpers\ImportHelper as ImportHelper;

class AdminController extends AbstractController {

	public function register_hook_callbacks() {
		//And ...ACTION(S)!
		add_action( 'admin_menu', array( $this, 'controller_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts_and_styles' ) );
		add_action( 'wp_ajax_file_upload', array( $this, 'file_upload_callback' ) );
		add_action( 'wp_ajax_nopriv_file_upload', array( $this, 'file_upload_callback' ) );
		//Adding the products import hook
		add_action( 'ishark_products_import', array( $this, 'products_import' ) );
	}


	public function products_import() {
		ImportHelper::import_products();
	}

	public function admin_enqueue_scripts_and_styles() {
		wp_register_style( 'ishark-admin', plugins_url( MainHelper::get_plugin_folder_name() . '/assets/admin/style.css' ), false, '1.0' );
		wp_enqueue_style( 'ishark-admin' );
		wp_register_script( 'ishark-admin-script', plugins_url( MainHelper::get_plugin_folder_name() . '/assets/admin/script.js' ), false, '1.0' );

		// Localizing the script
		$script_data_array = array(
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
			'security' => wp_create_nonce( 'file_upload' ),
		);
		wp_localize_script( 'ishark-admin-script', 'ishark_file_handle', $script_data_array );
		wp_enqueue_script( 'ishark-admin-script' );

	}

	function file_upload_callback() {

		//File upload form call back to start the importer
		$settings_object = new ImportSettings();
		check_ajax_referer( 'file_upload', 'security' );
		$ishark_options = $settings_object->get();

		$arr_img_ext = array( 'text/csv' );
		if ( in_array( $_FILES['file']['type'], $arr_img_ext ) ) {

			$ishark_options["delimiter"] = $_POST["delimiter"];
			$ishark_options["status"]    = "in-progress";

			$settings_object->update( $ishark_options );

			$upload                        = wp_upload_bits( $_FILES["file"]["name"], null, file_get_contents( $_FILES["file"]["tmp_name"] ) );
			$ishark_options["upload_file"] = $upload["file"];
			$settings_object->update( $ishark_options );

			//scheduling the cron job
			wp_schedule_single_event( time(), 'ishark_products_import' );
		}
		wp_die();
	}

	public function controller_menu() {
		//Adding menu in the settings
		add_menu_page(
			__( 'iShark Options', 'color-based-product-import' ),
			'iShark Options',
			'manage_options',
			'ishark_settings',
			array( $this, 'render_ishark_settings_page' ),
			'dashicons-admin-generic',
			6
		);

	}

	public function render_ishark_settings_page() {
		$settings_object = new ImportSettings();
		$ishark_settings = $settings_object->get();

		if ( isset( $_POST["ishark_command"] ) ) {
			if ( $_POST["ishark_command"] == "new_import" ) {
				$ishark_settings["status"] = "idle";
				$settings_object->update( $ishark_settings );

			}
		}

		//Rendering the settings page from the view
		return new ImporterView( $ishark_settings );

	}


}
