<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Create a new CategoryController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::all();
        return $category;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if ( $request->name && $request->url && $request->info) {
            $category = Category::create([
                'name' => $request->name,
                'url' => $request->url,
                'info' => $request->info
              ]);
            } else {
                return response()->json(['err' => ['not found files']],400);
            }
        return $category;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        return $category;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Category $category)
    {
        if (!$request->name && !$request->url && !$request->info){
            return response()->json(['err' => ['one of the fields is not entered']],400);
         }
        if ($request->name) {
            $category->name = $request->name;
        } 
        if ($request->info) {
            $category->info = $request->info;
        } 
        if ($request->url){
            $category->url = $request->url;
        } 
        $category->save();
       return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();
        return true;
    }
}
