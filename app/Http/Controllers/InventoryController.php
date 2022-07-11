<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Inventory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $inventories = Inventory::all();

        return response()->json([
            'status' => true,
            'data' => $inventories
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required | max:255',
            'quantity' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),405);
        }

        $inventory = Inventory::create([
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => $request->price
        ]);

        if($inventory) {
            return response()->json([
                'status' => true,
                'message' => 'Inventory created successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error creating Inventory'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required | max:255',
            'quantity' => 'required',
            'price' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),405);
        }

        $inventory = Inventory::findorFail($id);
        $inventory->update([
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => $request->price
        ]);

        if($inventory) {
            return response()->json([
                'status' => true,
                'message' => 'Inventory updated successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error updating Inventory'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $inventory = Inventory::findOrFail($id);
        $deleted_inventory = $inventory->delete();

        if($deleted_inventory ) {
            return response()->json([
                'status' => true,
                'message' => 'Inventory deleted successfully'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error deleting Inventory'
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Users Inventory Routes
    |--------------------------------------------------------------------------
    */

    //  fetch all inbentories for users
    public function view_inventories()
    {
        if(!auth()->check()) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $inventories = Inventory::all();

        return response()->json([
            'status' => true,
            'data' => $inventories
        ], 200);
    }

    //  show single inventory details
    public function single_inventory($id)
    {
        if(!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $inventory = Inventory::findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $inventory
        ], 200);
    }

    //  add to cart
    public function add_to_cart(Request $request)
    {
        if(!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $cart = Cart::create([
            'quantity' => $request->quantity,
            'total_amount' => $request->amount,
            'user_id' => Auth::user()->id,
            'inventory_id' => $request->id
        ]);

        if($cart) {
            return response()->json([
                'status' => true,
                'message' => 'Item added to cart successfully'
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error adding item to cart'
        ], 200);
    }

    //  View cart
    public function view_cart(Request $request)
    {
        if(!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $cart = Cart::where('user_id', Auth::user()->id)->get();

        if($cart) {
            return response()->json([
                'status' => true,
                'data' => $cart
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Error fetching cart'
        ], 200);
    }

    //  checkout from cart
    public function checkout(Request $request)
    {
        if(!auth()->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $cart_items = $request->cartItems;

        DB::transaction(function() use ($cart_items) {
            foreach ($cart_items as $key => $item) {
                $inventory = Inventory::find($item['inventory_id']);
                $remaining_quantity = $inventory->quantity != 0 ?  $inventory->quantity - $item['quantity'] : 0;

                $inventory->update([
                    'quantity' => $remaining_quantity
                ]);

            }
        });

        return response()->json([
            'status' => true,
            'message' => 'Cart checkout successful'
        ], 200);
    }
}
