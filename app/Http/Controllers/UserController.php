<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pokja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('pokja')->orderBy('created_at', 'desc')->get();
        $pokjas = Pokja::orderBy('group')->orderBy('code')->get();

        return view('settings.users', compact('users', 'pokjas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'pokja_id' => 'nullable|exists:pokjas,id',
            'role' => 'required|in:user,ketua_tim,it,verifikator,regulasi',
        ]);

        User::create($request->only(['name', 'email', 'pokja_id', 'role']));

        return redirect()->route('settings.users')->with('success', 'User berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'pokja_id' => 'nullable|exists:pokjas,id',
            'role' => 'required|in:user,ketua_tim,it,verifikator,regulasi',
        ]);

        $user->update($request->only(['name', 'email', 'pokja_id', 'role']));

        return redirect()->route('settings.users')->with('success', 'User berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('settings.users')->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();

        return redirect()->route('settings.users')->with('success', 'User berhasil dihapus.');
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        $user->update([
            'password' => Hash::make('password')
        ]);

        return redirect()->route('settings.users')->with('success', 'Password user ' . $user->name . ' berhasil direset menjadi "password".');
    }
}
