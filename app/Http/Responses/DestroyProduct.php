<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class DestroyProduct implements Responsable
{
    protected $product;

    public function __construct($product)
    {
        $this->product = $product;
    }

    public function toResponse($request)
    {
        return response()->json(['message' => 'This product has been deleted.'], 200);
    }
}
