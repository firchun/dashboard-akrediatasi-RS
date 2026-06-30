@extends('layouts.admin')

@section('title', $pokja->code . ' - ' . $pokja->name)

@php
  use App\Http\Controllers\DashboardController as DC;
  $JENIS = ["SK/Kebijakan","Pedoman","Panduan","SPO","Program","Lainnya"];
  $STATUS = ["Belum","Proses","Review","Selesai"];
  $NILAI = ["", "TL", "TS", "TT", "TDD"];
  $BUKTI = ["R","D","O","W","S"];
  $s = DC::pokjaStats($pokja);
  $eps = DC::epScore($pokja);
  $today = now()->format('Y-m-d');
@endphp

@section('content')
<main class="wrap pt-6">
  <div class="mb-4">
    <a href="{{ route('pokja.index') }}" class="text-xs font-semibold text-teal hover:text-teal-deep">← Kembali ke Pokja</a>
  </div>

  <div class="board p-5 mb-5 shadow-custom bg-card border border-line rounded-[14px]">
    <div class="grid grid-cols-[auto_1fr] gap-5 items-center">
      @php
        $colors = ['t-none' => '#94a3ad', 't-low' => '#cf4a39', 't-mid' => '#cf8616', 't-high' => '#2f74b5', 't-full' => '#157a52'];
        $tier = $s->total === 0 || $s->pct === 0 ? 't-none' : ($s->pct < 50 ? 't-low' : ($s->pct < 80 ? 't-mid' : ($s->pct < 100 ? 't-high' : 't-full')));
        $col = $colors[$tier];
      @endphp
      <div class="w-[50px] h-[50px]">{!! ringSVG($s->pct, 60, 7, $col, '#e6edee') !!}</div>
      <div>
        <h2 class="m-0 text-lg font-bold text-ink">{{ $pokja->code }} · {{ $pokja->name }}</h2>
        <div class="mt-1.5 text-xs text-slate-500 font-medium">
          Kesiapan: <b class="text-ink-soft">{{ $s->pct }}%</b> · 
          EP Skor: <b class="text-ink-soft">{{ $eps }}%</b> · 
          {{ $s->Selesai }}/{{ $s->total }} regulasi selesai
        </div>
      </div>
    </div>
  </div>

  <div class="flex items-baseline justify-between gap-4 mt-8 mb-3 flex-wrap">
    <h2 class="m-0 text-sm sm:text-base font-bold tracking-tight text-ink flex items-center gap-2">
      <span class="w-1 h-[18px] rounded-[2px] bg-teal"></span> Regulasi — {{ $pokja->code }}
    </h2>
  </div>

  <div class="board p-4 mb-5 shadow-custom bg-card border border-line rounded-[14px]">
    <table class="tbl">
      <thead><tr>
        <th>Nama Regulasi</th>
        <th class="w-[120px]">Jenis</th>
        <th class="w-[130px]">PIC</th>
        <th class="w-[140px]">Target</th>
        <th class="w-[120px]">Status</th>
        <th class="w-[140px]">Dokumen</th>
      </tr></thead>
      <tbody>
        @forelse($pokja->regulasis as $reg)
        <tr>
          <td class="text-sm font-semibold text-slate-800">{{ $reg->nama }}</td>
          <td>
            <span class="inline-block text-[10.5px] font-bold font-mono px-2 py-0.5 rounded-md bg-slate-100 border border-slate-200 text-slate-600">
              {{ $reg->jenis }}
            </span>
          </td>
          <td class="text-sm font-medium text-slate-600">{{ $reg->pic ?: '—' }}</td>
          <td class="text-sm font-medium text-slate-600 font-mono">
            {{ $reg->target ? $reg->target->format('d/m/Y') : '—' }}
            @if($reg->status !== 'Selesai' && $reg->target && $reg->target->format('Y-m-d') < $today)
              <span class="overdue-tag">LEWAT</span>
            @endif
          </td>
          <td>
            <span class="chip s-{{ strtolower($reg->status) }}">{{ $reg->status }}</span>
          </td>
          <td>
            @if(!empty($reg->link))
              <a href="{{ $reg->link }}" target="_blank" rel="noopener" class="text-xs font-semibold text-teal hover:text-teal-deep flex items-center gap-1">
                🔗 Buka Dokumen
              </a>
            @else
              <span class="text-xs text-slate-400 font-bold">—</span>
            @endif
          </td>
        </tr>
        @empty
        <tr class="empty-row"><td colspan="6" class="text-center text-slate-400 italic">Belum ada regulasi.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if($pokja->epItems->count() > 0)
  <div class="flex items-baseline justify-between gap-4 mt-8 mb-3 flex-wrap">
    <h2 class="m-0 text-sm sm:text-base font-bold tracking-tight text-ink flex items-center gap-2">
      <span class="w-1 h-[18px] rounded-[2px] bg-teal"></span> Rincian EP — {{ $pokja->code }}
    </h2>
  </div>
  <div class="board p-4 mb-5 shadow-custom bg-card border border-line rounded-[14px]">
    <table class="eptbl">
      <thead><tr>
        <th class="w-[104px]">Kode EP</th>
        <th>Uraian</th>
        <th class="w-[148px]">Bukti</th>
        <th class="w-[96px]">Nilai</th>
        <th class="w-[118px]">PIC</th>
      </tr></thead>
      <tbody>
        @foreach($pokja->epItems as $ei)
        <tr>
          <td class="font-mono font-bold text-xs text-ink pt-2">{{ $ei->kode }}</td>
          <td class="text-xs text-slate-700 leading-normal pt-2">{{ $ei->uraian }}</td>
          <td class="pt-2">
            @php $buktid = []; @endphp
            @foreach($BUKTI as $bk) @if($ei->{'bukti_'.strtolower($bk)}) @php $buktid[] = $bk; @endphp @endif @endforeach
            <span class="font-mono text-[11px] font-bold text-slate-700 bg-slate-100 border border-slate-200 px-1.5 py-0.5 rounded-md">
              {{ implode('', $buktid) ?: '—' }}
            </span>
          </td>
          <td class="pt-2">
            <span class="chip" style="background:{{ $ei->nilai === 'TL' ? 'var(--st-selesai-bg)' : ($ei->nilai === 'TS' ? 'var(--st-proses-bg)' : ($ei->nilai === 'TT' ? 'var(--danger-bg)' : ($ei->nilai === 'TDD' ? 'var(--st-belum-bg)' : '#eef3f4'))) }};color:{{ $ei->nilai === 'TL' ? 'var(--st-selesai)' : ($ei->nilai === 'TS' ? 'var(--st-proses)' : ($ei->nilai === 'TT' ? 'var(--danger)' : ($ei->nilai === 'TDD' ? 'var(--st-belum)' : 'var(--muted-2)'))) }}">
              {{ $ei->nilai ?: '—' }}
            </span>
          </td>
          <td class="text-xs font-semibold text-slate-600 pt-2">{{ $ei->pic ?: '—' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @endif

  <footer class="mt-8 pt-4 border-t border-line text-slate-400 text-xs">
    <span>Standar acuan: STARKES Kemenkes (KMK HK.01.07/MENKES/1596/2024)</span>
  </footer>
</main>
@endsection
