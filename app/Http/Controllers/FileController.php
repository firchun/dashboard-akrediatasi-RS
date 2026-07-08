<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pokja;
use App\Models\Regulasi;
use App\Models\EpItem;
use App\Models\UploadFile;

class FileController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;
        if (!in_array($role, ['it', 'ketua_tim', 'regulasi', 'admin'])) {
            abort(403, 'Akses ditolak.');
        }

        return view('file.index');
    }

    public function getData(Request $request)
    {
        $role = auth()->user()->role;
        if (!in_array($role, ['it', 'ketua_tim', 'regulasi', 'admin'])) {
            return response()->json(['message' => 'Akses ditolak.'], 403);
        }

        $pokjaCode = $request->query('pokja');
        $type = $request->query('type'); // 'regulasi' or 'ep'
        $standarId = $request->query('standar_id');
        $itemId = $request->query('item_id');

        $breadcrumbs = [
            ['title' => 'File Manager', 'query' => []]
        ];

        $folders = [];
        $files = [];

        if (!$pokjaCode) {
            // Level 0: List Pokja
            $pokjas = Pokja::orderBy('code')->get();
            foreach ($pokjas as $p) {
                $folders[] = [
                    'name' => $p->code,
                    'desc' => $p->name,
                    'query' => ['pokja' => $p->code]
                ];
            }
        } else {
            $pokja = Pokja::where('code', $pokjaCode)->first();
            if (!$pokja) return response()->json(['message' => 'Pokja tidak ditemukan'], 404);

            $breadcrumbs[] = ['title' => $pokja->code, 'query' => ['pokja' => $pokja->code]];

            if (!$type) {
                // Level 1: List 'Regulasi' and 'Elemen Penilaian' folders
                $folders[] = [
                    'name' => 'Regulasi',
                    'desc' => 'Dokumen Regulasi ' . $pokja->code,
                    'query' => ['pokja' => $pokja->code, 'type' => 'regulasi']
                ];
                $folders[] = [
                    'name' => 'Elemen Penilaian',
                    'desc' => 'Dokumen Bukti EP ' . $pokja->code,
                    'query' => ['pokja' => $pokja->code, 'type' => 'ep']
                ];
            } else {
                $breadcrumbs[] = [
                    'title' => $type === 'regulasi' ? 'Regulasi' : 'Elemen Penilaian',
                    'query' => ['pokja' => $pokja->code, 'type' => $type]
                ];

                if (!$itemId) {
                    // Level 2: List Items
                    if ($type === 'regulasi') {
                        $items = Regulasi::where('pokja_id', $pokja->id)->orderBy('id')->get();
                        foreach ($items as $item) {
                            $folders[] = [
                                'name' => $item->nama ?: 'Tanpa Nama',
                                'desc' => $item->jenis,
                                'query' => ['pokja' => $pokja->code, 'type' => 'regulasi', 'item_id' => $item->id]
                            ];
                        }
                    } else {
                        if (!$standarId) {
                            $standars = \App\Models\Standar::where('pokja_id', $pokja->id)->orderBy('kode')->get();
                            foreach ($standars as $s) {
                                $folders[] = [
                                    'name' => 'Standar ' . $s->kode,
                                    'desc' => $s->name,
                                    'query' => ['pokja' => $pokja->code, 'type' => 'ep', 'standar_id' => $s->id]
                                ];
                            }
                        } else {
                            $standar = \App\Models\Standar::find($standarId);
                            if ($standar) {
                                $breadcrumbs[] = [
                                    'title' => 'Standar ' . $standar->kode,
                                    'query' => ['pokja' => $pokja->code, 'type' => 'ep', 'standar_id' => $standar->id]
                                ];

                                $eps = EpItem::where('standar_id', $standarId)->orderBy('no_urut')->get();
                                foreach ($eps as $ep) {
                                    $folders[] = [
                                        'name' => 'EP ' . $ep->no_urut,
                                        'desc' => \Illuminate\Support\Str::limit($ep->uraian, 50),
                                        'query' => ['pokja' => $pokja->code, 'type' => 'ep', 'standar_id' => $standar->id, 'item_id' => $ep->id]
                                    ];
                                }
                            }
                        }
                    }
                } else {
                    // Level 3: Files
                    if ($type === 'ep' && $standarId) {
                        $standar = \App\Models\Standar::find($standarId);
                        if ($standar) {
                            $breadcrumbs[] = [
                                'title' => 'Standar ' . $standar->kode,
                                'query' => ['pokja' => $pokja->code, 'type' => 'ep', 'standar_id' => $standar->id]
                            ];
                        }
                    }

                    if ($type === 'regulasi') {
                        $item = Regulasi::find($itemId);
                        if ($item) {
                            $breadcrumbs[] = [
                                'title' => \Illuminate\Support\Str::limit($item->nama ?: 'Tanpa Nama', 30),
                                'query' => ['pokja' => $pokja->code, 'type' => 'regulasi', 'item_id' => $item->id]
                            ];
                            $filesResult = UploadFile::with('user')->where('jenis_upload', 'regulasi')->where('related_id', $item->id)->orderBy('created_at', 'desc')->get();
                            
                            $files = $filesResult->map(function ($f) {
                                return [
                                    'id' => $f->id,
                                    'file' => $f->file,
                                    'filename' => basename($f->file),
                                    'jenis_upload' => $f->jenis_upload,
                                    'user_name' => $f->user ? $f->user->name : 'Sistem',
                                    'user_avatar' => $f->user ? $f->user->avatar : null,
                                    'created_at' => \Carbon\Carbon::parse($f->created_at)->translatedFormat('d M Y, H:i'),
                                    'is_virtual' => false
                                ];
                            })->toArray();

                            if (!empty($item->link)) {
                                $files[] = [
                                    'id' => 'link_' . $item->id,
                                    'file' => $item->link,
                                    'filename' => basename($item->link),
                                    'jenis_upload' => 'regulasi',
                                    'user_name' => 'Link Manual',
                                    'user_avatar' => null,
                                    'created_at' => \Carbon\Carbon::parse($item->updated_at)->translatedFormat('d M Y, H:i'),
                                    'is_virtual' => true
                                ];
                            }
                        }
                    } else {
                        $item = EpItem::find($itemId);
                        if ($item) {
                            $breadcrumbs[] = [
                                'title' => 'EP ' . $item->no_urut,
                                'query' => ['pokja' => $pokja->code, 'type' => 'ep', 'standar_id' => $standarId, 'item_id' => $item->id]
                            ];
                            
                            $filesResult = UploadFile::with('user')->where('jenis_upload', 'ep')->where('related_id', $item->id)->orderBy('created_at', 'desc')->get();
                            
                            $files = $filesResult->map(function ($f) {
                                return [
                                    'id' => $f->id,
                                    'file' => $f->file,
                                    'filename' => basename($f->file),
                                    'jenis_upload' => $f->jenis_upload,
                                    'user_name' => $f->user ? $f->user->name : 'Sistem',
                                    'user_avatar' => $f->user ? $f->user->avatar : null,
                                    'created_at' => \Carbon\Carbon::parse($f->created_at)->translatedFormat('d M Y, H:i'),
                                    'is_virtual' => false
                                ];
                            })->toArray();

                            if (!empty($item->link)) {
                                $files[] = [
                                    'id' => 'link_' . $item->id,
                                    'file' => $item->link,
                                    'filename' => basename($item->link),
                                    'jenis_upload' => 'ep',
                                    'user_name' => 'Link Manual',
                                    'user_avatar' => null,
                                    'created_at' => \Carbon\Carbon::parse($item->updated_at)->translatedFormat('d M Y, H:i'),
                                    'is_virtual' => true
                                ];
                            }
                        }
                    }
                }
            }
        }

        return response()->json([
            'breadcrumbs' => $breadcrumbs,
            'folders' => $folders,
            'files' => $files
        ]);
    }
}
