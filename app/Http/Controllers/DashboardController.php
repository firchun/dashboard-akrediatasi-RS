<?php

namespace App\Http\Controllers;

use App\Models\Pokja;
use App\Models\Regulasi;
use App\Models\Setting;

class DashboardController extends Controller
{
    public function index()
    {
        $setting = Setting::first();
        $pokjas = Pokja::withCount(['regulasis', 'epItems'])->with('regulasis')->get();
        $globalStats = $this->globalStats($pokjas);

        return view('dashboard.index', compact('setting', 'pokjas', 'globalStats'));
    }

    private function globalStats($pokjas)
    {
        $setting = Setting::first();
        $stats = ['Belum' => 0, 'Proses' => 0, 'Review' => 0, 'Selesai' => 0, 'overdue' => 0, 'total' => 0];
        $today = now()->format('Y-m-d');

        foreach ($pokjas as $pokja) {
            foreach ($pokja->regulasis as $reg) {
                $stats[$reg->status]++;
                $stats['total']++;
                if ($reg->status !== 'Selesai' && $setting && $setting->target_date && $setting->target_date->format('Y-m-d') < $today) {
                    $stats['overdue']++;
                }
            }
        }

        $stats['pct'] = $this->readinessAll($pokjas);
        return (object) $stats;
    }

    private function readinessAll($pokjas)
    {
        $setting = Setting::first();
        $total = 0;
        $weightSum = 0;

        foreach ($pokjas as $pokja) {
            foreach ($pokja->regulasis as $reg) {
                $total++;
                $weightSum += $this->itemWeight($reg);
            }
        }

        if ($total === 0) return 0;

        if ($setting && $setting->calc_mode === 'bobot') {
            return round($weightSum / $total);
        }

        // count only "Selesai"
        $done = 0;
        foreach ($pokjas as $pokja) {
            foreach ($pokja->regulasis as $reg) {
                if ($reg->status === 'Selesai') $done++;
            }
        }
        return round($done / $total * 100);
    }

    public static function itemWeight($reg)
    {
        $weights = ['Belum' => 0, 'Proses' => 50, 'Review' => 85, 'Selesai' => 100];
        return $weights[$reg->status] ?? 0;
    }

    public static function epScore($pokja)
    {
        $setting = Setting::first();
        $epItems = $pokja->epItems;

        if ($epItems->count() > 0) {
            $tl = $epItems->where('nilai', 'TL')->count();
            $ts = $epItems->where('nilai', 'TS')->count();
            $tdd = $epItems->where('nilai', 'TDD')->count();
            $total = $epItems->count();
        } else {
            $regs = $pokja->regulasis;
            if ($regs->count() === 0) return 0;
            $total = $pokja->ep_total;
            $app = max(0, $total);
            if ($app === 0) return 0;
            $sel = $regs->whereIn('status', ['Selesai'])->count();
            $pr = $regs->whereIn('status', ['Proses', 'Review'])->count();
            $n = $regs->count();
            $tl = round($app * $sel / $n);
            $ts = round($app * $pr / $n);
            if ($tl > $app) $tl = $app;
            if ($tl + $ts > $app) $ts = $app - $tl;
        }

        $app = max(0, $total);
        if ($app <= 0) return 0;
        return round(($tl * 10 + $ts * 5) / ($app * 10) * 100);
    }

    public static function pokjaStats($pokja)
    {
        $setting = Setting::first();
        $stats = ['Belum' => 0, 'Proses' => 0, 'Review' => 0, 'Selesai' => 0, 'overdue' => 0, 'total' => 0];
        $today = now()->format('Y-m-d');

        foreach ($pokja->regulasis as $reg) {
            $stats[$reg->status]++;
            $stats['total']++;
            if ($reg->status !== 'Selesai' && $setting && $setting->target_date && $setting->target_date->format('Y-m-d') < $today) {
                $stats['overdue']++;
            }
        }

        $stats['pct'] = self::readinessPokja($pokja);
        return (object) $stats;
    }

    private static function readinessPokja($pokja)
    {
        $setting = Setting::first();
        $regs = $pokja->regulasis;
        if ($regs->count() === 0) return 0;

        if ($setting && $setting->calc_mode === 'bobot') {
            $sum = 0;
            foreach ($regs as $r) $sum += self::itemWeight($r);
            return round($sum / $regs->count());
        }

        $done = $regs->where('status', 'Selesai')->count();
        return round($done / $regs->count() * 100);
    }

    public static function predictLevel($pokjas, $setting)
    {
        $n = $setting && $setting->is_pendidikan ? 16 : 15;
        $scores = [];
        $pass = 0;

        foreach ($pokjas as $p) {
            if (!$setting->is_pendidikan && $p->code === 'PPK') continue;
            $score = self::epScore($p);
            $scores[$p->code] = $score;
            if ($score >= 80) $pass++;
        }

        $skp = $scores['SKP'] ?? 0;
        $prog = $scores['PROGNAS'] ?? 0;
        $progTarget = ($setting && $setting->prognas_full) ? 100 : 80;
        $progBlock = ($setting && $setting->prognas_full && $prog < 100);

        if ($pass === $n && $skp >= 80) {
            $level = 'Paripurna';
            $cls = 'lvl-par';
        } elseif ($pass >= 12 && $skp >= 80) {
            $level = 'Utama';
            $cls = 'lvl-uta';
        } elseif ($pass >= 8 && $skp >= 70) {
            $level = 'Madya';
            $cls = 'lvl-mad';
        } else {
            $level = 'Belum Terakreditasi';
            $cls = 'lvl-no';
        }

        if ($progBlock && $level !== 'Belum Terakreditasi') {
            $level = 'Belum Terakreditasi';
            $cls = 'lvl-no';
        }

        return (object) [
            'level' => $level,
            'cls' => $cls,
            'pass' => $pass,
            'n' => $n,
            'skp' => $skp,
            'prog' => $prog,
            'progTarget' => $progTarget,
            'progBlock' => $progBlock,
            'risk' => $n - $pass,
            'scores' => $scores,
        ];
    }

    public static function avgEpScore($pokjas, $setting)
    {
        $total = 0;
        $count = 0;
        foreach ($pokjas as $p) {
            if (!$setting->is_pendidikan && $p->code === 'PPK') continue;
            $total += self::epScore($p);
            $count++;
        }
        return $count > 0 ? round($total / $count) : 0;
    }
}
