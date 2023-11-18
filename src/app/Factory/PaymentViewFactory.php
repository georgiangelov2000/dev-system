<?php

namespace App\Factory;

use App\Factory\Views\PaymentView;
use App\Factory\Payments\PaymentRelationFactory;
use App\Helpers\FunctionsHelper;
use App\Models\Customer;
use App\Models\OrderPayment;
use App\Models\PurchasePayment;
use App\Models\Supplier;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaymentViewFactory
{
    /**
     * Valid payment types.
     *
     * @var array
     */
    private static $types = ['order', 'purchase'];

    /**
     * Mapping of edit views.
     *
     * @var array
     */
    private static $editMapping = [
        'order' => OrderPayment::class,
        'purchase' => PurchasePayment::class,
    ];

    /**
     * Mapping of index views.
     *
     * @var array
     */
    private static $indexMapping = [
        'order' => [
            'customers' => Customer::class,
        ],
        'purchase' => [ 
            'suppliers' => Supplier::class,
        ],
    ];

    /**
     * Private constructor to prevent instantiation.
     */
    private function __construct()
    {
        // Private constructor to prevent instantiation
    }

/**
 * Selects the appropriate view based on the payment type.
 *
 * @param string $type    The payment type.
 * @param string|null $payment The payment identifier.
 *
 * @throws NotFoundHttpException If the type is not valid or the model class is invalid.
 *
 * @return View
 */
    public static function select(string $type, ?int $id = 0): View
    {
        $data = [];

        if (in_array($type, self::$types)) {
            if ($id) {

                $className = self::$editMapping[$type];

                if (class_exists($className)) {
                    $instance = new $className;
                }

                $instance = $instance->find($id);
                
                $payment = PaymentRelationFactory::selectBuilder($instance);

                $data['payment'] = $payment;
                $data['settings'] = FunctionsHelper::settings();
            } 
            else {
                // Get the model class from the array
                $modelClassArray = self::$indexMapping[$type];

                // Check if $modelClassArray is an array and has a valid model class
                if (is_array($modelClassArray) && count($modelClassArray) === 1) {
                    $modelAlias = key($modelClassArray);
                    $modelClass = reset($modelClassArray);
                    
                    // Check if the modelClass is a string (class name) and if it is a subclass of Eloquent\Model
                    if (is_string($modelClass) && is_subclass_of($modelClass, 'Illuminate\Database\Eloquent\Model')) {
                        // Use resolve() to create an instance of the model
                        $modelInstance = resolve($modelClass);

                        // Fetch data from the model
                        $data[$modelAlias] = $modelInstance->select('id', 'name')->get();
                        
                    } else {
                        // Handle the case where $modelClass is not a valid model class
                        throw new NotFoundHttpException("Invalid model class for type '$type'");
                    }
                } else {
                    // Handle the case where $modelClassArray is not a valid array
                    throw new NotFoundHttpException("Invalid model class array for type '$type'");
                }
            }
        } else {
            throw new NotFoundHttpException();
        }
        return view(PaymentView::getView($type, $id), $data);
    }
    
    /**
    * Retrieves and returns an instance of a payment model by ID and type.
    *
    * @param int $payment - Payment ID.
    * @param string $type - Type of payment ('order' or 'purchase').
    * @return mixed - Returns an instance of the payment model.
    */
   public static function getInstanceModel($payment, $type)
   {
        $className =  self::$editMapping[$type]::find($payment);
        return $className;
   }
}
