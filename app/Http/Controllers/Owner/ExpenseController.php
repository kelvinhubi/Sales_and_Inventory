<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function showView()
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }

        return view('owner.expenses');
    }
}
