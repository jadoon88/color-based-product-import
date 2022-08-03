<?php
namespace MerryCode\ColorBasedProductImport\Core;

//All controllers must extend this class

abstract class AbstractController
{
    public function __construct()
    {
        //all actions and hooks should be initialized here
        $this->register_hook_callbacks();
    }
    abstract protected function register_hook_callbacks();
}