<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Session;
class login extends Controller
{
    public function showForm():View{
        return View('index');
    }

    public function loginUser(Request $request):RedirectResponse|View{
        $info = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $credentials = $request->only('email','password');
        if(Auth::attempt($credentials,$request->boolean('remember'))){
            if(Auth::user()->Role == 'Owner'){
                return redirect()->intended(route('owner'));
            }
            elseif(Auth::user()->Role == 'Customer'){
                return redirect()->intended(route('customer'));
            }
            elseif(Auth::user()->Role == 'Manager'){
                return redirect()->intended(route('manager'));
            }
            
        }
         return back()->withInput()->with('status', 'Invalid Credentials or User not Found');
    }
    public function logout():RedirectResponse{
        // Mark user as offline before logging out
        if (Auth::check()) {
            Auth::user()->update([
                'is_online' => false,
                'last_activity' => null
            ]);
        }
        
        Session::flush();
        Auth::logout();
        return redirect(route('Login'));
    }
}
