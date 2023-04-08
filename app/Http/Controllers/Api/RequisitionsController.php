<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Requisition;
use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RequisitionsController extends Controller
{
    public function __construct()
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Requisition::with('details','details.item')->where('employee_id',Auth::user()->id)->get();
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
        // validate the incoming request

        $validator = Validator::make($request->all(), [
            'item_id.*' => 'required|integer',
            'quantity.*' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // create the requisition
        $requisition = Requisition::create([
            'employee_id' => Auth::user()->id,
        ]);

        // create the requisition details
        foreach($request->item_id as $key => $single_item_id){
            $requisition->details()->create([
                // 'requisition_id' => $requisition->id,
                'item_id' => $single_item_id,
                'quantity' => $request->quantity[$key],
            ]);
        }

        return response()->json([
            'message' => 'Requisition created successfully.',
            'data' => $requisition,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $requisition = Requisition::where('id',$id)->with('details','details.item')->first();
        return response()->json($requisition);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Get new list 
     */
    public function getNewList()
    {
        if (Auth::user()->isAdmin()) {
            return Requisition::with('details','details.item')
            ->where('status','new')
            ->get();
        }else{
            return Requisition::with('details','details.item')
            ->where('employee_id',Auth::user()->id)
            ->where('status','new')
            ->get();

        }
    }
    
    /**
     * Get approved list 
     */
    public function getApprovedList()
    {
        return Requisition::with('details','details.item')
        ->where('status','approved')
        ->get();
    }

    /**
     * Get rejected list 
     */
    public function getRejectedList()
    {
        return Requisition::with('details','details.item')
        ->where('employee_id',Auth::user()->id)
        ->where('status','rejected')
        ->get();
    }

    public function updateStatus(Request $request, $id)
    {
        $requisition = Requisition::find($id);
        if(!$requisition){
            return response()->json([
                'success' => false,
                'message' => 'Requisition not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        $request->validate([
            'status' => 'required|in:new,approved,rejected',
        ]);
        
        $requisition->status = $request->status;
        $requisition->save();


        return response()->json([
            'message' => 'Requisition status updated successfully.',
            'data' => $requisition,
        ]);
    }

}
