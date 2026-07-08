<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>@yield('title', 'Dashboard Akreditasi RS')</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Spline+Sans+Mono:wght@500;600;700&display=swap" rel="stylesheet">
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
@vite(['resources/css/app.css'])
@stack('styles')
</head>
<body x-data="{ profileModal: false, profileSaving: false, profileMsg: '' }">

<nav class="sticky top-0 z-50 bg-ink text-[#eaf4f5]">
  <div class="mx-auto max-w-[1240px] px-5 flex items-center gap-2 min-h-[52px]">
    <div class="font-extrabold text-sm tracking-tight text-white mr-5 whitespace-nowrap">◇ Akreditasi RS</div>
    <div class="flex gap-1 flex-1">
      <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-[#aacdd5] hover:bg-white/8 hover:text-white transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-teal/25 text-white' : '' }}">Dashboard</a>
      @auth
      <a href="{{ route('pokja.index') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-[#aacdd5] hover:bg-white/8 hover:text-white transition duration-150 {{ request()->routeIs('pokja.*') ? 'bg-teal/25 text-white' : '' }}">Pokja</a>
        @if(in_array(auth()->user()->role, ["it", "ketua_tim", "regulasi"]))
        <a href="{{ route("file.manager") }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-[#aacdd5] hover:bg-white/8 hover:text-white transition duration-150 {{ request()->routeIs("file.*") ? "bg-teal/25 text-white" : "" }}">File</a>
        @endif
        @if(auth()->user()->hasSettingsAccess())
        <a href="{{ route('settings.index') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-[#aacdd5] hover:bg-white/8 hover:text-white transition duration-150 {{ request()->routeIs('settings.*') ? 'bg-teal/25 text-white' : '' }}">Settings</a>
        @endif
      @endauth
    </div>
    <div class="flex items-center gap-2.5 ml-auto">
      @auth
        @if(auth()->user()->avatar)
        <img class="w-[30px] h-[30px] rounded-full object-cover" src="{{ auth()->user()->avatar }}" alt="" />
        @endif
        <button type="button" @click="profileModal = true; profileMsg = ''" class="text-xs text-[#bfe0e4] hidden sm:inline hover:text-white cursor-pointer underline decoration-dotted underline-offset-2 transition">{{ auth()->user()->name }}</button>
        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded text-white uppercase {{ auth()->user()->role === 'it' ? 'bg-[#2363a6]' : (auth()->user()->role === 'ketua_tim' ? 'bg-[#bd770d]' : (auth()->user()->role === 'verifikator' ? 'bg-[#7a5bbd]' : (auth()->user()->role === 'regulasi' ? 'bg-[#0d9488]' : 'bg-[#647b85]'))) }}">
          {{ str_replace('_', ' ', auth()->user()->role) }}
        </span>
        <form method="POST" action="{{ route('logout') }}" class="inline">
          @csrf
          <button class="px-3 py-1.5 rounded-lg bg-white/8 text-[#bfe0e4] text-xs font-semibold border border-white/12 hover:bg-white/15 hover:text-white" type="submit">Logout</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="px-3 py-1.5 rounded-lg bg-white/8 text-[#bfe0e4] text-xs font-semibold border border-white/12 hover:bg-white/15 hover:text-white">Login</a>
      @endauth
    </div>
  </div>
</nav>

@auth
<!-- Profile Modal -->
<div class="modal-bg" :class="profileModal ? 'show' : ''" @click.self="profileModal = false" style="z-index:100">
  <div class="modal max-w-[440px]">
    <div class="modal-h">
      <div>
        <h3>Profil Saya</h3>
        <p>{{ auth()->user()->name }} · {{ auth()->user()->email }}</p>
      </div>
      <button type="button" class="modal-close" @click="profileModal = false">×</button>
    </div>
    <form @submit.prevent="
      profileSaving = true; profileMsg = '';
      fetch(window.baseUrl + '/profile', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
          pokja_id: $refs.profPokja.value || null,
          password: $refs.profPass.value || '',
          password_confirmation: $refs.profPassC.value || ''
        })
      }).then(r => r.json()).then(d => {
        if (d.success) { profileMsg = '✓ ' + d.message; $refs.profPass.value = ''; $refs.profPassC.value = ''; }
        else { profileMsg = '✗ ' + (d.message || JSON.stringify(d.errors || 'Gagal')); }
      }).catch(() => { profileMsg = '✗ Terjadi kesalahan jaringan.'; })
      .finally(() => { profileSaving = false; })
    ">
      <div class="modal-b flex flex-col gap-4 px-5 py-4">
        <div>
          <label class="block text-[11px] font-bold text-slate-400 mb-1">Pokja</label>
          <select x-ref="profPokja" class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
            <option value="">— Tidak ada —</option>
            @foreach(\App\Models\Pokja::orderBy('group')->orderBy('code')->get() as $p)
              <option value="{{ $p->id }}" {{ auth()->user()->pokja_id == $p->id ? 'selected' : '' }}>{{ $p->code }} — {{ $p->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="border-t border-line-soft pt-3">
          <label class="block text-[11px] font-bold text-slate-400 mb-1">Password Baru <span class="font-normal">(kosongkan jika tidak ingin mengubah)</span></label>
          <input type="password" x-ref="profPass" placeholder="Minimal 6 karakter" autocomplete="new-password" class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
        </div>

        <div>
          <label class="block text-[11px] font-bold text-slate-400 mb-1">Konfirmasi Password</label>
          <input type="password" x-ref="profPassC" placeholder="Ulangi password baru" autocomplete="new-password" class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
        </div>

        <div x-show="profileMsg" x-text="profileMsg" class="text-xs font-bold px-3 py-2 rounded-lg" :class="profileMsg.startsWith('✓') ? 'bg-teal-50 text-teal-700' : 'bg-red-50 text-red-600'"></div>
      </div>
      <div class="modal-f">
        <button type="button" class="btn ghost" @click="profileModal = false">Tutup</button>
        <button type="submit" class="btn primary" :disabled="profileSaving" x-text="profileSaving ? 'Menyimpan...' : 'Simpan'"></button>
      </div>
    </form>
  </div>
</div>
@endauth

@yield('content')

<script>
window.baseUrl = '{{ url('/') }}';
function esc(s){ return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function todayISO(){ return new Date().toISOString().slice(0,10); }
function normalizeUrl(u){ u=String(u||'').trim(); if(!u) return ''; return /^https?:\/\//i.test(u)?u:'https://'+u; }

function ringSVG(p,size,stroke,color,trackColor){
  var r=(size-stroke)/2, c=2*Math.PI*r, off=c*(1-p/100), cx=size/2;
  return '<svg width="'+size+'" height="'+size+'" viewBox="0 0 '+size+' '+size+'">'
    +'<circle cx="'+cx+'" cy="'+cx+'" r="'+r+'" fill="none" stroke="'+trackColor+'" stroke-width="'+stroke+'"/>'
    +'<circle cx="'+cx+'" cy="'+cx+'" r="'+r+'" fill="none" stroke="'+color+'" stroke-width="'+stroke+'" '
    +'stroke-linecap="round" stroke-dasharray="'+c.toFixed(2)+'" stroke-dashoffset="'+off.toFixed(2)+'" '
    +'transform="rotate(-90 '+cx+' '+cx+')"/></svg>';
}
function ringInner(p,size,stroke,color,trackColor){
  var r=(size-stroke)/2, c=2*Math.PI*r, off=c*(1-p/100), cx=size/2;
  return '<circle cx="'+cx+'" cy="'+cx+'" r="'+r+'" fill="none" stroke="'+trackColor+'" stroke-width="'+stroke+'"/>'
    +'<circle cx="'+cx+'" cy="'+cx+'" r="'+r+'" fill="none" stroke="'+color+'" stroke-width="'+stroke+'" '
    +'stroke-linecap="round" stroke-dasharray="'+c.toFixed(2)+'" stroke-dashoffset="'+off.toFixed(2)+'" '
    +'transform="rotate(-90 '+cx+' '+cx+')" style="transition:stroke-dashoffset .5s ease"/>';
}
</script>
@stack('scripts')
</body>
</html>
