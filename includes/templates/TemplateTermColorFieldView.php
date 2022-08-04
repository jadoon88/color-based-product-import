<?php

use MerryCode\ColorBasedProductImport\Configs\Main;

/** @var array $data */

$value = $data["value"];
?>
    <tr class="form-field">
        <th>
            <label for="<?= Main::TERM_META_KEY ?>"><?= __("Color",Main::TEXT_DOMAIN); ?></label>
        </th>
        <td>
            <input
                    name="<?= Main::TERM_META_KEY ?>"
                    id="<?= Main::TERM_META_KEY ?>"
                    type="color"
                    class="wpColorChoose"
                    value="<?= esc_attr($value); ?>"
            />
        </td>
    </tr>;