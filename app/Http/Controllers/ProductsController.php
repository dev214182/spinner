<?php

namespace App\Http\Controllers;

use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['show']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function productsAPI()
    {
        $company_id = Auth::user()->company_id;
        $products = Product::where('company_id', $company_id)->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($products, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $company_id = Auth::user()->company_id;
        // validate request
        $this->validateRequest();
        // store request
        $product = Product::create([
            'title' => $request->title,
            'slug' => $request->slug,
            'user_id' => auth()->id(),
            'company_id' => $company_id,
        ]);
        // response
        return response()->json([
            'product' => $product->id,
            'message' => 'Product has been created',
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($slug)
    {
        return view('product.single-product');
    }

    public function uploadVideo()
    {

    }


    /**
     * Form Validation
     */
    public function validateRequest()
    {
        return request()->validate([
            'title' => ['required', 'min:1', 'max:50', 'string'],
            'slug' => ['min:1', 'max:50', 'string', 'alpha_dash', 'unique:products'],
            'company_id' => [''],
        ]);

    }
}
