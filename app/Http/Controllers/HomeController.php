<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Psy\Readline\Hoa\Console;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        return response()->json($user, 200);
    }


    public function getSeller()
    {
        $data = DB::table('orders')
            ->select(
                'orders.id as order_id',
                'orders.user_id',
                'orders.address_id',
                'orders.status',
                'orders.type_shipping',
                'orders.order_date',
                'order_items.id as order_item_id',
                'order_items.product_id',
                'order_items.amount',
                'order_items.price as order_item_price',
                'payments.id as payment_id',
                'payments.user_id as payment_user_id',
                'payments.price as payment_price',
                'payments.status as payment_status',
                'payments.slip_img',
                'addresses.id as address_id',
                'addresses.name as address_name',
                'addresses.street',
                'addresses.district',
                'addresses.subdistrict',
                'addresses.province',
                'addresses.zip_code',
                'addresses.phone',
                'products.id as product_id',
                'products.name as product_name',
                'products.price as product_price',
                'products.stock',
                'products.thumbnail',
                'products.detailProduct',
                'products.detailShipping',
                'products.condition',
                'products.typeProduct',
                'products.typeShipping'
            )
            ->leftJoin('order_items', 'orders.id', '=', 'order_items.order_id')
            ->leftJoin('payments', 'orders.id', '=', 'payments.order_id')
            ->leftJoin('addresses', 'orders.address_id', '=', 'addresses.id')
            ->leftJoin('products', 'order_items.product_id', '=', 'products.id')
            ->get();

        return response()->json($data, 200);


        // dd($data);
    }


    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
