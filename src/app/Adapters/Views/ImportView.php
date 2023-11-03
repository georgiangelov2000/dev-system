<?php

namespace App\Adapters\Views;

use App\Adapters\ViewType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ImportView extends ViewType
{
    /**
     * __construct
     *
     * @return void
     */
    public function __construct()
    {
        $this->types = [
            'supplier',
            'customer',
            'purchase',
            'package',
            'category',
            'brand'
        ];
        $this->directory = 'imports';
    }

    /**
     * redirectToView
     *
     * @param  mixed $type
     * @param  mixed $id
     * @return void
     */
    public function redirectToView(string $type, string $id = null)
    {
        if (in_array($type, $this->types)) {
            return $this->getView($type, $id);
        }
        throw new NotFoundHttpException();
    }

    /**
     * getView
     *
     * @param  mixed $type
     * @param  mixed $id
     * @return void
     */
    public function getView(string $type, string $id = null)
    {
        return view($this->directory . '.' . $type);
    }
}
