<?php

namespace App\Factory;

abstract class ViewType
{
    protected $directory;
    protected $types = [];
    protected $dataMapping = [];
        
    /**
     * redirectToView
     *
     * @param  mixed $type
     * @param  mixed $id
     * @return void
     */
    abstract protected function redirectToView(string $type, string $id=null);
    
    /**
     * getView
     *
     * @param  mixed $type
     * @param  mixed $id
     * @return void
     */
    abstract protected function getView(string $type, string $id = null);
    
}
