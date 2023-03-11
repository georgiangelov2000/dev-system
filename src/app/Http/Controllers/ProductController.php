<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\LoadStaticData;

class ProductController extends Controller
{
    public function index () {
        return view('products.index');
    }
    
    public function create () {
        $suppliers = LoadStaticData::callSupliers();

        return view('products.create',['suppliers' => $suppliers]);
    }
}
