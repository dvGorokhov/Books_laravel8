<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Create a new BookController instance.
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
        $book = Book::all();
        return $book;
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
        $author = $request->author;
        $info = $request->info;
        $img = $request->img;
        $year = $request->year;
        $category_id = $request->category_id;

        if ( !$name && !$author &&
             !$info &&  !$img && 
             !$category_id && !$year) {
            return response()->json(['err' => ['not found files']],400);
        }

        if ($request->id) { 
            if(!$book = Book::where('id',$request->id)->first()){
                return response()->json(['err' => ['book not found']],400);
            }
            if ($name) {
                $book->name = $name;
            }
            if ($author) {
                $book->author = $author;
            }
            if ($info) {
                $book->info = $info;
            }
            if ($year) {
                $book->year = $year;
            }
            if ($category_id) {
                $book->category_id = $category_id;
            }
            if ($request->hasFile('img')) {
                if ($request->file('img')->isValid()) {
                    $path = $request->img->store('images');
                    $book->url = Storage::url($path);
                    $book->save();
                }
            }
            $book->save();
        } else {
            if ($name && $author && $info && $category_id){
                if ($request->hasFile('img')) {
                  if ($request->file('img')->isValid() && $request->file('img')->getSize()<1024*500) {
                    $book = Book::create([
                        'name' => $request->name,
                        'author' => $request->author,
                        'info' => $request->info,
                        'img' => Storage::url($request->img->store('images')),
                        'category_id' => $request->category_id,
                        'year' => $request->year??null,
                        'user_id'=> Auth::id()
                      ]);
                    }
                }
            } 
        }
        return $book;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function show(Book $book)
    {
        $arr = $book->toArray();
        $arr['category_name'] = $book->getCategoryName();
        return $arr;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function sort()
    {
        $book = Book::orderBy('name', 'asc')->get();
        return $book;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Book $book)
    {
        if (!$request->name && !$request->author &&
         !$request->year && !$request->info 
         && !$request->img && !$request->category_id){
            return response()->json(['err' => ['one of the fields is not entered']],400);
         }
        if ($request->name) {
            $book->name = $request->name;
        } 
        if ($request->author) {
            $book->author = $request->author;
        } 
        if ($request->year){
            $book->year = $request->year;
        } 
        if ($request->info){
            $book->info = $request->info;
        } 
        if ($request->img){
            $book->img = $request->img;
        } 
        if ($request->category_id){
            $book->category_id = $request->category_id;
        } 
        $book->save();
       return true;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\Response
     */
    public function destroy(Book $book)
    {
        $book->delete();
        return true;
    }
}
