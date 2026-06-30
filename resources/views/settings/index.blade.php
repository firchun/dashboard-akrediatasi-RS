@extends('layouts.admin')

@section('title', 'Settings - Dashboard Akreditasi RS')

@section('content')
<main class="wrap pt-6">
  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="flex items-baseline justify-between gap-4 mt-8 mb-3 flex-wrap">
    <h2 class="m-0 text-sm sm:text-base font-bold tracking-tight text-ink flex items-center gap-2">
      <span class="w-1 h-[18px] rounded-[2px] bg-teal"></span> Pengaturan Umum
    </h2>
  </div>

  <div class="board p-5 mb-5 shadow-custom bg-card border border-line rounded-[14px]">
    <form method="POST" action="{{ route('settings.update') }}">
      @csrf
      @method('PUT')

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4 max-w-[600px]">
        <div class="mb-3.5">
          <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Nama Rumah Sakit</label>
          <input type="text" name="hospital_name" value="{{ $setting->hospital_name }}" class="w-full px-3 py-2 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" required/>
        </div>

        <div class="mb-3.5">
          <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Target Survei</label>
          <input type="date" name="target_date" value="{{ $setting->target_date ? $setting->target_date->format('Y-m-d') : '' }}" class="w-full px-3 py-2 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12"/>
        </div>
      </div>

      <div class="flex gap-5 flex-wrap mb-4 items-center">
        <label class="chk cursor-pointer flex items-center gap-2 font-semibold text-xs text-ink bg-white border border-line rounded-lg px-3 py-2">
          <input type="checkbox" name="is_pendidikan" value="1" {{ $setting->is_pendidikan ? 'checked' : '' }} class="w-4 h-4 accent-teal"/> RS Pendidikan (16 bab)
        </label>
        <label class="chk cursor-pointer flex items-center gap-2 font-semibold text-xs text-ink bg-white border border-line rounded-lg px-3 py-2">
          <input type="checkbox" name="prognas_full" value="1" {{ $setting->prognas_full ? 'checked' : '' }} class="w-4 h-4 accent-teal"/> PROGNAS wajib 100%
        </label>
        <label class="inline-flex items-center gap-2 text-xs font-semibold text-ink">
          Mode hitung:
          <select name="calc_mode" class="px-2.5 py-1.5 border border-line rounded-lg text-xs bg-white focus:outline-none focus:border-teal">
            <option value="bobot" {{ $setting->calc_mode === 'bobot' ? 'selected' : '' }}>Bobot progres</option>
            <option value="selesai" {{ $setting->calc_mode === 'selesai' ? 'selected' : '' }}>Selesai saja</option>
          </select>
        </label>
      </div>

      <button type="submit" class="btn primary">Simpan Pengaturan</button>
    </form>
  </div>

  <div class="flex items-baseline justify-between gap-4 mt-8 mb-3 flex-wrap">
    <h2 class="m-0 text-sm sm:text-base font-bold tracking-tight text-ink flex items-center gap-2">
      <span class="w-1 h-[18px] rounded-[2px] bg-teal"></span> Daftar Pokja
    </h2>
  </div>

  <div class="board p-4 mb-5 shadow-custom bg-card border border-line rounded-[14px]">
    <table class="tbl">
      <thead><tr>
        <th class="w-20">Kode</th>
        <th>Nama Pokja</th>
        <th class="w-[140px]">Kelompok</th>
        <th class="w-[100px]">Total EP</th>
        <th class="w-[100px]">Users</th>
      </tr></thead>
      <tbody>
        @foreach($pokjas as $p)
        <tr>
          <td><span class="font-mono font-bold text-ink">{{ $p->code }}</span></td>
          <td class="text-sm font-medium text-slate-800">{{ $p->name }}</td>
          <td><span class="text-xs text-slate-400">{{ $p->group }}</span></td>
          <td class="text-sm font-medium text-slate-700 font-mono">{{ $p->ep_total }}</td>
          <td class="text-sm font-medium text-slate-700 font-mono">{{ $p->users_count ?? 0 }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="mt-5">
    <a href="{{ route('settings.users') }}" class="btn primary">Kelola User</a>
  </div>

  <footer class="mt-6 pt-4 border-t border-line text-slate-400 text-xs">
    <span>Standar acuan: STARKES Kemenkes (KMK HK.01.07/MENKES/1596/2024)</span>
  </footer>
</main>
@endsection
