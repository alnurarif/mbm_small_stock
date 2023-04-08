<?php

namespace App\Http\Controllers\Api;

use App\Models\Receive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ReceivesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Receive::with('supplier','item')->get();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|integer',
            'item_id' => 'required|integer',
            'quantity' => 'required|integer',
            'price' => 'required|numeric'
        ]);
        
        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $receive = Receive::create([
            'supplier_id' => $request->supplier_id,
            'user_id' => Auth::user()->id,
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'price' => $request->price
        ]);

        return response()->json([
            'message' => 'Receive created successfully.',
            'data' => $receive,
        ], Response::HTTP_CREATED);

        
        
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {   
        $receive = Receive::find($id);
        if(!$receive){
            return response()->json([
                'success' => false,
                'message' => 'Receive not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json($receive);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $receive = Receive::find($id);
        if(!$receive){
            return response()->json([
                'success' => false,
                'message' => 'Receive not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'supplier_id' => 'required|integer',
            'user_id' => 'required|integer',
            'item_id' => 'required|integer',
            'quantity' => 'required|integer',
            'price' => 'required|numeric'
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $receive->supplier_id = $request->supplier_id;
        $receive->user_id = $request->user_id;
        $receive->item_id = $request->item_id;
        $receive->quantity = $request->quantity;
        $receive->price = $request->price;

        $receive->save();

        return response()->json([
            'message' => 'Receive updated successfully.',
            'data' => $receive,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $receive = Receive::find($id);

        if(!$receive){
            return response()->json([
                'success' => false,
                'message' => 'Receive not found.'
            ], Response::HTTP_NOT_FOUND);
        }

        $receive->delete();

        return response()->json(['message' => 'Receive deleted successfully.'], Response::HTTP_ACCEPTED);
    }

    public function getStock(){
        
        return Receive::with('item')->selectRaw('item_id,SUM(quantity) AS quantity_total')
        ->selectRaw('SUM(consumed) AS consumed_total')
        ->selectRaw('SUM(price) AS price_total')
        ->groupBy('item_id')
        ->get();
    }
}
