<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\User\Category;

class CategoryController extends Controller
{
     public function index()
    {
        $categories = Category::where('parent_id', 1)->get();
        
        
        return view('user.layout.header', compact('categories'));
    }
}
