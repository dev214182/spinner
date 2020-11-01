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
        $usersAuth = User::with('permissions')->find(Auth::id());
       
        return view('builder.index' , compact("usersAuth"));
       
    }

    public function edit($id)
    { 
      
        $userCompanyId = Auth::user()->company_id;
        $product = Product::where('id', '=', $id)->firstOrFail();
        
        // Restrict Users to access other company's product pages
        if($product && $product->company_id == $userCompanyId ){
             $usersAuth = User::with('permissions')->find(Auth::id());
             return view('builder.index' , compact("usersAuth"));
        }
        return abort(404);
    }
}
