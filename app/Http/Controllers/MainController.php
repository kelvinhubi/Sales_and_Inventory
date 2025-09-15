<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Session;
class MainController extends Controller
{
    public function owner():View|RedirectResponse{
        if(!$this->AuthenticateUser()){
            return redirect()->route('Login')->with('status','Login to access Owner');
        }
        return view('owner.dashboard');
    }
    public function customer():View|RedirectResponse{
        if(!$this->AuthenticateUser()){
            return redirect()->route('Login')->with('status','Login to access Customer');
        }
        return view('customer.dashboard');
    }
    public function manager():View|RedirectResponse{
        if(!$this->AuthenticateUser()){
            return redirect()->route('Login')->with('status','Login to access Manager');
        }
        return view('manager.dashboard');
    }

    public function AuthenticateUser():bool{
        $result = Auth::check();
        return $result;
    }
    public function ownerChangePassword():View|RedirectResponse{
        $userlog = Auth::user();
        return view('owner.changepassword')->with('userlog',$userlog);
    }

    public function managerChangePassword():View|RedirectResponse{
        $userlog = Auth::user();
        return view('manager.changepassword')->with('userlog',$userlog);
    }

    public function ownerUpdatePassword(Request $request):View|RedirectResponse{
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);
        $userlog = Auth::user();
        $userlog->password = bcrypt($request->password);
        $userlog->save();
        Session::flush();
        Auth::logout();
        return redirect()->route('Login')->with('status','Password Changed Successfully Login Again');
    }

    public function managerUpdatePassword(Request $request):View|RedirectResponse{
        $request->validate([
            'email'=>'required',
            'password'=>'required'
        ]);
        $userlog = Auth::user();
        $userlog->password = bcrypt($request->password);
        $userlog->save();
        Session::flush();
        Auth::logout();
        return redirect()->route('Login')->with('status','Password Changed Successfully Login Again');
    }
}
