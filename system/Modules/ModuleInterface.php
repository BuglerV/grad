<?php

namespace App\Modules;

interface ModuleInterface
{
    public const isOpen = false;
    public function boot();
}