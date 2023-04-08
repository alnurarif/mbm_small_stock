<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\Issue;
use App\Models\Receive;
use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Requisition;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class IssuesController extends Controller
{
    public function __construct()
    {
        
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        return Issue::with('details','details.item')->where('employee_id',Auth::user()->id)->get();
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
            'requisition_id' => 'required|integer',
            'item_id.*' => 'required|integer',
            'quantity.*' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $error_arr = [];
        foreach($request->item_id as $key => $single_item_id){
            
            $quantity_consumed_total = Receive::selectRaw('SUM(quantity) AS quantity_total')
            ->selectRaw('SUM(consumed) AS consumed_total')
            ->where('quantity', '>', 'consumed')
            ->where('quantity', '<>', 'consumed')
            ->where('item_id',$single_item_id)
            ->groupBy('item_id')
            ->first();
            $its_null = false;
            if(isset($quantity_consumed_total) && isset($quantity_consumed_total->quantity_total)){
                $quantity_total = $quantity_consumed_total->quantity_total;
                $consumed_total = $quantity_consumed_total->consumed_total;
            }else{
                $its_null = true;
            }
            if($its_null){
                $item = Item::find($single_item_id);
                array_push($error_arr, $item->name.' is short in stock. It is 0');
            }else{
                if(($quantity_total-$consumed_total) < $request->quantity[$key]){
                    $item = Item::find($single_item_id);
                    array_push($error_arr, $item->name.' is short in stock. It is '.($quantity_total-$consumed_total));
                }
            }

        }
        if(count($error_arr) > 0){
            return response()->json(['errors' => $error_arr], 404);
        }

        // create the issue
        $issue = Issue::create([
            'requisition_id' => $request->requisition_id,
            'employee_id' => Auth::user()->id,
        ]);

        // create the issue details
        foreach($request->item_id as $key => $single_item_id){
            $receives = Receive::where('quantity', '>', 'consumed')
            ->where('quantity', '<>', 'consumed')
            ->where('item_id',$single_item_id)
            ->orderBy('id')
            ->get();

            $price = 0;
            $necessary = $request->quantity[$key];
            foreach($receives as $receive){
                if(($receive->quantity - $receive->consumed) >= $necessary){
                    $rec = Receive::find($receive->id);
                    $rec->consumed = $rec->consumed + $necessary;
                    $rec->save();
                    $price = $price + ($rec->price * $necessary);
                    break;
                }
                if(($receive->quantity - $receive->consumed) < $necessary){
                    $rec = Receive::find($receive->id);
                    $rec->consumed = $rec->quantity;
                    $rec->save();
                    $price = $price + ($rec->price * $rec->quantity);
                    
                    $necessary = $necessary - ($receive->quantity - $receive->consumed);
                }

            }

            $issue->details()->create([
                // 'issue_id' => $issue->id,
                'requisition_id' => $request->requisition_id,
                'item_id' => $single_item_id,
                'quantity' => $request->quantity[$key],
                'price' => $price,
            ]);

        }
        $requisition = Requisition::find($request->requisition_id);
        $requisition->status = 'issued';
        $requisition->save();

        return response()->json([
            'message' => 'Issue created successfully.',
            'data' => $issue,
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $issue = Issue::where('id',$id)->with('details','details.item')->first();
        return response()->json($issue);
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
            return Issue::with('details','details.item')
            ->where('status','new')
            ->get();
        }else{
            return Issue::with('details','details.item')
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
        return Issue::with('details','details.item')
        ->where('employee_id',Auth::user()->id)
        ->where('status','approved')
        ->get();
    }

    /**
     * Get rejected list 
     */
    public function getRejectedList()
    {
        return Issue::with('details','details.item')
        ->where('employee_id',Auth::user()->id)
        ->where('status','rejected')
        ->get();
    }

    public function updateStatus(Request $request, $id)
    {
        $issue = Issue::find($id);
        if(!$issue){
            return response()->json([
                'success' => false,
                'message' => 'Issue not found.'
            ], Response::HTTP_NOT_FOUND);
        }
        $request->validate([
            'status' => 'required|in:new,approved,rejected',
        ]);
        
        $issue->status = $request->status;
        $issue->save();


        return response()->json([
            'message' => 'Issue status updated successfully.',
            'data' => $issue,
        ]);
    }

}
