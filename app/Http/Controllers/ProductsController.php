<?php

namespace App\Http\Controllers;

use App\Product;
use App\Hotspot;
use App\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth')->except(['publicproductsAPI','show']);
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

    public function fetch($id)
    { 
       
        $product = Product::where('id', '=', $id)->firstOrFail();
        return response()->json($product, 200);
      
    }

    public function searchProduct($search)
    {
       
        $company_id = Auth::user()->company_id;
        $products = Product::where('company_id', $company_id)->where('title', 'like', '%'.$search.'%')->orderBy('created_at', 'desc')->paginate(10);
        
        return response()->json($products, 200);
    }

    public function productsAPI()
    {
        $user = Auth::user();
        
        //if(Auth::user()->role == 5){
        if($user->hasRole('editor')){
            $userId = Auth::user()->id;
            $products = Product::where('user_id', $userId)->orderBy('created_at', 'desc')->paginate(10);
        }else{
            $company_id = Auth::user()->company_id;
            $products = Product::where('company_id', $company_id)->orderBy('created_at', 'desc')->paginate(10);
        }
        return response()->json($products, 200);
    }

    public function publicproductsAPI($id)
    {
        $hotspot = array();
        $videos = array();
        $products = Product::where(function ($query) use ($id) {
                                $query->where('slug', '=', $id)
                                    ->orWhere('id', '=', $id);
        })->with('user','items','items.media_file','items.hotspot_setting')->get();
       
        if(@count($products) > 0){
            $hotspot = Hotspot::where('product_id', '=', $products[0]->id)->orderBy("hotspot_for")->get(); 

            $videos = Video::where('product_id', '=', $products[0]->id)->get(); 
            return response()->json(["dataItems" => $products, "hpItems" => $hotspot, "videos" => $videos]);
        } 
       
        return response()->json(["dataItems" => false]);;
    }

    public function destroy($id)
    {
       
        $product = Product::where('id', '=', $id)->firstOrFail();
        $product->delete(); 
        
        return response()->json('Item has been deleted', 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      
        if (!$request->user()->can('all-permission') && !$request->user()->can('c-only') && !$request->user()->can('c-u-only') && !$request->user()->can('c-d-only')) { 
            return response()->json([
                        'product' => false,
                        'message' => 'You dont have permission to create',
                    ], 200);
        }

        $company_id = Auth::user()->company_id;
        // validate request 

        $check_product = Product::where('slug', $request->slug)->first(); 
       
        if(@$check_product->slug){ 
            $request['slug'] = $request->slug."-".$check_product->count();
        } 

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
        return view('product.single-product', compact('slug'));
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
