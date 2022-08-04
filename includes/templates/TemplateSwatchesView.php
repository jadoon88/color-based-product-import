<?php

use MerryCode\ColorBasedProductImport\Configs\Main;

/** @var array $data */

$variationAttributes = $data["variationAttributes"];
?>
<b><?= __("Available Colors:", Main::TEXT_DOMAIN); ?></b>
<div class="ishark-color-swatches">

	<?php
    foreach ($variationAttributes as $key => $colorSlug) {
        $term       = get_term_by('name', $colorSlug, Main::ATTRIBUTE_SLUG_COLOR);
        $termValue  = get_term_meta($term->term_id, Main::TERM_META_KEY, true); ?>

        <div class="ishark-color-box" data-val="<?= $colorSlug ?>" data-name="<?= $term->name ?>"
             style="background-color:<?= $termValue; ?>"></div>

		<?php
    }

?>
</div>
<div class="ishark-selected-color"><?= __("Selected Color:", Main::TEXT_DOMAIN); ?>
    <strong>
		<?php _e("Selected Color:", Main::TEXT_DOMAIN); ?>
    </strong>
</div>
