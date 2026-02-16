<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    protected $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index()
    {
        $products = $this->productService->getAllProducts();
        return view('product.index', compact('products'));
    }


    public function store(ProductRequest $request)
    {
        $data = $request->all();
        $data['featured'] = $request->has('featured') ? 1 : 0;

        if ($request->hasFile('image'))
        {
            $filename = $request->file('image')->getClientOriginalName();

            $request->file('image')->storeAs('products',$filename,'public');
            $data['image'] = $filename;
        }

        $this->productService->createProduct($data);

        return redirect()->back()->with('success','Product Added Successfully!');
    }

    public function product()
    {
        $products =$this->productService->getAllProducts();
        return view('product.index',compact('products'));
    }

    public function create()
    {
        return view('product.create');
    }

}
