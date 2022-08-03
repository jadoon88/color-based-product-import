<?php
namespace MerryCode\ColorBasedProductImport\Core;

//All Models should extend this class

abstract class AbstractModel
{
    public $wp_option_name;
    public $autoload;
 
    abstract function get();
    abstract function update($array);
    abstract function delete();
}