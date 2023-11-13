<?php

namespace App\Factory\Views;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentView
{
    private static $types = ['order', 'purchase'];

    private static $directory = 'payments';

    /**
     * redirectToView
     *
     * @param  string $type
     * @param  string|null $id
     * @return string
     */
    public static function redirectToView(string $type, string $id = null): string
    {
        return in_array($type, self::$types) ? self::getView($type, $id) : throw new NotFoundHttpException();
    }

    /**
     * getView
     *
     * @param  string $type
     * @param  string|null $id
     * @return string
     */
    public static function getView(string $type, string $id = null): string
    {
        $viewName = !$id ? "payments.{$type}_payments" : "{$type}s.edit_payment";
        return $viewName;
    }
}
