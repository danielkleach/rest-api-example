<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, int $id)
    {
        $product = Product::findOrFail($id);
        $product->addMedia($request->file('image'))->toMediaCollection('images');

        return response()->json([
            'message' => 'The image has been attached to the product.'
        ], 200);
    }
}
