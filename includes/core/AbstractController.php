<?php

namespace MerryCode\ColorBasedProductImport\Core;

// all controllers must extend this class
abstract class AbstractController
{
    public function __construct()
    {
        //all actions and hooks should be initialized here
        $this->registerCallBackHooks();
    }

    /**
     * Registers callback hooks
     *
     * @return void
     */
    abstract protected function registerCallBackHooks() : void;
}
