<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryController extends Controller
{
    /**
     * Create a new CategoryController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index','show']]);
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
        $name = $request->name;
        $info = $request->info;
        $url = $request->url;
        if (!$name && !$info && !$url){
            return response()->json(['err' => ['one of the fields is not entered']],400);
         }
        $category = Category::where('id',$request->id)->first();
        if ($category) {
            if ($name) {
                $category->name = $name;
            } 
            if ($request->hasFile('img')) {
                if ($request->file('img')->isValid() && $request->file('img')->getSize()<1024*500) {
                    $path = $request->img->store('images');
                    $category->url = Storage::url($path);
                    $category->save();
                }
            }
            if ($info){
                $category->info = $info;
            } 
            $category->save();
        } else {
            if ($request->name  && $request->info) {
                if ($request->hasFile('img')) {
                    if ($request->file('img')->isValid() && $request->file('img')->getSize()<1024*500) {
                        $category = Category::create([
                            'name' => $request->name,
                            'url' => Storage::url($request->img->store('images')),
                            'info' => $request->info
                          ]);
                    } 
                }
            } 
            if (!$category) {
                return response()->json(['err' => ['not found files']],400);
            }
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
        $books = Book::where('category_id', $category->id)->get();
        return ['books' => $books, 'info' => $category->info];
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
