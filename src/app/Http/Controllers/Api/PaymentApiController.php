<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Factory\Payments\API\PaymentRepositoryFactory;

class PaymentApiController extends Controller
{
    public function getData(Request $request)
    {
        // Additional checks or conditions
        if (!$request->has('type')) {
            return response()->json(['error' => 'Type is required'], 400);
        }

        $type = $request->input('type');

        // You can add more conditions here if needed

        // Create repository based on type
        try {
            $repository = PaymentRepositoryFactory::create($type);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // Now use $repository to call the getData method
        $data = $repository->getData($request);

        return $data;
        // ... rest of your code
    }
}
