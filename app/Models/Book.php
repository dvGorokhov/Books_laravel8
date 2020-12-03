<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Category;

class Book extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'author',
        'year',
        'info',
        'img',
        'category_id',
        'user_id'
    ];

    public function getCategoryName(){
        if ($category = Category::find($this->category_id)) {
            return $category->name;
        }
        return '';
    }
}
