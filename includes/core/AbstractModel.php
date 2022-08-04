<?php
namespace MerryCode\ColorBasedProductImport\Core;

// all Models should extend this class

abstract class AbstractModel
{
    public string $WpOptionName;
    public string $autoLoad;

    /**
     * Gets an option
     *
     * @return array
     */
    abstract public function get() : array;
    /**
     * Update an option
     *
     * @param $array
     *
     * @return bool
     */
    abstract public function update($array) : bool;
    /**
     * Deletes an option
     *
     * @return bool
     */
    abstract public function delete() : bool;
}
