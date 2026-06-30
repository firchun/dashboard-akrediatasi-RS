<?php

namespace App\Http\Controllers;

use App\Models\Pokja;
use App\Models\Regulasi;
use App\Models\Setting;
use Illuminate\Http\Request;

class RegulasiController extends Controller
{
    public function store(Request $request, $code)
    {
        $pokja = Pokja::where('code', $code)->firstOrFail();

        $status = $request->status ?? 'Belum';
        if (auth()->user()->role === 'user') {
            $status = 'Belum';
        }

        $reg = Regulasi::create([
            'pokja_id' => $pokja->id,
            'nama' => $request->nama ?? '',
            'jenis' => $request->jenis ?? 'Panduan',
            'pic' => $request->pic ?? '',
            'target' => $request->target,
            'link' => $request->link ?? '',
            'keterangan' => $request->keterangan ?? '',
            'status' => $status,
        ]);

        return response()->json($reg->load('pokja'));
    }

    public function update(Request $request, $id)
    {
        $reg = Regulasi::findOrFail($id);

        $data = $request->only([
            'nama', 'jenis', 'pic', 'target', 'link', 'keterangan', 'status'
        ]);

        if (auth()->user()->role === 'user') {
            unset($data['status']);
        }

        $reg->update($data);

        return response()->json($reg);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|in:regulasi,ep',
            'file' => 'required|file|max:20480', // limit to 20MB
        ]);

        $file = $request->file('file');
        $fileName = time() . '_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $file->getClientOriginalName());
        $path = $file->storeAs('uploads', $fileName, 'public');
        $url = asset('storage/' . $path);

        if ($request->type === 'regulasi') {
            $item = Regulasi::findOrFail($request->id);
        } else {
            $item = \App\Models\EpItem::findOrFail($request->id);
        }

        $item->update(['link' => $url]);

        return response()->json([
            'success' => true,
            'url' => $url
        ]);
    }

    public function destroy($id)
    {
        $reg = Regulasi::findOrFail($id);
        $reg->delete();

        return response()->json(['success' => true]);
    }
}
