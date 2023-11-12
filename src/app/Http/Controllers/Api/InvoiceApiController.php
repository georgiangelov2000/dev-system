<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Factory\InvoiceRepositoryFactory;

class InvoiceApiController extends Controller
{
    public function getData(Request $request)
    {
        // Additional checks or conditions
        if (!$request->has('type')) {
            return response()->json(['error' => 'Type is required'], 400);
        }

        $type = $request->input('type');

        // Create repository based on type
        try {
            $repository = InvoiceRepositoryFactory::create($type);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        // Now use $repository to call the getData method
        $data = $repository->getData($request);

        return $data;
        // ... rest of your code
    }
}
