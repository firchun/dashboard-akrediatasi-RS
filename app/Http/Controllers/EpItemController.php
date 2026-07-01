<?php

namespace App\Http\Controllers;

use App\Models\Pokja;
use App\Models\EpItem;
use Illuminate\Http\Request;

class EpItemController extends Controller
{
    public function index($code)
    {
        $pokja = Pokja::where('code', $code)->firstOrFail();
        $items = $pokja->epItems;

        return response()->json($items);
    }

    public function penilaian($code)
    {
        $user = auth()->user();

        if (!$user->isAdmin()) {
            if (!$user->pokja || $user->pokja->code !== $code) {
                abort(403);
            }
        }

        $pokja = Pokja::where('code', $code)
            ->with(['standars.epItems', 'epItems'])
            ->firstOrFail();

        $setting = \App\Models\Setting::first();

        return view('ep.penilaian', compact('pokja', 'setting'));
    }

    public function storeStandar(Request $request, $code)
    {
        $pokja = Pokja::where('code', $code)->firstOrFail();

        $standar = \App\Models\Standar::create([
            'pokja_id' => $pokja->id,
            'kode' => $request->kode,
            'uraian' => $request->uraian,
        ]);

        return response()->json($standar);
    }

    public function store(Request $request, $code)
    {
        $pokja = Pokja::where('code', $code)->firstOrFail();

        $item = EpItem::create([
            'pokja_id' => $pokja->id,
            'standar_id' => $request->standar_id,
            'no_urut' => $request->no_urut ?? '',
            'uraian' => $request->uraian ?? '',
            'bukti_r' => $request->boolean('bukti_r'),
            'bukti_d' => $request->boolean('bukti_d'),
            'bukti_o' => $request->boolean('bukti_o'),
            'bukti_w' => $request->boolean('bukti_w'),
            'bukti_s' => $request->boolean('bukti_s'),
            'nilai' => $request->nilai ?? '',
            'fakta_analisis' => $request->fakta_analisis ?? '',
            'rekomendasi' => $request->rekomendasi ?? '',
            'pengingat' => $request->pengingat ?? '',
            'pic' => $request->pic ?? '',
            'link' => $request->link ?? '',
            'keterangan' => $request->keterangan ?? '',
        ]);

        return response()->json($item->load('pokja', 'standar'));
    }

    public function update(Request $request, $id)
    {
        $item = EpItem::findOrFail($id);

        $item->update([
            'standar_id' => $request->standar_id ?? $item->standar_id,
            'no_urut' => $request->no_urut ?? $item->no_urut,
            'uraian' => $request->uraian ?? $item->uraian,
            'bukti_r' => $request->has('bukti_r') ? $request->boolean('bukti_r') : $item->bukti_r,
            'bukti_d' => $request->has('bukti_d') ? $request->boolean('bukti_d') : $item->bukti_d,
            'bukti_o' => $request->has('bukti_o') ? $request->boolean('bukti_o') : $item->bukti_o,
            'bukti_w' => $request->has('bukti_w') ? $request->boolean('bukti_w') : $item->bukti_w,
            'bukti_s' => $request->has('bukti_s') ? $request->boolean('bukti_s') : $item->bukti_s,
            'nilai' => $request->nilai ?? $item->nilai,
            'fakta_analisis' => $request->fakta_analisis ?? $item->fakta_analisis,
            'rekomendasi' => $request->rekomendasi ?? $item->rekomendasi,
            'pengingat' => $request->pengingat ?? $item->pengingat,
            'pic' => $request->pic ?? $item->pic,
            'link' => $request->link ?? $item->link,
            'keterangan' => $request->keterangan ?? $item->keterangan,
        ]);

        return response()->json($item);
    }

    public function destroy($id)
    {
        $item = EpItem::findOrFail($id);
        $item->delete();

        return response()->json(['success' => true]);
    }

    public function import(Request $request, $code)
    {
        $pokja = Pokja::where('code', $code)->firstOrFail();

        $request->validate([
            'lines' => 'required|string',
            'replace' => 'boolean',
        ]);

        if ($request->boolean('replace')) {
            EpItem::where('pokja_id', $pokja->id)->delete();
            \App\Models\Standar::where('pokja_id', $pokja->id)->delete();
        }

        $lines = explode("\n", $request->lines);
        $imported = 0;
        $currentStandar = null;

        foreach ($lines as $line) {
            $line = rtrim($line, "\r\n");
            if (empty(trim($line))) continue;

            $cols = str_contains($line, "\t") ? explode("\t", $line) : explode(";", $line);
            
            $colStandar = trim($cols[0] ?? '');
            $colNoUrut = trim($cols[1] ?? '');
            $colUraian = trim($cols[2] ?? '');
            $colR = trim($cols[3] ?? '');
            $colD = trim($cols[4] ?? '');
            $colO = trim($cols[5] ?? '');
            $colW = trim($cols[6] ?? '');
            $colS = trim($cols[7] ?? '');
            $colNilai = strtoupper(trim($cols[8] ?? ''));
            $colFakta = trim($cols[9] ?? '');
            $colRekom = trim($cols[10] ?? '');
            $colPengingat = trim($cols[11] ?? '');

            if (!empty($colStandar)) {
                $currentStandar = \App\Models\Standar::create([
                    'pokja_id' => $pokja->id,
                    'kode' => $colStandar,
                    'uraian' => $colUraian,
                ]);
                continue;
            }

            if (!empty($colNoUrut) && $currentStandar) {
                EpItem::create([
                    'pokja_id' => $pokja->id,
                    'standar_id' => $currentStandar->id,
                    'no_urut' => $colNoUrut,
                    'uraian' => $colUraian,
                    'bukti_r' => !empty($colR),
                    'bukti_d' => !empty($colD),
                    'bukti_o' => !empty($colO),
                    'bukti_w' => !empty($colW),
                    'bukti_s' => !empty($colS),
                    'nilai' => in_array($colNilai, ['TL', 'TS', 'TT', 'TDD']) ? $colNilai : '',
                    'fakta_analisis' => $colFakta,
                    'rekomendasi' => $colRekom,
                    'pengingat' => $colPengingat,
                    'pic' => '',
                    'link' => '',
                    'keterangan' => '',
                ]);
                $imported++;
            }
        }

        return response()->json([
            'success' => true,
            'imported' => $imported,
        ]);
    }
}
