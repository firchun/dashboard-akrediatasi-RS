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

        return response()->json($reg->load(['pokja', 'uploadFiles.user']));
    }

    public function update(Request $request, $id)
    {
        $reg = Regulasi::findOrFail($id);

        $data = $request->only([
            'nama',
            'jenis',
            'pic',
            'link',
            'keterangan'
        ]);

        if (isset($data['link']) && $data['link'] !== '' && $data['link'] !== $reg->link && !empty($reg->link)) {
            \App\Models\UploadFile::create([
                'jenis_upload' => 'regulasi',
                'file' => $data['link'],
                'id_user' => auth()->id(),
                'related_id' => $reg->id
            ]);
            unset($data['link']);
        }

        if (auth()->user()->role === 'verifikator' || auth()->user()->isAdmin()) {
            if ($request->has('is_verified')) {
                $checkVerified = $request->is_verified ? true : false;

                if ($checkVerified) {
                    $checkPic = $data['pic'] ?? $reg->pic;
                    $checkLink = $data['link'] ?? $reg->link;
                    $hasFiles = $reg->uploadFiles()->exists();

                    if (empty($checkPic) || (empty($checkLink) && !$hasFiles)) {
                        return response()->json(['message' => 'Status Selesai (Verifikasi) membutuhkan dokumen dan PIC yang terisi.'], 400);
                    }
                }

                $data['is_verified'] = $checkVerified;
            }
        }

        $reg->update($data);

        $reg->load('uploadFiles.user');

        return response()->json($reg);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|in:regulasi,ep',
            'file' => [
                'required',
                'file',
                'max:102400',
                function ($attribute, $value, $fail) {
                    $ext = strtolower($value->getClientOriginalExtension());
                    $allowed = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'jpg', 'jpeg', 'png', 'webp', 'zip', 'rar', 'mp4', 'mkv', 'avi', 'mov', 'webm'];
                    if (!in_array($ext, $allowed)) {
                        $fail('Format file tidak valid. Hanya gambar (jpg/png), dokumen (pdf/doc/xls/dll), dan video (mp4/mkv/dll) yang diperbolehkan.');
                    }
                }
            ],
        ]);

        if ($request->type === 'regulasi') {
            $item = Regulasi::with('pokja')->findOrFail($request->id);
            if ($item->is_verified) {
                return response()->json(['message' => 'Dokumen sudah terverifikasi dan tidak dapat diubah.'], 403);
            }
        } else {
            $item = \App\Models\EpItem::with(['pokja', 'standar'])->findOrFail($request->id);
        }

        $file = $request->file('file');
        
        $pokjaCode = $item->pokja?->code ?? 'UMUM';
        $type = $request->type; // 'regulasi' or 'ep'

        if ($type === 'ep') {
            $standardCode = $item->standar?->kode ?? 'std';
            $noUrut = $item->no_urut ?? '1';
            $baseName = \Illuminate\Support\Str::slug($standardCode . '-ep-' . $noUrut);
        } else {
            $baseName = \Illuminate\Support\Str::slug($item->nama ?? 'doc');
        }

        // Hitung jumlah file baru yang sudah ada untuk menentukan versi
        $existingNewFiles = \App\Models\UploadFile::where('related_id', $item->id)
                            ->where('jenis_upload', $type)
                            ->count();
        $newVersion = $existingNewFiles + 1;

        $fileName = $pokjaCode . '_' . $type . '_' . $baseName . '_v' . $newVersion . '_' . date('dmYHis') . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs('uploads', $fileName, 'public');
        $url = asset('storage/' . $path);

        $uploadRecord = \App\Models\UploadFile::create([
            'jenis_upload' => $request->type,
            'file' => $url,
            'id_user' => auth()->id(),
            'related_id' => $item->id
        ]);

        return response()->json([
            'success' => true,
            'url' => $url,
            'status' => $item->status ?? null,
            'upload' => $uploadRecord->load('user')
        ]);
    }

    public function destroy($id)
    {
        $reg = Regulasi::findOrFail($id);
        $reg->delete();
        return response()->json(['success' => true]);
    }

    public function deleteUpload(Request $request, $id)
    {
        $isLink = $request->boolean('is_link');
        $type = $request->input('type');
        $item = null;
        $pokjaCode = '';

        if ($isLink) {
            if ($type === 'ep') {
                $item = \App\Models\EpItem::with(['uploadFiles', 'pokja'])->find($id);
            } else {
                $item = Regulasi::with(['uploadFiles', 'pokja'])->find($id);
            }
            if ($item) {
                $item->update(['link' => null]);
                $item->refresh();
                $pokjaCode = $item->pokja->code ?? '';
            }
        } else {
            $upload = \App\Models\UploadFile::find($id);
            if ($upload) {
                if ($type === 'ep') {
                    $item = \App\Models\EpItem::with(['uploadFiles', 'pokja'])->find($upload->related_id);
                } else {
                    $item = Regulasi::with(['uploadFiles', 'pokja'])->find($upload->related_id);
                }
                $upload->delete();
                if ($item) {
                    $item->load('uploadFiles');
                    $item->upload_files = $item->uploadFiles; // Ensure frontend gets the array as 'upload_files' if it expects it
                    $pokjaCode = $item->pokja->code ?? '';
                }
            }
        }

        if ($item) {
            // Append upload_files explicitly since model returns uploadFiles by default
            $item->setAttribute('upload_files', $item->uploadFiles);
        }

        return response()->json([
            'success' => true,
            'item' => $item,
            'pokja_code' => $pokjaCode
        ]);
    }

}
