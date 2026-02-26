<?php

namespace App\Http\Controllers\AuthController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Role;

class AuthController extends \App\Http\Controllers\Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::find(Auth::id());
            if ($user) {
                $user->last_login_at = now();
                $user->save();
            }
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    public function showRegisterForm()
    {
        $roles = Role::all();
        return view('auth.register', compact('roles'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|max:100|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $request->password, // User model mutator will hash this
        ]);

        // Assign role
        $user->roles()->attach($request->role_id, [
            'assigned_by' => null, // or current admin user id if available
            'assigned_at' => now()
        ]);

        // Redirect to login page without auto-login
        return redirect('/login')->with('success', 'Registration successful! Please login with your credentials.');
    }

    public function dashboard()
    {
        $user = Auth::user();
        $roles = $user->roles->pluck('name')->toArray();

        return view('dashboard.index', compact('user', 'roles'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    // Other methods for different pages...
    public function bagian() { return view('bagian.index'); }
    public function level() { return view('level.index'); }
    public function status() { return view('status.index'); }
    public function user() { return view('user.index'); }
    public function userprofile() { return view('userprofile.index'); }
    public function proyek() { return view('proyek.index'); }
    public function keterangan() { return view('keterangan.index'); }
    public function aktivitas() { return view('aktivitas.index'); }
    public function ModeJamKerja() { return view('modejamkerja.index'); }
    public function StatusJamKerja() { return view('statusjamkerja.index'); }
    public function progresKerja() { return view('progreskerja.index'); }
    public function lembur() { return view('lembur.index'); }
    public function pesan() { return view('pesan.index'); }
    public function jenisPesan() { return view('jenispesan.index'); }
    public function rekan() { return view('rekan.index'); }
    public function profile() { return view('profile.index'); }
    public function updateProfile(Request $request) { /* implementation */ }
    public function uploadFoto(Request $request) { /* implementation */ }

    public function apiLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = User::find(Auth::id());
            if ($user) {
                $user->last_login_at = now();
                $user->save();
                $token = $user->createToken('API Token')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'user' => $user,
                    'token' => $token,
                    'roles' => $user->roles->pluck('name')->toArray(),
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function apiRegister(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'username' => 'nullable|string|max:100|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => $request->password, // User model mutator will hash this
        ]);

        // Assign role
        $user->roles()->attach($request->role_id, [
            'assigned_by' => null,
            'assigned_at' => now()
        ]);

        $token = $user->createToken('API Token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'roles' => $user->roles->pluck('name')->toArray(),
        ], 201);
    }
}
