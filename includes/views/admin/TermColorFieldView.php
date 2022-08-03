<?php

namespace MerryCode\ColorBasedProductImport\Views\Admin;

//Renders view for the color field on term edit
use MerryCode\ColorBasedProductImport\Core\AbstractView;

class TermColorFieldView extends AbstractView
{

   public function render_view($variables)
   {
      $value=$variables;
?>
    <tr class="form-field">
    <th>
        <label for="ishark_pa_color"><?= __("Color","color-based-product-import"); ?></label>
    </th>
    <td>
        <input name="ishark_pa_color" id="ishark_pa_color" type="color" class="wpColorChoose" value="<?= esc_attr($value); ?>" />
    </td>
    </tr>;

      <?php

   }
}
      ?>