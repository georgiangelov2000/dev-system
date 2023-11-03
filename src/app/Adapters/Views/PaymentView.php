<?php

namespace App\Adapters\Views;

use App\Adapters\ViewType;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentView extends ViewType
{

    public function __construct()
    {
        $this->types = [
            'order',
            'purchase'
        ];

        $this->directory = 'payments';
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
        return in_array($type, $this->types) ? $this->getView($type, $id) : throw new NotFoundHttpException();
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
        $viewName = !$id ? "payments.{$type}_payments" : "{$type}s.edit_payment";
        return $viewName;
    }
    
}
