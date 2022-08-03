<?php

namespace MerryCode\ColorBasedProductImport\Views\FrontEnd;

//Renders view for the front end swatches

use MerryCode\ColorBasedProductImport\Core\AbstractView;

class SwatchesView extends AbstractView
{
   public function render_view($variables)
   {
      $product = $variables;
?>
      <b><?= __("Available Colors:","color-based-product-import"); ?></b>
      <div class="ishark-color-swatches">


         <?php
         foreach ($product->get_variation_attributes()["pa_color"] as $key => $color_slug) {
            $term = get_term_by('name', $color_slug, 'pa_color');
            $term_value = get_term_meta($term->term_id, 'ishark_pa_color', true);

         ?>


            <div class="ishark-color-box" data-val="<?= $color_slug ?>" data-name="<?= $term->name ?>" style="background-color:<?= $term_value; ?>"></div>

         <?php

         }

         ?>
      </div>
      <div class="ishark-selected-color"><?php _e("Selected Color:", "color-based-product-import"); ?><strong>None</strong></div>

<?php

   }
}
?>