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
<body>

<nav class="sticky top-0 z-50 bg-ink text-[#eaf4f5]">
  <div class="mx-auto max-w-[1240px] px-5 flex items-center gap-2 min-h-[52px]">
    <div class="font-extrabold text-sm tracking-tight text-white mr-5 whitespace-nowrap">◇ Akreditasi RS</div>
    <div class="flex gap-1 flex-1">
      <a href="{{ route('dashboard') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-[#aacdd5] hover:bg-white/8 hover:text-white transition duration-150 {{ request()->routeIs('dashboard') ? 'bg-teal/25 text-white' : '' }}">Dashboard</a>
      @auth
      <a href="{{ route('pokja.index') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-[#aacdd5] hover:bg-white/8 hover:text-white transition duration-150 {{ request()->routeIs('pokja.*') ? 'bg-teal/25 text-white' : '' }}">Pokja</a>
        @if(auth()->user()->isAdmin())
        <a href="{{ route('settings.index') }}" class="px-3.5 py-2 rounded-lg text-xs font-semibold text-[#aacdd5] hover:bg-white/8 hover:text-white transition duration-150 {{ request()->routeIs('settings.*') ? 'bg-teal/25 text-white' : '' }}">Settings</a>
        @endif
      @endauth
    </div>
    <div class="flex items-center gap-2.5 ml-auto">
      @auth
        @if(auth()->user()->avatar)
        <img class="w-[30px] h-[30px] rounded-full object-cover" src="{{ auth()->user()->avatar }}" alt="" />
        @endif
        <span class="text-xs text-[#bfe0e4] hidden sm:inline">{{ auth()->user()->name }}</span>
        <span class="text-[10px] font-bold px-1.5 py-0.5 rounded text-white uppercase {{ auth()->user()->role === 'it' ? 'bg-[#2363a6]' : (auth()->user()->role === 'ketua_tim' ? 'bg-[#bd770d]' : (auth()->user()->role === 'verifikator' ? 'bg-[#7a5bbd]' : 'bg-[#647b85]')) }}">
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
