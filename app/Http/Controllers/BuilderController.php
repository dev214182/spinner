<?php

namespace App\Http\Controllers;

use App\User;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuilderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        return view('builder.index');
    }

    public function edit($id)
    {
        $userCompanyId = Auth::user()->company_id;
        $product = Product::where('id', '=', $id)->firstOrFail();
        
        // Restrict Users to access other company's product pages
        if($product && $product->company_id == $userCompanyId ){
            return view('builder.index');
        }
        return abort(404);
    }
}
