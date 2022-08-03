<?php

namespace MerryCode\ColorBasedProductImport\Helpers;

//Core helper functions for the file importing functionality

class MainHelper
{
    public static function get_plugin_folder_name()
    {
        // Get the relative path to current file from plugin root
        $file_path_from_plugin_root = str_replace(WP_PLUGIN_DIR . '/', '', __DIR__);

        // Explode the path into an array
        $path_array = explode('/', $file_path_from_plugin_root);

        // Plugin folder is the first element
        $plugin_folder_name = reset($path_array);

        return $plugin_folder_name;

    }
}
