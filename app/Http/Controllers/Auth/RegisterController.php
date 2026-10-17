<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function register()
    {
        $roles = Role::registrable();
        return view('auth.register',compact('roles'));
    }

    public function storeUser(Request $request)
    {
        try {
            $request->validate([
                'name'      => 'required|string|max:255',
                'email'     => 'required|string|email|max:255|unique:users',
                'role_name' => ['required', Rule::enum(Role::class)->only(Role::registrable())],
                'password'  => 'required|string|min:8|confirmed',
                'password_confirmation' => 'required',
            ]);
    
            User::create([
                'name'      => $request->name,
                'avatar'    => $request->image,
                'email'     => $request->email,
                'join_date' => now(),
                'role_name' => $request->role_name,
                'password'  => Hash::make($request->password),
            ]);
    
            return redirect()->route('login')->with('success', 'Create new account successfully :)');
        } catch (QueryException $e) {
            Log::error('Creating user: ' . $e->getMessage() );
            return back()->with('error', 'Create new account failed :(');
        }
    }
}
