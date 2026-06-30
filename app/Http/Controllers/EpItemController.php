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

    public function store(Request $request, $code)
    {
        $pokja = Pokja::where('code', $code)->firstOrFail();

        $item = EpItem::create([
            'pokja_id' => $pokja->id,
            'kode' => $request->kode ?? '',
            'uraian' => $request->uraian ?? '',
            'bukti_r' => $request->boolean('bukti_r'),
            'bukti_d' => $request->boolean('bukti_d'),
            'bukti_o' => $request->boolean('bukti_o'),
            'bukti_w' => $request->boolean('bukti_w'),
            'bukti_s' => $request->boolean('bukti_s'),
            'nilai' => $request->nilai ?? '',
            'pic' => $request->pic ?? '',
            'link' => $request->link ?? '',
            'keterangan' => $request->keterangan ?? '',
        ]);

        return response()->json($item->load('pokja'));
    }

    public function update(Request $request, $id)
    {
        $item = EpItem::findOrFail($id);

        $item->update([
            'kode' => $request->kode ?? $item->kode,
            'uraian' => $request->uraian ?? $item->uraian,
            'bukti_r' => $request->has('bukti_r') ? $request->boolean('bukti_r') : $item->bukti_r,
            'bukti_d' => $request->has('bukti_d') ? $request->boolean('bukti_d') : $item->bukti_d,
            'bukti_o' => $request->has('bukti_o') ? $request->boolean('bukti_o') : $item->bukti_o,
            'bukti_w' => $request->has('bukti_w') ? $request->boolean('bukti_w') : $item->bukti_w,
            'bukti_s' => $request->has('bukti_s') ? $request->boolean('bukti_s') : $item->bukti_s,
            'nilai' => $request->nilai ?? $item->nilai,
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
        }

        $lines = explode("\n", $request->lines);
        $imported = 0;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $cols = str_contains($line, "\t") ? explode("\t", $line) : explode(";", $line);
            $kode = trim($cols[0] ?? '');
            $uraian = trim($cols[1] ?? '');
            $buktiStr = trim($cols[2] ?? '');
            $nilai = strtoupper(trim($cols[3] ?? ''));

            if (empty($kode) && empty($uraian)) continue;

            $bukti = [];
            foreach (str_split($buktiStr) as $ch) {
                if (in_array($ch, ['R', 'D', 'O', 'W', 'S'])) $bukti[] = $ch;
            }

            EpItem::create([
                'pokja_id' => $pokja->id,
                'kode' => $kode,
                'uraian' => $uraian,
                'bukti_r' => in_array('R', $bukti),
                'bukti_d' => in_array('D', $bukti),
                'bukti_o' => in_array('O', $bukti),
                'bukti_w' => in_array('W', $bukti),
                'bukti_s' => in_array('S', $bukti),
                'nilai' => in_array($nilai, ['', 'TL', 'TS', 'TT', 'TDD']) ? $nilai : '',
                'pic' => '',
                'link' => '',
                'keterangan' => '',
            ]);

            $imported++;
        }

        return response()->json([
            'success' => true,
            'imported' => $imported,
        ]);
    }
}
