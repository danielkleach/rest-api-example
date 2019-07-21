<?php

namespace App\Http\Controllers;

use Auth;
use App\User;
use App\Product;
use Illuminate\Http\Request;
use App\Http\Resources\Product as ProductResource;

class UserProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = User::find(Auth::user()->id)->products()->paginate(25);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required'
        ]);

        Auth::user()->products()->attach($request->product_id);

        return response()->json([
            'message' => 'The product has been attached to the user.'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Auth::user()->products()->detach($id);

        return response()->json([
            'message' => 'The product has been detached from the user.'
        ], 200);
    }
}
