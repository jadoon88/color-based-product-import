<?php

namespace MerryCode\ColorBasedProductImport\Controllers\FrontEnd;

use MerryCode\ColorBasedProductImport\Core\AbstractController;
use MerryCode\ColorBasedProductImport\Helpers\MainHelper;
use MerryCode\ColorBasedProductImport\Views\Admin\TermColorFieldView;
use MerryCode\ColorBasedProductImport\Views\FrontEnd\SwatchesView;

//Controller class to manage swatches

class SwatchesController extends AbstractController
{
   public function register_hook_callbacks()
   {
      //And ...ACTION(S)!
      add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_and_styles'));
      add_action('created_pa_color', array($this,'ishark_pa_color_save_term_fields'));
      add_action('edited_pa_color', array($this,'ishark_pa_color_save_term_fields'));
      add_action('pa_color_edit_form_fields', array($this,'ishark_pa_color_edit_term_fields'), 10, 2 );
      add_action('woocommerce_before_variations_form', array($this, 'ishark_render_swatches'));
   }


   public function enqueue_scripts_and_styles()
   {
      // Get the relative path to current file from plugin root
$file_path_from_plugin_root = str_replace(WP_PLUGIN_DIR . '/', '', __DIR__);

      wp_register_style('ishark-swatches', plugins_url(MainHelper::get_plugin_folder_name().'/assets/frontend/style.css'), false, '1.0');
      wp_enqueue_style('ishark-swatches');
      wp_register_script('ishark-swatches-script', plugins_url(MainHelper::get_plugin_folder_name().'/assets/frontend/script.js'), false, '1.0');
      wp_enqueue_script('ishark-swatches-script');
   }

   //Renders swatches on the frontend
   public function ishark_render_swatches()
   {
      global $product;
      if (is_a($product, 'WC_Product_Variable')) {

         return new SwatchesView($product);
   
      }
   }
   //Adds extra field to the term editor
   public function ishark_pa_color_edit_term_fields($term, $taxonomy)
   {

      $value = get_term_meta($term->term_id, 'ishark_pa_color', true);
      return new TermColorFieldView($value);
   }

   //Saves the data of extra field
   public function ishark_pa_color_save_term_fields($term_id)
   {

      update_term_meta(
         $term_id,
         'ishark_pa_color',
         sanitize_text_field($_POST['ishark_pa_color'])
      );
   }
}