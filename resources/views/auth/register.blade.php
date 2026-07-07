@extends('layouts.admin')

@section('title', 'Daftar - Dashboard Akreditasi RS')

@section('content')
  <div class="max-w-[480px] mx-auto my-10 px-5">
    <div class="bg-card border border-line rounded-[14px] p-7 shadow-custom">
      <h2 class="m-0 mb-1 text-lg font-bold text-ink text-center">Daftar Akun Baru</h2>
      <p class="m-0 mb-5 text-xs text-slate-400 text-center">Isi data di bawah untuk membuat akun</p>

      @if($errors->any())
        <div class="alert alert-error">
          @foreach($errors->all() as $err) {{ $err }}<br /> @endforeach
        </div>
      @endif

      <form method="POST" action="{{ route('register.post') }}">
        @csrf

        <div class="mb-3.5">
          <label class="block text-xs font-bold text-slate-500 mb-1.5">Nama Lengkap</label>
          <input type="text" name="name" value="{{ old('name') }}" required
            class="w-full px-3 py-2.5 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12"
            placeholder="Nama lengkap" />
        </div>

        <div class="mb-3.5">
          <label class="block text-xs font-bold text-slate-500 mb-1.5">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" required
            class="w-full px-3 py-2.5 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12"
            placeholder="nama@email.com" />
        </div>

        <div class="mb-3.5">
          <label class="block text-xs font-bold text-slate-500 mb-1.5">Password</label>
          <input type="password" name="password" required
            class="w-full px-3 py-2.5 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12"
            placeholder="Minimal 8 karakter" />
        </div>

        <div class="mb-3.5">
          <label class="block text-xs font-bold text-slate-500 mb-1.5">Konfirmasi Password</label>
          <input type="password" name="password_confirmation" required
            class="w-full px-3 py-2.5 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12"
            placeholder="Ulangi password" />
        </div>

        <div class="mb-4">
          <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Pilih Pokja</label>
          <select name="pokja_id" required
            class="w-full px-3 py-2.5 border border-line rounded-lg text-sm bg-white font-medium focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
            <option value="">— Pilih Pokja —</option>
            @foreach($pokjas as $p)
              <option value="{{ $p->id }}" {{ old('pokja_id') == $p->id ? 'selected' : '' }}>[{{ $p->code }}] {{ $p->name }} —
                {{ $p->group }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-5">
          <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wider">Role</label>
          <select name="role" required
            class="w-full px-3 py-2.5 border border-line rounded-lg text-sm bg-white font-medium focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
            <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>User (Anggota Pokja)</option>
            <option value="ketua_tim" {{ old('role') === 'ketua_tim' ? 'selected' : '' }}>Ketua Tim</option>
            <option value="it" {{ old('role') === 'it' ? 'selected' : '' }}>IT</option>
            <option value="verifikator" {{ old('role') === 'verifikator' ? 'selected' : '' }}>Verifikator (Verifikasi Status)</option>
            <option value="regulasi" {{ old('role') === 'regulasi' ? 'selected' : '' }}>Regulasi</option>
          </select>
        </div>

        <button type="submit" class="btn primary w-full justify-center py-2.5">Daftar</button>
      </form>

      <div class="mt-4.5 pt-4 border-t border-line-soft text-center">
        <p class="text-xs text-slate-400 m-0">Sudah punya akun?</p>
        <a href="{{ route('login') }}"
          class="inline-block mt-1.5 text-sm font-semibold text-teal hover:text-teal-deep">Masuk</a>
      </div>
    </div>
  </div>
@endsection