<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pokja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            $request->session()->regenerate();
            return $this->redirectAfterLogin(Auth::user());
        }

        return back()->withErrors(['email' => 'Email atau password salah.'])->onlyInput('email');
    }

    public function showRegisterForm()
    {
        $pokjas = Pokja::orderBy('group')->orderBy('code')->get();
        return view('auth.register', compact('pokjas'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'pokja_id' => 'required|exists:pokjas,id',
            'role' => 'required|in:user,ketua_tim,it,verifikator,regulasi',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'pokja_id' => $request->pokja_id,
            'role' => $request->role,
        ]);

        Auth::login($user, true);
        return $this->redirectAfterLogin($user)->with('success', 'Registrasi berhasil!');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Gagal login dengan Google.');
        }

        $user = User::where('email', $googleUser->email)->first();

        if ($user) {
            $user->update([
                'google_id' => $googleUser->id,
                'avatar' => $googleUser->avatar,
            ]);
            Auth::login($user, true);
            return $this->redirectAfterLogin($user);
        }

        // Auto-create account for Google first-timers
        $user = User::create([
            'name' => $googleUser->name,
            'email' => $googleUser->email,
            'google_id' => $googleUser->id,
            'avatar' => $googleUser->avatar,
            'role' => 'user',
            'pokja_id' => null,
        ]);

        Auth::login($user, true);
        return redirect()->route('pokja.index')->with('success', 'Akun Google berhasil terhubung. Silakan hubungi IT/Ketua Tim untuk assignment Pokja.');
    }

    private function redirectAfterLogin($user)
    {
        if ($user->isAdmin()) {
            return redirect()->route('dashboard');
        }
        return redirect()->route('pokja.index');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $rules = [];

        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        if ($request->has('pokja_id')) {
            $rules['pokja_id'] = 'nullable|exists:pokjas,id';
        }

        $request->validate($rules);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->has('pokja_id')) {
            $user->pokja_id = $request->pokja_id ?: null;
        }

        $user->save();

        return response()->json(['success' => true, 'message' => 'Profil berhasil diperbarui.']);
    }
}
