<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ManagerController extends Controller
{
    public function showView()
    {
        if (! Auth::check()) {
            return redirect()->route('Login');
        }

        return view('owner.manager');
    }

    public function index(Request $request)
    {
        $query = User::where('Role', 'manager');

        if ($request->has('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        $users = $query->latest()->paginate(10);

        return response()->json($users);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => ['required', 'regex:/^(09|\+639)\d{9}$|^(0[2-8]|\+63[2-8])\d{7,8}$/'],
            'notes' => 'required|nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'notes' => $request->notes,
            'Role' => 'Manager',
            'is_online' => false,
            'last_activity' => now(),
        ]);

        return response()->json($user, 201);
    }

    public function update(Request $request, User $manager)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $manager->id,
            'password' => 'nullable|string|min:8',
            'phone' => ['required', 'regex:/^(09|\+639)\d{9}$|^(0[2-8]|\+63[2-8])\d{7,8}$/'],
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->only(['name', 'email', 'phone', 'notes']);
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $manager->update($data);

        return response()->json($manager);
    }

    public function destroy(User $manager)
    {
        $manager->delete();

        return response()->json(null, 200);
    }
}
