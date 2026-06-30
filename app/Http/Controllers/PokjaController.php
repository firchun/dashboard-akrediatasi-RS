<?php

namespace App\Http\Controllers;

use App\Models\Pokja;
use App\Models\Setting;
use Illuminate\Http\Request;

class PokjaController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $setting = Setting::first();
        $groups = ['MANAJEMEN', 'PASIEN', 'SKP', 'PROGNAS'];

        if (!$user->isAdmin()) {
            if ($user->pokja) {
                $pokjas = Pokja::where('id', $user->pokja_id)
                    ->with(['regulasis', 'epItems'])
                    ->get();
            } else {
                $pokjas = collect();
            }
        } else {
            $pokjas = Pokja::with(['regulasis', 'epItems'])->get();
        }

        return view('pokja.index', compact('pokjas', 'groups', 'setting'));
    }

    public function show($code)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            if (!$user->pokja || $user->pokja->code !== $code) {
                abort(403);
            }
        }

        $pokja = Pokja::where('code', $code)
            ->with(['regulasis', 'epItems'])
            ->firstOrFail();

        $setting = Setting::first();

        return view('pokja.show', compact('pokja', 'setting'));
    }
}
