<?php

namespace MerryCode\ColorBasedProductImport\Helpers;

// contains helper functions for the file importing functionality
use MerryCode\ColorBasedProductImport\Configs\Main;
use MerryCode\ColorBasedProductImport\Models\ImportSettings;
use WC_Data_Exception;
use WC_Product;
use WC_Product_Data_Store_CPT;
use WC_Product_Variation;

class ImportHelper
{
    /**
     * Starts the import functionality
     *
     * @return void
     *
     * @throws WC_Data_Exception
     */
    public static function importProducts(): void
    {
        global $woocommerce;

        $settingsObject = new ImportSettings();
        $cbpiSettings = $settingsObject->get();
        $currentLine = 0;

        $rowsPerInterval = intVal($cbpiSettings["rows_per_interval"]);
        $offset = intVal($cbpiSettings["offset"]);
        $limit = $rowsPerInterval + $offset;
        $csvRowCount = 0;


        if (($open = fopen($cbpiSettings["upload_file"], "r")) !== false) {
            $csvRows = array();

            while (($data = fgetcsv($open, 1000, $cbpiSettings["delimiter"])) !== false) {
                $csvRows[] = $data;
            }
            $csvRowCount = count($csvRows);

            if ($limit >= ($csvRowCount - 1)) {
                $cbpiSettings["offset"] = 0;
                $cbpiSettings["status"] = "done";
                $settingsObject->update($cbpiSettings);
                return;
            }

            foreach ($csvRows as $row => $data) {
                if ($currentLine == 0) {
                    $currentLine++;
                    continue;
                }
                // skipping Rows if required
                if ($currentLine > $limit) {
                    $cbpiSettings["offset"] = $limit;
                    $settingsObject->update($cbpiSettings);
                    wp_schedule_single_event(time(), Main::ACTION_PRODUCT_IMPORT);
                    return;
                }

                $currentLine++;

                $colorAttributeName = $cbpiSettings["color_attribute_name"];

                $productSku = (array_key_exists(0, $data)) ? $data[0] : '';
                $productName = (array_key_exists(1, $data)) ? $data[1] : '';
                $productDescription = (array_key_exists(2, $data)) ? $data[2] : '';
                $productColor = (array_key_exists(3, $data)) ? $data[3] : '';
                $productPrice = (array_key_exists(4, $data)) ? $data[4] : '';
                $productStock = (array_key_exists(4, $data)) ? $data[5] : '';
                $productTermToExport = $productColor;

                $productTermSlug = "";
                $productAttributeSlug = "";

                $existingProductId = wc_get_product_id_by_sku($productSku);

                if ($existingProductId) {
                    $productVariable = wc_get_product($existingProductId);
                } else {
                    $productVariable = new \WC_Product_Variable();
                }

                //Creating Variable Product from Data

                $productVariable->set_name($productName);
                $productVariable->set_description($productDescription);
                $productVariable->set_sku($productSku);
                $productVariable->save();

                //Adding Attributes
                $attributesAndTerms = ImportHelper::addProductAttribute(
                    $productVariable->get_id(),
                    [
                        '_attributes' => [
                            $colorAttributeName => [$productTermToExport],
                        ],
                    ]
                );

                $variation_attributes_of_existing_product = $productVariable->get_variation_attributes();

                $isAttributeFound = false;

                foreach ($attributesAndTerms as $attribute => $term) {
                    $productAttributeSlug = "pa_" . $attribute;
                    $productTermSlug = $term;
                }
                foreach ($productVariable->get_available_variations() as $key => $attributeValue) {
                    foreach ($attributeValue["attributes"] as $attributeKey => $attributeValue) {
                        if ($attributeKey == 'attribute_pa_color') {
                            if ($attributeValue == $productTermSlug) {
                                $isAttributeFound = true;
                            }
                        }
                    }
                }

                $existingVariationId=null;

                if ($isAttributeFound) {
                    $existingVariationId=ImportHelper::findMatchingProductVariationId(
                        $existingProductId,
                        [ "attribute_" . $productAttributeSlug => $productTermSlug]
                    );
                }

                // creating Variations
                if ($existingVariationId) {
                    $productVariation =  wc_get_product($existingVariationId);
                } else {
                    $productVariation = new WC_Product_Variation();

                    $productVariation->set_parent_id($productVariable->get_id());
                    $productVariation->set_attributes(array(
                        $productAttributeSlug => $productTermSlug
                    ));
                }
                
                
                $productVariation->set_regular_price($productPrice);
                $productVariation->set_manage_stock(true);
                $productVariation->set_stock_quantity($productStock);
                $productVariation->save();
            }

            fclose($open);
        }
    }

    /**
     * Searching for matching product variation id and returns it
     *
     * @param int   $product_id
     * @param array $attributes
     *
     * @return int
     */
    public static function findMatchingProductVariationId(int $product_id, array $attributes): int
    {
        return (new WC_Product_Data_Store_CPT())->find_matching_product_variation(
            new WC_Product($product_id),
            $attributes
        );
    }

    /**
     * Gets attribute Id from name
     *
     * @param $name
     *
     * @return string|null
     *
     */
    public static function getAttributeIdFromName($name): ?string
    {
        global $wpdb;
        $attributeId = $wpdb->get_var("SELECT attribute_id
    FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
    WHERE attribute_name LIKE '$name'");
        return $attributeId;
    }

    /**
     * Saves the product attribute from name
     *
     * @return void
     *
     */
    public static function saveProductAttributeFromName($name, $label = '', $set = true)
    {
        global $wpdb;

        $label = $label == '' ? ucfirst($name) : $label;
        $attributeId = ImportHelper::getAttributeIdFromName($name);
        $taxonomy = wc_attribute_taxonomy_name($name); // The taxonomy slug

        if (empty($attributeId)) {
            $attributeId = null;
        } else {
            $set = false;
        }

        //register existing taxonomy
        if (isset($attributeId) && !taxonomy_exists($taxonomy)) {
            ImportHelper::registerAttribute($name);
        }

        $args = array(
            'attribute_id'      => $attributeId,
            'attribute_name'    => $name,
            'attribute_label'   => $label,
            'attribute_type'    => 'select',
            'attribute_orderby' => 'menu_order',
            'attribute_public'  => 0,
        );

        if (empty($attributeId)) {
            $wpdb->insert("{$wpdb->prefix}woocommerce_attribute_taxonomies", $args);
            set_transient('wc_attribute_taxonomies', false);
        }

        if ($set) {
            $attributes = wc_get_attribute_taxonomies();
            $args['attribute_id'] = ImportHelper::getAttributeIdFromName($name);
            $attributes[] = (object)$args;
            //print_r($attributes);
            set_transient('wc_attribute_taxonomies', $attributes);
        }
    }

    /**
     * Registers a new attribute
     *
     * @param $name
     *
     * @return void
     */
    public static function registerAttribute($name): void
    {
        $taxonomy = wc_attribute_taxonomy_name($name); // The taxonomy slug
        $attr_label = ucfirst($name); // attribute label name
        $attr_name = (wc_sanitize_taxonomy_name($name)); // attribute slug

        register_taxonomy(
            $taxonomy,
            'product',
            array(
                'label'        => __($attr_label),
                'rewrite'      => array('slug' => $attr_name),
                'hierarchical' => true,
            )
        );
    }

    /**
     * Adds new product attribute
     *
     * @param int   $productId
     * @param array $data
     *
     * @return array
     */
    public static function addProductAttribute(int $productId, array $data) : array
    {
        $productAttributes = array();
        $termsToReturn = array();

        foreach ($data['_attributes'] as $key => $terms) {
            $taxonomy = wc_attribute_taxonomy_name($key); // The taxonomy slug
            $attributeLabel = ucfirst($key); // attribute label name
            $attributeName = (wc_sanitize_taxonomy_name($key)); // attribute slug


            // register and save new attributes
            if (!taxonomy_exists($taxonomy)) {
                ImportHelper::saveProductAttributeFromName($attributeName, $attributeLabel);
            }

            $productAttributes[$taxonomy] = array(
                'name'         => $taxonomy,
                'value'        => '',
                'position'     => '',
                'is_visible'   => 1,
                'is_variation' => 1,
                'is_taxonomy'  => 1
            );

            foreach ($terms as $value) {
                $termName = ucfirst($value);
                $termSlug = sanitize_title($value);

                $termsToReturn[$attributeName] = $termSlug;

                // check if the Term name exist and if not we create it.
                if (!term_exists($value, $taxonomy)) {
                    wp_insert_term($termName, $taxonomy, array('slug' => $termSlug));
                }
                // create the term
                // set attribute values
                wp_set_object_terms($productId, $termName, $taxonomy, true);
            }
        }
        update_post_meta($productId, '_product_attributes', $productAttributes);
        return $termsToReturn;
    }
}
