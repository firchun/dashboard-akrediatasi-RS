@extends('layouts.admin')

@section('title', 'Login - Dashboard Akreditasi RS')

@section('content')
<div class="max-w-[420px] mx-auto my-16 px-5">
  <div class="bg-card border border-line rounded-[14px] p-8 shadow-custom">
    <h2 class="margin-0 mb-1.5 text-lg font-bold text-ink text-center">Masuk</h2>
    <p class="margin-0 mb-6 text-xs text-slate-400 text-center">Masukkan email & password atau gunakan Google</p>

    @if(session('error'))
      <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('login.post') }}">
      @csrf
      <div class="mb-3.5">
        <label class="block text-xs font-bold text-slate-500 mb-1.5">Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required
          class="w-full px-3 py-2.5 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12"
          placeholder="nama@email.com" />
      </div>
      <div class="mb-1.5">
        <label class="block text-xs font-bold text-slate-500 mb-1.5">Password</label>
        <input type="password" name="password" required
          class="w-full px-3 py-2.5 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12"
          placeholder="••••••••" />
      </div>
      @error('email') <div class="text-danger text-xs font-semibold mb-2">{{ $message }}</div> @enderror

      <button type="submit" class="btn primary w-full justify-center py-2.5 mt-2.5">Masuk</button>
    </form>

    <div class="flex items-center gap-3 my-4.5">
      <hr class="flex-1 border-none border-t border-line-soft" />
      <span class="text-xs text-slate-400">atau</span>
      <hr class="flex-1 border-none border-t border-line-soft" />
    </div>

    <a href="{{ route('auth.google') }}"
       class="flex items-center justify-center gap-2.5 w-full py-2.5 px-4 rounded-lg border border-line bg-white text-xs font-semibold text-slate-700 hover:border-teal transition duration-150">
      <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
      Masuk dengan Google
    </a>

    <div class="mt-5 pt-4.5 border-t border-line-soft text-center">
      <p class="text-xs text-slate-400 m-0">Belum punya akun?</p>
      <a href="{{ route('register') }}" class="inline-block mt-2 text-sm font-semibold text-teal hover:text-teal-deep">Daftar</a>
    </div>
  </div>
</div>
@endsection
