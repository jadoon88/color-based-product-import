<?php

namespace MerryCode\ColorBasedProductImport\Controllers\Admin;

use MerryCode\ColorBasedProductImport\Configs\Main;
use MerryCode\ColorBasedProductImport\Core\AbstractController;
use MerryCode\ColorBasedProductImport\Helpers\ImportHelper;
use MerryCode\ColorBasedProductImport\Helpers\MainHelper;
use MerryCode\ColorBasedProductImport\Models\ImportSettings;
use WC_Data_Exception;

class AdminController extends AbstractController
{
    public const STYLES_HANDLE = Main::STYLES_PREFIX . '-admin';
    public const SCRIPTS_HANDLE = Main::SCRIPTS_PREFIX . '-admin-script';

    public const STYLES_URL = "/assets/admin/style.css";
    public const SCRIPTS_URL = "/assets/admin/script.js";

    public const LOCALIZED_SCRIPT_OBJECT = "cbpi_file_handle";

    public const VIEW_SETTINGS_PAGE = "TemplateImporterView";

    /**
     * Registers all hook callbacks for Admin controller
     *
     * @return void
     */
    protected function registerCallBackHooks(): void
    {
        //And ...ACTION(S)!
        add_action('admin_menu', array( $this, 'controllerMenu' ));
        add_action('admin_enqueue_scripts', array( $this, 'adminEnqueueScriptsAndStyles' ));
        add_action('wp_ajax_file_upload', array( $this, 'fileUploadCallback' ));
        add_action('wp_ajax_nopriv_file_upload', array( $this, 'fileUploadCallback' ));
        //Adding the products import hook
        add_action(Main::ACTION_PRODUCT_IMPORT, array( $this, 'productsImport' ));
    }


	/**
	 * Initiates the products import
	 *
	 * @return void
	 *             
	 * @throws WC_Data_Exception
	 */
    public function productsImport() : void
    {
        ImportHelper::importProducts();
    }

    /**
     * Enqueues all admin scripts
     *
     * @return void
     */
    public function adminEnqueueScriptsAndStyles() : void
    {
        wp_register_style(
            self::STYLES_HANDLE,
            plugins_url(MainHelper::getPluginFolderName() . self::STYLES_URL),
            false,
            Main::VERSION
        );
        wp_enqueue_style(self::STYLES_HANDLE);
        wp_register_script(
            self::SCRIPTS_HANDLE,
            plugins_url(MainHelper::getPluginFolderName() . self::SCRIPTS_URL),
            false,
            Main::VERSION
        );

        // Localizing the script
        $script_data_array = array(
            'ajaxurl'  => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('file_upload'),
        );
        wp_localize_script(self::SCRIPTS_HANDLE, self::LOCALIZED_SCRIPT_OBJECT, $script_data_array);
        wp_enqueue_script(self::SCRIPTS_HANDLE);
    }

    /**
     * File upload form call back to start the importer
     *
     * @return void
     */
    public function fileUploadCallback() : void
    {
        $settings_object = new ImportSettings();
        check_ajax_referer('file_upload', 'security');
        $ishark_options = $settings_object->get();

        $arr_img_ext = array( 'text/csv' );
        if (in_array($_FILES['file']['type'], $arr_img_ext)) {
            $ishark_options["delimiter"] = $_POST["delimiter"];
            $ishark_options["status"]    = "in-progress";

            $settings_object->update($ishark_options);

            $upload                        = wp_upload_bits(
                $_FILES["file"]["name"],
                null,
                file_get_contents($_FILES["file"]["tmp_name"])
            );
            $ishark_options["upload_file"] = $upload["file"];
            $settings_object->update($ishark_options);

            //scheduling the cron job
            wp_schedule_single_event(time(), Main::ACTION_PRODUCT_IMPORT);
        }
        wp_die();
    }

    /**
     * Adds menu page to the settings
     *
     * @return void
     */
    public function controllerMenu() : void
    {
        add_menu_page(
            Main::getMenuPageTitle(),
            Main::getMenuPageTitle(),
            Main::MENU_CAPABILITY,
            Main::MENU_SLUG,
            array( $this, 'renderSettingsPage' ),
            Main::MENU_ICON,
            Main::MENU_POSITION
        );
    }

    /**
     * Renders the settings page in the backend
     *
     * @return void
     */
    public function renderSettingsPage() : void
    {
        $settingsObject = new ImportSettings();
        $settings = $settingsObject->get();
        //ToDo verify nonce .. if else needs refactor
        if (isset($_POST[Main::POST_ARGUMENT_COMMAND])) {
            if ($_POST[Main::POST_ARGUMENT_COMMAND] == Main::POST_ARGUMENT_VALUE_NEW_IMPORT) {
                $settings[Main::SETTINGS_KEY_STATUS] = Main::IMPORT_STATUS_IDLE;
                $settingsObject->update($settings);
            }
        }
        //Rendering the settings page from the view
        MainHelper::loadTemplate(self::VIEW_SETTINGS_PAGE, $settings);
    }
}
