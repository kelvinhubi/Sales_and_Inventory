<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BrandController extends Controller
{
    public function showView()
    {
        if (!Auth::check()) {
            return redirect()->route('homepage');
        }

        return view('manager.brand');
    }
}