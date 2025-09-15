<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class signup extends Controller
{
    public function showForm():View{
        return View('signup');
    }
    public function createUser(Request $request):RedirectResponse|View{
        $info = $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'Role' => 'min:3|max:10',
        ]);
        $info['password'] = bcrypt($info['password']);
        $info['Role'] = 'Customer';
        User::create($info);
        return redirect()->route('Login')->with('status','Registration Complete, You can now log in');
    }
}
