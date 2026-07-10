@extends('layouts.admin')

@section('title', 'Dashboard Monitoring Regulasi Akreditasi')

@php
  use App\Http\Controllers\DashboardController as DC;
  $pred = DC::predictLevel($pokjas, $setting);
  $avg = DC::avgEpScore($pokjas, $setting);
  $today = now()->format('Y-m-d');
  $groups = ['MANAJEMEN', 'PASIEN', 'SKP', 'PROGNAS'];
  $groupLabels = [
    'MANAJEMEN' => 'A · Kelompok Manajemen Rumah Sakit',
    'PASIEN' => 'B · Pelayanan Berfokus pada Pasien',
    'SKP' => 'C · Sasaran Keselamatan Pasien',
    'PROGNAS' => 'D · Program Nasional',
  ];
  $JENIS = ["SK/Kebijakan", "Pedoman", "Panduan", "SPO", "Program", "Lainnya"];
  $STATUS = ["Belum", "Proses", "Review", "Selesai"];
@endphp

@section('content')
  <header
    class="bg-gradient-to-br from-ink via-ink-light to-ink-soft text-[#eaf4f5] relative overflow-hidden after:absolute after:inset-0 after:bg-[radial-gradient(620px_320px_at_88%_-20%,rgba(12,124,140,0.38),transparent_60%),radial-gradient(420px_260px_at_10%_120%,rgba(12,124,140,0.20),transparent_60%)] after:pointer-events-none">
    <div
      class="mx-auto max-w-[1240px] px-5 py-6 sm:py-7 relative z-10 grid grid-cols-1 md:grid-cols-[1fr_auto] gap-7 items-center">
      <div>
        <span
          class="inline-flex items-center gap-2 font-mono text-[10px] tracking-widest uppercase text-[#8fd0d8] bg-teal/16 border border-[#8fd0d8]/28 px-2.5 py-1 rounded-full">◇
          STARKES · KMK 1596/2024 · 16 Pokja</span>
        <h1 class="my-3 text-2xl sm:text-3xl md:text-[32px] font-extrabold tracking-tight leading-none text-white">
          Dashboard Monitoring Regulasi Akreditasi</h1>
        <div class="flex items-center gap-2.5 flex-wrap mt-2">
          <span
            class="text-sm font-semibold px-2.5 py-1 text-white bg-white/10 rounded-lg border border-white/10">{{ $setting->hospital_name ?? 'RSUD Merauke' }}</span>
        </div>
        <div class="flex gap-2.5 flex-wrap mt-3.5 items-center">
          <div class="flex items-center gap-2 bg-white/7 border border-white/14 px-3 py-1.5 rounded-lg text-xs">
            <label class="text-[#9fcbd2] font-semibold">Target survei</label>
            <span
              class="text-white font-bold">{{ $setting->target_date ? \Carbon\Carbon::parse($setting->target_date)->format('d M Y') : 'Belum diatur' }}</span>
          </div>
          <div class="flex items-center gap-2 bg-white/7 border border-white/14 px-3 py-1.5 rounded-lg text-xs">
            <span class="font-mono font-bold text-white" id="countdown">
              @php
                if ($setting->target_date) {
                  $d = ceil((strtotime($setting->target_date) - strtotime(now()->format('Y-m-d'))) / 86400);
                  if ($d > 0)
                    echo 'H-' . $d;
                  elseif ($d === 0)
                    echo 'Hari ini';
                  else
                    echo abs($d) . ' hari terlewat';
                } else {
                  echo 'Atur target survei →';
                }
              @endphp
            </span>
          </div>
        </div>
      </div>
      <div class="flex flex-col items-center gap-2">
        <div class="relative w-[150px] h-[150px]">
          <svg width="150" height="150" viewBox="0 0 150 150" id="heroRing"></svg>
          <div class="absolute inset-0 flex flex-col items-center justify-center">
            <b class="text-[38px] font-extrabold leading-none text-white font-mono"
              id="heroPct">{{ $globalStats->pct }}%</b>
            <span class="text-[11px] text-[#9fcbd2] tracking-widest uppercase mt-0.5">Kesiapan</span>
          </div>
        </div>
        <div class="text-xs text-[#bfe0e4] text-center" id="heroSub">{{ $globalStats->Selesai }} dari
          {{ $globalStats->total }} selesai · {{ $setting->calc_mode === 'bobot' ? 'bobot progres' : 'hitung selesai' }}
        </div>
      </div>
    </div>
  </header>

  <main class="mx-auto max-w-[1240px] px-5 pb-16" id="app">
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mt-[-22px] relative z-20" id="kpis">
      <div class="bg-card border border-line rounded-[14px] p-3.5 shadow-custom">
        <div class="text-[10px] sm:text-xs tracking-wider uppercase text-slate-500 font-bold flex items-center gap-2">
          Total Regulasi</div>
        <div class="font-mono text-2xl sm:text-3xl font-bold mt-1.5 leading-none text-ink">{{ $globalStats->total }}</div>
        <div class="text-xs text-slate-400 mt-0.5">16 pokja</div>
      </div>
      <div class="bg-card border border-line rounded-[14px] p-3.5 shadow-custom">
        <div class="text-[10px] sm:text-xs tracking-wider uppercase text-slate-500 font-bold flex items-center gap-2">
          <span class="w-2 h-2 rounded-full inline-block bg-st-selesai"></span>Selesai
        </div>
        <div class="font-mono text-2xl sm:text-3xl font-bold mt-1.5 leading-none text-st-selesai">
          {{ $globalStats->Selesai }}</div>
        <div class="text-xs text-slate-400 mt-0.5">
          {{ $setting->calc_mode === 'bobot' ? 'kesiapan tertimbang ' . $globalStats->pct . '%' : $globalStats->pct . '% dari total' }}
        </div>
      </div>
      <div class="bg-card border border-line rounded-[14px] p-3.5 shadow-custom">
        <div class="text-[10px] sm:text-xs tracking-wider uppercase text-slate-500 font-bold flex items-center gap-2">
          <span class="w-2 h-2 rounded-full inline-block bg-st-proses"></span>Proses + Review
        </div>
        <div class="font-mono text-2xl sm:text-3xl font-bold mt-1.5 leading-none text-st-proses">
          {{ $globalStats->Proses + $globalStats->Review }}</div>
        <div class="text-xs text-slate-400 mt-0.5">sedang dikerjakan</div>
      </div>
      <div class="bg-card border border-line rounded-[14px] p-3.5 shadow-custom">
        <div class="text-[10px] sm:text-xs tracking-wider uppercase text-slate-500 font-bold flex items-center gap-2">
          <span class="w-2 h-2 rounded-full inline-block bg-st-belum"></span>Belum Mulai
        </div>
        <div class="font-mono text-2xl sm:text-3xl font-bold mt-1.5 leading-none text-st-belum">{{ $globalStats->Belum }}
        </div>
        <div class="text-xs text-slate-400 mt-0.5">perlu ditindaklanjuti</div>
      </div>
      <div class="bg-card border border-line rounded-[14px] p-3.5 shadow-custom">
        <div class="text-[10px] sm:text-xs tracking-wider uppercase text-slate-500 font-bold flex items-center gap-2">
          <span class="w-2 h-2 rounded-full inline-block bg-danger"></span>Terlewat Target
        </div>
        <div class="font-mono text-2xl sm:text-3xl font-bold mt-1.5 leading-none text-danger">{{ $globalStats->overdue }}
        </div>
        <div class="text-xs text-slate-400 mt-0.5">lewat tenggat & belum selesai</div>
      </div>
    </div>

    <div class="flex items-baseline justify-between gap-4 mt-8 mb-3 flex-wrap">
      <h2 class="m-0 text-sm sm:text-base font-bold tracking-tight text-ink flex items-center gap-2">
        <span class="w-1 h-[18px] rounded-[2px] bg-teal"></span> Prediksi Skor EP &amp; Kelulusan <span
          class="font-semibold text-slate-400 text-xs">· simulasi</span>
      </h2>
      <span class="text-xs text-slate-500">Berdasarkan self-assessment Elemen Penilaian (TL=10 · TS=5 · TT=0 · TDD
        dikecualikan)</span>
    </div>

    <div class="bg-gradient-to-br from-white to-[#f6fafa] border border-line rounded-[14px] shadow-custom p-4 mt-1.5">
      <div class="grid grid-cols-1 md:grid-cols-[auto_1fr] gap-4 items-center">
        <div
          class="flex flex-col items-center justify-center min-w-[170px] p-3.5 rounded-[13px] text-white text-center {{ $pred->cls === 'lvl-par' ? 'bg-gradient-to-br from-[#147a52] to-[#0e5e3f]' : ($pred->cls === 'lvl-uta' ? 'bg-gradient-to-br from-[#2363a6] to-[#184a80]' : ($pred->cls === 'lvl-mad' ? 'bg-gradient-to-br from-[#bd770d] to-[#9a5f08]' : 'bg-gradient-to-br from-[#9aa6ad] to-[#6c7980]')) }}">
          <span class="text-[10px] tracking-wider uppercase opacity-90">Prediksi Level</span>
          <span class="text-[23px] font-extrabold mt-0.5 leading-none">{{ $pred->level }}</span>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2.5">
          <div class="bg-white border border-line rounded-xl p-2.5">
            <div class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Bab memenuhi target</div>
            <div class="font-mono text-2xl font-bold mt-0.5 text-ink">{{ $pred->pass }}<small
                class="text-xs text-slate-400 font-semibold">/{{ $pred->n }}</small></div>
          </div>
          <div class="bg-white border border-line rounded-xl p-2.5 @if($pred->skp < 80) border-danger/40 @endif">
            <div class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Skor SKP</div>
            <div class="font-mono text-2xl font-bold mt-0.5 @if($pred->skp >= 80) text-ink @else text-danger @endif">
              {{ $pred->skp }}%</div>
          </div>
          <div
            class="bg-white border border-line rounded-xl p-2.5 @if($pred->prog < ($pred->progTarget ?? 80)) border-danger/40 @endif">
            <div class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Skor PROGNAS</div>
            <div
              class="font-mono text-2xl font-bold mt-0.5 @if($pred->prog >= ($pred->progTarget ?? 80)) text-ink @else text-danger @endif">
              {{ $pred->prog }}%</div>
          </div>
          <div class="bg-white border border-line rounded-xl p-2.5">
            <div class="text-[10px] uppercase tracking-wider text-slate-500 font-bold">Rata-rata skor EP</div>
            <div class="font-mono text-2xl font-bold mt-0.5 text-ink">{{ $avg }}%</div>
          </div>
        </div>
      </div>
      @if($pred->progBlock)
        <div class="mt-3 bg-danger-bg border border-[#f0c9c2] text-[#8f2c20] rounded-lg p-2.5 text-xs font-semibold">
          ⚠ PROGNAS baru {{ $pred->prog }}% — dengan aturan ini, kelulusan tertahan sampai PROGNAS mencapai <b
            class="font-mono">100%</b>.
        </div>
      @endif
      <div class="flex flex-wrap gap-2.5 items-center justify-between mt-3.5 border-t border-line-soft pt-3">
        <div class="text-xs text-slate-500 max-w-[640px]">
          Aturan: <b class="text-ink-soft">Paripurna</b> = semua {{ $pred->n }} bab ≥80%. <b
            class="text-ink-soft">Utama</b> = 12–{{ $pred->n - 1 }} bab ≥80% &amp; SKP ≥80%. <b
            class="text-ink-soft">Madya</b> = 8–11 bab ≥80% &amp; SKP ≥70%. Skor EP berasal dari self-assessment di tiap
          pokja.
        </div>
      </div>
    </div>

    <div x-data="{ viewMode: 'card' }" class="mt-8">
      <div class="flex items-center justify-between gap-4 mb-3 flex-wrap">
        <h2 class="m-0 text-sm sm:text-base font-bold tracking-tight text-ink flex items-center gap-2">
          <span class="w-1 h-[18px] rounded-[2px] bg-teal"></span> Panel Kesiapan per Pokja
        </h2>
        <div class="flex items-center gap-3">
          <span class="text-xs text-slate-500 hidden sm:inline">Klik sel untuk melihat detail</span>
          <div class="bg-slate-100 p-0.5 rounded-lg flex items-center text-xs font-semibold border border-line-soft">
            <button @click="viewMode = 'card'" :class="viewMode === 'card' ? 'bg-white shadow-sm text-teal-deep' : 'text-slate-400 hover:text-slate-600'" class="px-2.5 py-1.5 rounded-md transition-colors flex items-center gap-1.5">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7"></rect><rect x="14" y="3" width="7" height="7"></rect><rect x="14" y="14" width="7" height="7"></rect><rect x="3" y="14" width="7" height="7"></rect></svg> Card
            </button>
            <button @click="viewMode = 'list'" :class="viewMode === 'list' ? 'bg-white shadow-sm text-teal-deep' : 'text-slate-400 hover:text-slate-600'" class="px-2.5 py-1.5 rounded-md transition-colors flex items-center gap-1.5">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="8" y1="6" x2="21" y2="6"></line><line x1="8" y1="12" x2="21" y2="12"></line><line x1="8" y1="18" x2="21" y2="18"></line><line x1="3" y1="6" x2="3.01" y2="6"></line><line x1="3" y1="12" x2="3.01" y2="12"></line><line x1="3" y1="18" x2="3.01" y2="18"></line></svg> List
            </button>
          </div>
        </div>
      </div>

      <div class="bg-card border border-line rounded-[14px] p-4 shadow-custom">
      @foreach($groups as $gk)
        @php $list = $pokjas->filter(function ($p) use ($gk) {
        return $p->group === $gk; }); @endphp
        @if($list->count() > 0)
          <div class="mb-5 last:mb-2">
            <h3 class="my-1.5 mx-0.5 text-[10px] tracking-wider uppercase text-slate-400 font-bold">{{ $groupLabels[$gk] }}
            </h3>
            <div :class="viewMode === 'card' ? 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3' : 'grid grid-cols-1 lg:grid-cols-2 gap-3'">
              @foreach($list as $p)
                @php
                  $s = DC::pokjaStats($p);
                  $colors = ['t-none' => '#94a3ad', 't-low' => '#cf4a39', 't-mid' => '#cf8616', 't-high' => '#2f74b5', 't-full' => '#157a52'];
                  $tier = $s->total === 0 || $s->pct === 0 ? 't-none' : ($s->pct < 50 ? 't-low' : ($s->pct < 80 ? 't-mid' : ($s->pct < 100 ? 't-high' : 't-full')));
                  $col = $colors[$tier];
                @endphp
                <a href="{{ route('pokja.show', $p->code) }}"
                  :class="viewMode === 'card' ? 'block' : 'flex flex-col sm:flex-row sm:items-center gap-3 sm:gap-6'"
                  class="bg-white text-left relative overflow-hidden transition duration-150 ease-in-out hover:-translate-y-0.5 hover:shadow-lg cursor-pointer w-full"
                  style="border: 2px solid {{ $col }}; border-radius: 16px; padding: 1rem;">
                  
                  <div :class="viewMode === 'card' ? 'pb-2 mb-3' : 'min-w-[80px]'" :style="viewMode === 'card' ? 'border-bottom: 2px solid {{ $col }} !important;' : ''">
                    <div class="font-bold text-base tracking-wide" style="color: #1f2937;">{{ $p->code }}</div>
                  </div>
                  
                  <div :class="viewMode === 'card' ? 'block' : 'flex-1 grid grid-cols-2 gap-4'">
                    <!-- Regulasi Section -->
                    <div class="flex items-center gap-3" :class="viewMode === 'card' ? 'mb-3 pb-3 border-b border-line' : ''">
                      <!-- Circular Progress -->
                      <div class="relative flex-shrink-0" style="width: 50px; height: 50px;">
                        <svg class="transform -rotate-90" style="width: 50px; height: 50px;">
                          <circle cx="25" cy="25" r="21" stroke="currentColor" stroke-width="3" fill="transparent" class="text-slate-100" />
                          <circle cx="25" cy="25" r="21" stroke="currentColor" stroke-width="3" fill="transparent" 
                            style="color: {{ $col }}; stroke-dasharray: 131.9; stroke-dashoffset: {{ 131.9 - (131.9 * $s->pct / 100) }}; transition: stroke-dashoffset 0.5s ease;" stroke-linecap="round" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                          <span class="font-bold" style="font-size: 13px; color: black;">{{ $s->pct }}%</span>
                        </div>
                      </div>
                      
                      <!-- Text info -->
                      <div class="flex flex-col justify-center">
                        <span class="font-bold text-sm leading-tight" style="color: #1f2937;">Regulasi</span>
                        <span class="font-bold text-sm leading-tight mt-1" style="color: #1f2937;">{{ $s->Selesai }}/{{ $s->total }} selesai</span>
                        @if($s->overdue || $s->Proses + $s->Review > 0)
                        <div class="mt-1 flex gap-1 font-semibold" style="font-size: 10px; color: #6b7280;">
                          @if($s->Proses + $s->Review > 0) <span class="text-amber-600">{{ $s->Proses + $s->Review }} prs</span> @endif
                          @if($s->overdue) <span class="text-danger">({{ $s->overdue }} telat)</span> @endif
                        </div>
                        @endif
                      </div>
                    </div>

                    <!-- Skor EP Section -->
                    <div class="flex items-center gap-3">
                      <!-- Circular Progress -->
                      <div class="relative flex-shrink-0" style="width: 50px; height: 50px;">
                        <svg class="transform -rotate-90" style="width: 50px; height: 50px;">
                          <circle cx="25" cy="25" r="21" stroke="currentColor" stroke-width="3" fill="transparent" class="text-slate-100" />
                          <circle cx="25" cy="25" r="21" stroke="currentColor" stroke-width="3" fill="transparent" 
                            style="color: {{ $col }}; stroke-dasharray: 131.9; stroke-dashoffset: {{ 131.9 - (131.9 * $s->ep_score / 100) }}; transition: stroke-dashoffset 0.5s ease;" stroke-linecap="round" />
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                          <span class="font-bold" style="font-size: 13px; color: black;">{{ $s->ep_score }}%</span>
                        </div>
                      </div>
                      
                      <!-- Text info -->
                      <div class="flex flex-col justify-center w-full">
                        <span class="font-bold text-sm leading-tight" style="color: #1f2937;">Elemen Penilaian</span>
                        <span class="font-bold text-sm leading-tight mt-1" style="color: #1f2937;">{{ $s->ep->TL + $s->ep->TS }}/{{ $s->ep->total }} selesai</span>
                        <div class="flex flex-wrap items-center gap-1 mt-1.5" style="font-size: 9px;">
                          <span class="px-1.5 py-0.5 rounded font-bold" style="background-color: #a7f3d0; color: #065f46;">TL:{{ $s->ep->TL }}</span>
                          <span class="px-1.5 py-0.5 rounded font-bold" style="background-color: #fde047; color: #854d0e;">TS:{{ $s->ep->TS }}</span>
                          <span class="px-1.5 py-0.5 rounded font-bold" style="background-color: #fca5a5; color: #991b1b;">TT:{{ $s->ep->TT }}</span>
                          <span class="px-1.5 py-0.5 rounded font-bold" style="background-color: #e5e7eb; color: #374151;">TDD:{{ $s->ep->TDD }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </a>
              @endforeach
            </div>
          </div>
        @endif
      @endforeach
    </div>
  </div>

    <div class="flex flex-wrap gap-3.5 mx-0.5 mt-3.5 mb-1 text-xs text-slate-500">
      <span class="inline-flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-[3px] inline-block bg-t-none"></i>
        Belum mulai</span>
      <span class="inline-flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-[3px] inline-block bg-t-low"></i>
        Tertinggal &lt;50%</span>
      <span class="inline-flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-[3px] inline-block bg-t-mid"></i>
        Berjalan 50–79%</span>
      <span class="inline-flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-[3px] inline-block bg-t-high"></i>
        Hampir 80–99%</span>
      <span class="inline-flex items-center gap-1.5"><i class="w-2.5 h-2.5 rounded-[3px] inline-block bg-t-full"></i>
        Lengkap 100%</span>
    </div>

    <footer class="mt-8 pt-4 border-t border-line text-slate-400 text-xs flex justify-between gap-3.5 flex-wrap">
      <span>Standar acuan: STARKES Kemenkes (KMK HK.01.07/MENKES/1596/2024) · 16 BAB/Pokja | BY Firmansyah diana</span>
    </footer>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var pct = {{ $globalStats->pct }};
      var ring = document.getElementById('heroRing');
      if (ring) ring.innerHTML = ringInner(pct, 150, 13, '#4fd1c5', 'rgba(255,255,255,.14)');
    });
  </script>
@endsection