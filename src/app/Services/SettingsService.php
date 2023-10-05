<?php

namespace App\Services;

class SettingsService
{
    protected $property;

    public function __construct($property)
    {
        $this->property = $property;
    }
}
