<?php
namespace MerryCode\ColorBasedProductImport\Models;

//Defines model for ishark_import_settings wp_option
use MerryCode\ColorBasedProductImport\Core\AbstractModel;

class ImportSettings extends AbstractModel
{
   public $wp_option_name;
   public $autoload;

   public function __construct()
   {
	   //ToDo add this to config file
       $this->wp_option_name="ishark_import_settings";
       $this->autoload="no";

       $sub_options = [
           "upload_step" => 1,
           "upload_file" => "none",
           "rows_per_interval" => 2,
           "color_attribute_name" => "Color",
           "offset" => 0,
           "upload_file" => "",
           "status" => "idle",
 
        ];
 
        add_option( $this->wp_option_name, $sub_options, '', $this->autoload);

   }

   public function get()
   {
      return get_option($this->wp_option_name);
   }
   public function update($array)
   {
      return update_option($this->wp_option_name, $array);
   }
   public function delete()
   {
      return delete_option($this->wp_option_name);
   }

}