<?php

namespace App\Http\Controllers\Api;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Dotenv\Exception\ValidationException;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SuppliersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Supplier::get();
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
            'name' => 'required|min:5|max:100',
            'phone' => 'required|min:6|max:50',
            'email' => 'required|email',
            'address' => 'required|max:100|min:6',
            'status' => 'required|in:active,inactive'
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $supplier = Supplier::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Supplier created successfully.',
            'data' => $supplier,
        ], Response::HTTP_CREATED);

        
        
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {   
        $supplier = Supplier::find($id);
        if(!$supplier){
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json($supplier);
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
        $supplier = Supplier::find($id);
        if(!$supplier){
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:5|max:100',
            'phone' => 'required|min:6|max:50',
            'email' => 'required|email',
            'address' => 'required|max:100|min:6',
            'status' => 'required|in:active,inactive'
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $supplier->name = $request->name;
        $supplier->phone = $request->phone;
        $supplier->email = $request->email;
        $supplier->address = $request->address;
        $supplier->status = $request->status;
        $supplier->save();
        

        return response()->json([
            'message' => 'Supplier updated successfully.',
            'data' => $supplier,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        $supplier = Supplier::find($id);

        if(!$supplier){
            return response()->json([
                'success' => false,
                'message' => 'Supplier not found.'
            ], Response::HTTP_NOT_FOUND);
        }

        $supplier->delete();

        return response()->json(['message' => 'Supplier deleted successfully.'], Response::HTTP_ACCEPTED);
    }
}
