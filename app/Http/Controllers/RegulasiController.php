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

        $is_verified = $request->is_verified ? true : false;
        if (auth()->user()->role === 'user') {
            $is_verified = false;
        }

        if ($is_verified) {
            if (empty($request->pic) || empty($request->link)) {
                return response()->json(['message' => 'Status Selesai membutuhkan dokumen dan PIC yang terisi.'], 400);
            }
        }

        $reg = Regulasi::create([
            'pokja_id' => $pokja->id,
            'nama' => $request->nama ?? '',
            'jenis' => $request->jenis ?? 'Panduan',
            'pic' => $request->pic ?? '',
            'link' => $request->link ?? '',
            'keterangan' => $request->keterangan ?? '',
            'is_verified' => $is_verified,
        ]);

        return response()->json($reg->load('pokja'));
    }

    public function update(Request $request, $id)
    {
        $reg = Regulasi::findOrFail($id);

        $data = $request->only([
            'nama', 'jenis', 'pic', 'link', 'keterangan'
        ]);

        if (auth()->user()->role === 'verifikator' || auth()->user()->isAdmin()) {
            if ($request->has('is_verified')) {
                $checkVerified = $request->is_verified ? true : false;
                
                if ($checkVerified) {
                    $checkPic = $data['pic'] ?? $reg->pic;
                    $checkLink = $data['link'] ?? $reg->link;

                    if (empty($checkPic) || empty($checkLink)) {
                        return response()->json(['message' => 'Status Selesai (Verifikasi) membutuhkan dokumen dan PIC yang terisi.'], 400);
                    }
                }
                
                $data['is_verified'] = $checkVerified;
            }
        }

        $reg->update($data);

        return response()->json($reg);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|in:regulasi,ep',
            'file' => 'required|file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,webp,zip,rar', // limit to 20MB
        ], [
            'file.mimes' => 'Format file tidak valid. Hanya gambar (jpg/png) dan dokumen (pdf/doc/xls/dll) yang diperbolehkan.'
        ]);

        if ($request->type === 'regulasi') {
            $item = Regulasi::with('pokja')->findOrFail($request->id);
            if ($item->is_verified) {
                return response()->json(['message' => 'Dokumen sudah terverifikasi dan tidak dapat diubah.'], 403);
            }
        } else {
            $item = \App\Models\EpItem::with('pokja')->findOrFail($request->id);
        }

        $file = $request->file('file');
        $history = $item->history ?? [];
        $newVersion = count($history) + 1;
        
        $baseName = \Illuminate\Support\Str::slug($item->nama ?? $item->kode ?? 'doc');
        $pokjaCode = $item->pokja->code ?? 'UMUM';
        $fileName = $baseName . '_' . $pokjaCode . '_v' . $newVersion . '_' . date('dmYHis') . '.' . $file->getClientOriginalExtension();
        
        $path = $file->storeAs('uploads', $fileName, 'public');
        $url = asset('storage/' . $path);

        $history[] = [
            'version' => $newVersion,
            'url' => $url,
            'filename' => $fileName,
            'uploaded_at' => now()->toDateTimeString(),
            'uploaded_by' => auth()->user()->name ?? 'System'
        ];

        $item->update([
            'link' => $url,
            'history' => $history
        ]);

        return response()->json([
            'success' => true,
            'url' => $url,
            'status' => $item->status ?? null,
            'history' => $history
        ]);
    }

    public function destroy($id)
    {
        $reg = Regulasi::findOrFail($id);
        $reg->delete();

        return response()->json(['success' => true]);
    }
}
