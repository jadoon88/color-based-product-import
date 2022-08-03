<?php

namespace MerryCode\ColorBasedProductImport\Helpers;

//Contains helper functions for the file importing functionality
use MerryCode\ColorBasedProductImport\Models\ImportSettings;

class ImportHelper
{
    public static function import_products()
    {
        global $woocommerce;

        $settings_object = new ImportSettings();
        $ishark_settings = $settings_object->get();
        $current_line = 0;

        $rows_per_interval = intVal($ishark_settings["rows_per_interval"]);
        $offset = intVal($ishark_settings["offset"]);
        $limit = $rows_per_interval + $offset;
        $csv_rows_count = 0;


        if (($open = fopen($ishark_settings["upload_file"], "r")) !== FALSE) {
            $csv_rows = array();

            while (($data = fgetcsv($open, 1000, $ishark_settings["delimiter"])) !== FALSE) {
                $csv_rows[] = $data;
            }
            $csv_rows_count = count($csv_rows);

            if ($limit >= ($csv_rows_count - 1)) {
              
                $ishark_settings["offset"] = 0;
                $ishark_settings["status"] = "done";
                $settings_object->update($ishark_settings);
                return;
            }


            foreach ($csv_rows as $row => $data) {
                echo "Current Line:" . $current_line;
                echo "<br>";
                echo "Limit:" . $limit;
                echo "<br>";
                echo "Total Count:" . $csv_rows_count;
                echo "<br>";



                if ($current_line == 0) {
                    $current_line++;
                    continue;
                }
                //Skipping Rows if required
                if ($current_line > $limit) {
                    echo "limit reached at:" . $limit . "<br>";
                    $ishark_settings["offset"] = $limit;
                    $settings_object->update($ishark_settings);
                    wp_schedule_single_event(time(), 'ishark_products_import');
                    return;
                }



                $current_line++;

                $color_attribute_name = $ishark_settings["color_attribute_name"];

                $product_sku = (array_key_exists(0, $data)) ? $data[0] : '';
                $product_name = (array_key_exists(1, $data)) ? $data[1] : '';
                $product_description = (array_key_exists(2, $data)) ? $data[2] : '';
                $product_color = (array_key_exists(3, $data)) ? $data[3] : '';
                $product_price = (array_key_exists(4, $data)) ? $data[4] : '';
                $product_stock = (array_key_exists(4, $data)) ? $data[5] : '';
                $product_term_to_import = $product_color;

                $product_term_slug = "";
                $product_attribute_slug = "";

                $existing_product_id = wc_get_product_id_by_sku($product_sku);

                if ($existing_product_id) {
                    $product_variable = wc_get_product($existing_product_id);
                } else {
                    $product_variable = new \WC_Product_Variable();
                }

                //Creating Variable Product from Data

                $product_variable->set_name($product_name);
                $product_variable->set_description($product_description);
                $product_variable->set_sku($product_sku);
                $product_variable->save();

                //Adding Attributes
                $attributes_and_terms = ImportHelper::add_product_attribute(
                    $product_variable->get_id(),
                    [
                        '_attributes' => [
                            $color_attribute_name => [$product_term_to_import],
                        ],
                    ]
                );

                $variation_attributes_of_existing_product = $product_variable->get_variation_attributes();

                $is_attribute_found = false;

                foreach ($attributes_and_terms as $attribute => $term) {
                    $product_attribute_slug = "pa_" . $attribute;
                    $product_term_slug = $term;
                }
                foreach ($product_variable->get_available_variations() as $key => $attributes_value) {
                    foreach ($attributes_value["attributes"] as $attribute_key => $attribute_value) {

                        if ($attribute_key == 'attribute_pa_color') {
                            if ($attribute_value == $product_term_slug) {
                                $is_attribute_found = true;
                            }
                        }
                    }
                }

                $existing_variation_id=null;

                if ($is_attribute_found) {

                    $existing_variation_id=ImportHelper::find_matching_product_variation_id($existing_product_id, ["attribute_".$product_attribute_slug => $product_term_slug]);
                }

                //Creating Variations
                if($existing_variation_id)
                {
                    $product_variation =  wc_get_product($existing_variation_id);
                }
                else
                {
                    $product_variation = new \WC_Product_Variation();

                    $product_variation->set_parent_id($product_variable->get_id());
                    $product_variation->set_attributes(array(
                        $product_attribute_slug => $product_term_slug
                    ));
                }
                
                
                $product_variation->set_regular_price($product_price);
                $product_variation->set_manage_stock(true);
                $product_variation->set_stock_quantity($product_stock);
                $product_variation->save();
            }

            fclose($open);
        }
    }

    public static function find_matching_product_variation_id($product_id, $attributes)
    {
        return (new \WC_Product_Data_Store_CPT())->find_matching_product_variation(
            new \WC_Product($product_id),
            $attributes
        );
    }

    public static function get_attribute_id_from_name($name)
    {
        global $wpdb;
        $attribute_id = $wpdb->get_var("SELECT attribute_id
    FROM {$wpdb->prefix}woocommerce_attribute_taxonomies
    WHERE attribute_name LIKE '$name'");
        return $attribute_id;
    }


    public static function save_product_attribute_from_name($name, $label = '', $set = true)
    {
        global $wpdb;

        $label = $label == '' ? ucfirst($name) : $label;
        $attribute_id = ImportHelper::get_attribute_id_from_name($name);
        $taxonomy = wc_attribute_taxonomy_name($name); // The taxonomy slug

        if (empty($attribute_id)) {
            $attribute_id = NULL;
        } else {
            $set = false;
        }

        //register existing taxonomy
        if (isset($attribute_id) && !taxonomy_exists($taxonomy)) {
            ImportHelper::register_attribute($name);
        }

        $args = array(
            'attribute_id'      => $attribute_id,
            'attribute_name'    => $name,
            'attribute_label'   => $label,
            'attribute_type'    => 'select',
            'attribute_orderby' => 'menu_order',
            'attribute_public'  => 0,
        );


        if (empty($attribute_id)) {
            $wpdb->insert("{$wpdb->prefix}woocommerce_attribute_taxonomies", $args);
            set_transient('wc_attribute_taxonomies', false);
        }

        if ($set) {

            $attributes = wc_get_attribute_taxonomies();
            $args['attribute_id'] = ImportHelper::get_attribute_id_from_name($name);
            $attributes[] = (object)$args;
            //print_r($attributes);
            set_transient('wc_attribute_taxonomies', $attributes);
        } else {
            return;
        }
    }

    public static function register_attribute($name)
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

    public static function add_product_attribute($product_id, $data)
    {

        $_pf = new \WC_Product_Factory();
        $product = $_pf->get_product($product_id);

        $product_attributes = array();
        $terms_to_return = array();


        foreach ($data['_attributes'] as $key => $terms) {
            $taxonomy = wc_attribute_taxonomy_name($key); // The taxonomy slug
            $attr_label = ucfirst($key); // attribute label name
            $attr_name = (wc_sanitize_taxonomy_name($key)); // attribute slug


            // NEW Attributes: Register and save them
            if (!taxonomy_exists($taxonomy))
                ImportHelper::save_product_attribute_from_name($attr_name, $attr_label);


            $product_attributes[$taxonomy] = array(
                'name'         => $taxonomy,
                'value'        => '',
                'position'     => '',
                'is_visible'   => 1,
                'is_variation' => 1,
                'is_taxonomy'  => 1
            );

            foreach ($terms as $value) {
                $term_name = ucfirst($value);
                $term_slug = sanitize_title($value);

                $terms_to_return[$attr_name] = $term_slug;

                // Check if the Term name exist and if not we create it.
                if (!term_exists($value, $taxonomy))
                    wp_insert_term($term_name, $taxonomy, array('slug' => $term_slug)); // Create the term
                // Set attribute values
                wp_set_object_terms($product_id, $term_name, $taxonomy, true);
            }

            //$product->set_attributes(array($attribute));
        }
        update_post_meta($product_id, '_product_attributes', $product_attributes);

        return $terms_to_return;
    }
    
}
