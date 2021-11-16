<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
class CategoryController extends Controller
{
    public function __construct()
    {
        $this->limit = config()->get('app.pagination');
    }

    public function show()
    {
        $data = [];
        $message = "Category Successful";
        $statusCode = 200;
        $data = Category::select('id','name')->get();
        return apiResponse($data, $message, $statusCode);
    }
}
