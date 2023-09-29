<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Products;
use App\Models\User;
use Illuminate\Http\Request;
use Psy\Readline\Hoa\Console;

class HomeController extends Controller
{

    public function index()
    {
        // $products = Products::get();
        // return response()->json($products, 200);
        $users = User::get();
        // dd($users);
        return response()->json($users, 200);
    }


    public function create()
    {
        //
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
