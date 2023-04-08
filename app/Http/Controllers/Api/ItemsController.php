<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class ItemsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        
        // Check if user is an admin
        // if (Auth::user()->isAdmin()) {
            // Return admin data
        // }

        // Check if user is a store executive
        // if (Auth::user()->isStoreExecutive()) {
            // Return store executive data
        // }
        return Item::get();
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
            'name' => 'required|unique:items,name',
            'description' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item = Item::create([
            'name' => $request->name,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Item created successfully.',
            'data' => $item,
        ], Response::HTTP_CREATED);

        
        
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {   
        $item = Item::find($id);
        if(!$item){
            return response()->json([
                'success' => false,
                'message' => 'Item not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json($item);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $item = Item::find($id);
        if(!$item){
            return response()->json([
                'success' => false,
                'message' => 'Item not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:items,name,' . $item->id,
            'description' => 'required',
            'status' => 'required|in:active,inactive',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $item->name = $request->name;
        $item->description = $request->description;
        $item->status = $request->status;
        $item->save();
        

        return response()->json([
            'message' => 'Item updated successfully.',
            'data' => $item,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $item = Item::find($id);

        if(!$item){
            return response()->json([
                'success' => false,
                'message' => 'Item not found.'
            ], Response::HTTP_NOT_FOUND);
        }

        $item->delete();

        return response()->json(['message' => 'Item deleted successfully.'], Response::HTTP_ACCEPTED);
    }
}
