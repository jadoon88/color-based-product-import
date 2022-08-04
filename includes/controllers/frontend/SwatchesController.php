<?php

namespace MerryCode\ColorBasedProductImport\Controllers\FrontEnd;

use MerryCode\ColorBasedProductImport\Configs\Main;
use MerryCode\ColorBasedProductImport\Core\AbstractController;
use MerryCode\ColorBasedProductImport\Helpers\MainHelper;
use WP_Term;

// controller class to manage swatches

class SwatchesController extends AbstractController
{
    public const STYLES_HANDLE  = Main::STYLES_PREFIX.'-swatches';
    public const SCRIPTS_HANDLE = Main::SCRIPTS_PREFIX.'-swatches-script';

    public const STYLES_URL  = "/assets/frontend/style.css";
    public const SCRIPTS_URL = "/assets/frontend/script.js";

    public const VIEW_SWATCHES    = "TemplateSwatchesView";
    public const VIEW_COLOR_FIELD = "TemplateTermColorFieldView";

    /**
     * Registering call back hooks for color swatches
     *
     * @return void
     */
    protected function registerCallBackHooks() : void
    {
        // and ...ACTION(S)!
        add_action('wp_enqueue_scripts', array($this, 'enqueueScriptsAndStyles' ));
        add_action('created_pa_color', array($this, 'saveColorTermField' ));
        add_action('edited_pa_color', array($this, 'saveColorTermField' ));
        add_action('pa_color_edit_form_fields', array($this, 'editColorTermField' ), 10, 2);
        add_action('woocommerce_before_variations_form', array($this, 'renderSwatches' ));
    }

    /**
     * Registering call back hooks for color swatches
     *
     * @return void
     */
    public function enqueueScriptsAndStyles() : void
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
        wp_enqueue_script(self::SCRIPTS_HANDLE);
    }

    /**
     * Renders swatches on the frontend
     *
     * @return void
     */
    public function renderSwatches() : void
    {
        global $product;
        if (is_a($product, 'WC_Product_Variable')) {
            $data['variationAttributes'] = $product->get_variation_attributes()[Main::ATTRIBUTE_SLUG_COLOR];
            MainHelper::loadTemplate(self::VIEW_SWATCHES, $data);
        }
    }
    /**
     * Renders extra field to the term editor
     *
     * @param WP_Term $term
     *
     * @return void
     */
    public function editColorTermField(WP_Term $term): void {

        $data["value"] = get_term_meta($term->term_id, Main::TERM_META_KEY, true);
        MainHelper::loadTemplate(self::VIEW_COLOR_FIELD, $data);
    }

	/**
	 * Saves the data of extra field
	 *
	 * @param int $termId
	 *
	 * @return void
	 */
    public function saveColorTermField(int $termId): void {
        update_term_meta(
            $termId,
            Main::TERM_META_KEY,
            sanitize_text_field($_POST[Main::TERM_META_KEY])
        );
    }
}
