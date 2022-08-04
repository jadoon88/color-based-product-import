<?php
namespace MerryCode\ColorBasedProductImport\Models;

// defines model for cbpi_import_settings wp_option
use MerryCode\ColorBasedProductImport\Configs\Main;
use MerryCode\ColorBasedProductImport\Core\AbstractModel;

class ImportSettings extends AbstractModel
{
    public string $WpOptionName;
    public string $autoLoad;

    public function __construct()
    {
        $this->WpOptionName ="cbpi_import_settings";
        $this->autoLoad     ="no";

        $subOptions = [
           "upload_step" => 1,
           "upload_file" => "none",
           "rows_per_interval" => 2,
           "color_attribute_name" => Main::ATTRIBUTE_SLUG_COLOR_NAME,
           "offset" => 0,
           "upload_file" => "",
           "status" => Main::IMPORT_STATUS_IDLE,
        ];
 
        add_option($this->WpOptionName, $subOptions, '', $this->autoLoad);
    }

    /**
     * Gets import settings
     *
     * @return array
     */
    public function get() : array
    {
        return get_option($this->WpOptionName);
    }
    /**
     * Updates import settings
     *
     * @param $array of settings
     *
     * @return bool
     */
    public function update($array) : bool
    {
        return update_option($this->WpOptionName, $array);
    }
    /**
     * Deletes import settings
     *
     * @return bool
     */
    public function delete() : bool
    {
        return delete_option($this->WpOptionName);
    }
}
