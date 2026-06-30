<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Pokja;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        $pokjas = Pokja::withCount('users')->orderBy('group')->orderBy('code')->get();

        return view('settings.index', compact('setting', 'pokjas'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'hospital_name' => 'required|string|max:255',
            'target_date' => 'nullable|date',
            'is_pendidikan' => 'boolean',
            'prognas_full' => 'boolean',
            'calc_mode' => 'in:bobot,selesai',
        ]);

        $setting = Setting::first();
        $setting->update([
            'hospital_name' => $request->hospital_name,
            'target_date' => $request->target_date,
            'is_pendidikan' => $request->boolean('is_pendidikan'),
            'prognas_full' => $request->boolean('prognas_full'),
            'calc_mode' => $request->calc_mode ?? 'bobot',
        ]);

        return redirect()->route('settings.index')->with('success', 'Pengaturan berhasil disimpan.');
    }
}
