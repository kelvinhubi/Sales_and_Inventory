<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function showView()
    {
        if (!Auth::check()) {
            return redirect()->route('homepage');
        }

        return view('manager.products');
    }
}