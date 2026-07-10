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
<script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>
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

<!-- Global Background Upload Manager -->
<div id="global-upload-manager" data-turbo-permanent style="position: fixed; bottom: 24px; right: 24px; display: flex; flex-direction: column; align-items: flex-end; gap: 12px; z-index: 9999; pointer-events: none;" x-data="uploadManager()">
  
  <!-- Upload List Panel -->
  <div x-show="isOpen && uploads.length > 0" x-transition class="bg-white rounded-xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col" style="width: 360px; pointer-events: auto;">
    <div class="bg-slate-50 px-4 py-3 border-b border-slate-200 flex items-center justify-between">
      <span class="font-bold text-sm text-slate-700">Daftar Unggahan</span>
      <button @click="clearCompleted()" class="text-xs text-teal-600 hover:text-teal-800 font-semibold" x-show="uploads.some(u => u.status === 'success' || u.status === 'error')">Bersihkan Selesai</button>
    </div>
    <div class="overflow-y-auto p-2 flex flex-col gap-1" style="max-height: 300px;">
      <template x-for="item in uploads" :key="item.id">
        <div class="flex items-start gap-3 p-2 rounded-lg hover:bg-slate-50 transition-colors">
          <!-- Icon status -->
          <div class="flex-shrink-0 mt-0.5" style="width: 24px; height: 24px;">
            <template x-if="item.status === 'uploading' || item.status === 'pending'">
              <svg class="animate-spin text-teal-600" style="width: 24px; height: 24px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </template>
            <template x-if="item.status === 'success'">
              <svg class="text-green-500" style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </template>
            <template x-if="item.status === 'error'">
              <svg class="text-red-500" style="width: 24px; height: 24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            </template>
          </div>
          <div class="flex-1 min-w-0">
            <div class="text-xs font-semibold text-slate-800 truncate" x-text="item.file.name"></div>
            <div class="flex items-center gap-2 mt-1">
              <div class="h-1.5 flex-1 bg-slate-200 rounded-full overflow-hidden">
                <div class="h-full rounded-full transition-all duration-300" :class="item.status === 'error' ? 'bg-red-500' : (item.status === 'success' ? 'bg-green-500' : 'bg-teal-500')" :style="`width: ${item.progress}%`"></div>
              </div>
              <span class="font-bold text-slate-500 text-right" style="font-size: 10px; width: 32px;" x-text="item.progress + '%'"></span>
            </div>
            <div class="text-red-600 mt-0.5 leading-tight" style="font-size: 10px;" x-show="item.error" x-text="item.error"></div>
          </div>
        </div>
      </template>
    </div>
  </div>

  <!-- Upload Toggle Button -->
  <template x-if="uploads.length > 0">
    <button @click="isOpen = !isOpen" class="bg-white shadow-xl rounded-full px-4 py-2.5 flex items-center justify-center gap-2 border border-slate-200 hover:bg-slate-50 transition-colors" style="pointer-events: auto; width: max-content;">
      <div class="relative flex-shrink-0" style="width: 20px; height: 20px;">
        <svg class="transform -rotate-90" style="width: 20px; height: 20px;" viewBox="0 0 24 24">
          <circle cx="12" cy="12" r="10" stroke="#f1f5f9" stroke-width="2" fill="none" />
          <circle cx="12" cy="12" r="10" stroke="#0d9488" stroke-width="2" fill="none"
            :stroke-dasharray="62.8" :stroke-dashoffset="62.8 - (62.8 * overallProgress / 100)" style="transition: stroke-dashoffset 0.3s ease" stroke-linecap="round" />
        </svg>
      </div>
      <span class="text-sm font-bold text-slate-700 whitespace-nowrap" x-text="activeUploads === 0 ? 'Upload Selesai' : 'Mengunggah ' + activeUploads + ' file...'"></span>
      <svg class="text-slate-400 transition-transform duration-200" style="width: 16px; height: 16px;" :class="isOpen ? 'rotate-180' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"></polyline></svg>
    </button>
  </template>
</div>

<script>
window.baseUrl = '{{ url('/') }}';
function esc(s){ return String(s==null?'':s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function todayISO(){ return new Date().toISOString().slice(0,10); }
function normalizeUrl(u){ u=String(u||'').trim(); if(!u) return ''; return /^https?:\/\//i.test(u)?u:'https://'+u; }

// Global Upload Manager Component
document.addEventListener('alpine:init', () => {
  Alpine.data('uploadManager', () => ({
    isOpen: false,
    uploads: [], // { id, file, ep_id, progress, status: pending|uploading|success|error, error: '' }
    
    init() {
      window.GlobalUploader = this;
    },
    
    get activeUploads() {
      return this.uploads.filter(u => u.status === 'uploading' || u.status === 'pending').length;
    },
    
    get overallProgress() {
      if (this.uploads.length === 0) return 0;
      const total = this.uploads.reduce((sum, u) => sum + u.progress, 0);
      return Math.round(total / this.uploads.length);
    },
    
    clearCompleted() {
      this.uploads = this.uploads.filter(u => u.status === 'pending' || u.status === 'uploading');
      if(this.uploads.length === 0) this.isOpen = false;
    },

    addFiles(uploadType, uploadId, files) {
      if(!files || files.length === 0) return;
      this.isOpen = true;
      
      Array.from(files).forEach(file => {
        const id = Date.now() + Math.random().toString(36).substring(2);
        this.uploads.push({ id, file, uploadType, uploadId, progress: 0, status: 'pending', error: '' });
        this.processUpload(id);
      });
    },

    processUpload(id) {
      const getProxy = () => this.uploads.find(u => u.id === id);
      const initialProxy = getProxy();
      if(!initialProxy) return;

      initialProxy.status = 'uploading';
      const formData = new FormData();
      formData.append('file', initialProxy.file);
      formData.append('type', initialProxy.uploadType);
      formData.append('id', initialProxy.uploadId);

      const xhr = new XMLHttpRequest();
      xhr.open('POST', window.baseUrl + '/upload-document', true);
      xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');
      xhr.setRequestHeader('Accept', 'application/json');

      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
          const p = getProxy();
          if(p) p.progress = Math.round((e.loaded / e.total) * 99); // 99% until fully complete
        }
      };

      xhr.onload = () => {
        let res;
        try { res = JSON.parse(xhr.responseText); } catch(e) {}
        
        const p = getProxy();
        if(!p) return;

        if (xhr.status >= 200 && xhr.status < 300 && res && res.success) {
          p.progress = 100;
          p.status = 'success';
          // Dispatch event so local page can refresh data if needed
          window.dispatchEvent(new CustomEvent('upload-success'));
        } else {
          p.status = 'error';
          p.error = (res && res.message) ? res.message : 'Gagal mengunggah file';
        }
      };

      xhr.onerror = () => {
        const p = getProxy();
        if(p) {
          p.status = 'error';
          p.error = 'Kesalahan koneksi jaringan';
        }
      };

      xhr.send(formData);
    }
  }));
});

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
