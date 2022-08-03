<?php

namespace MerryCode\ColorBasedProductImport\Core;

abstract class AbstractView
{
    public function __construct($variables)
    {
        return $this->render_view($variables);
    }
    abstract protected function render_view($variables);
}