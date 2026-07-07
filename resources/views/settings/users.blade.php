@extends('layouts.admin')

@section('title', 'Kelola User - Dashboard Akreditasi RS')

@section('content')
  <main class="wrap pt-6">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="mb-4">
      <a href="{{ route('settings.index') }}" class="text-xs font-semibold text-teal hover:text-teal-deep">← Kembali ke
        Pengaturan</a>
    </div>

    <div class="flex items-baseline justify-between gap-4 mt-8 mb-3 flex-wrap">
      <h2 class="m-0 text-sm sm:text-base font-bold tracking-tight text-ink flex items-center gap-2">
        <span class="w-1 h-[18px] rounded-[2px] bg-teal"></span> Kelola User
      </h2>
    </div>

    <div class="board p-4 mb-5 shadow-custom bg-card border border-line rounded-[14px]">
      <div class="mb-4">
        <button class="btn"
          onclick="document.getElementById('addUserForm').style.display='block';this.style.display='none'">+ Tambah
          User</button>
      </div>

      <div id="addUserForm" style="display:none;" class="bg-[#f7fafa] border border-line-soft rounded-xl p-4 mb-4">
        <h3 class="m-0 mb-3 text-sm font-bold text-ink">Tambah User Baru</h3>
        <form method="POST" action="{{ route('settings.users.store') }}">
          @csrf
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 mb-3">
            <div>
              <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama</label>
              <input type="text" name="name" required
                class="w-full px-3 py-2 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-500 mb-1">Email</label>
              <input type="email" name="email" required
                class="w-full px-3 py-2 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-500 mb-1">Pokja</label>
              <select name="pokja_id"
                class="w-full px-3 py-2 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
                <option value="">Semua (Admin)</option>
                @foreach($pokjas as $p)
                  <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                @endforeach
              </select>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-500 mb-1">Role</label>
              <select name="role" required
                class="w-full px-3 py-2 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
                <option value="user">User</option>
                <option value="ketua_tim">Ketua Tim</option>
                <option value="it">IT</option>
                <option value="verifikator">Verifikator</option>
              </select>
            </div>
          </div>
          <div class="flex gap-2">
            <button type="submit" class="btn primary">Simpan</button>
            <button type="button" class="btn ghost"
              onclick="document.getElementById('addUserForm').style.display='none';document.querySelector('[onclick*=\'addUserForm\']').style.display=''">Batal</button>
          </div>
        </form>
      </div>

      <table class="tbl">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Email</th>
            <th class="w-[120px]">Role</th>
            <th class="w-[100px]">Pokja</th>
            <th class="w-[80px] text-center">Google ID</th>
            <th class="w-[140px] text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $u)
            <tr>
              <td>
                <div class="flex items-center gap-2">
                  @if($u->avatar)
                    <img src="{{ $u->avatar }}" class="w-6 h-6 rounded-full object-cover" alt="" />
                  @endif
                  <span class="text-sm font-semibold text-slate-800">{{ $u->name }}</span>
                </div>
              </td>
              <td><span class="text-xs text-slate-600">{{ $u->email }}</span></td>
              <td>
                <span
                  class="text-[10px] font-bold px-2 py-0.5 rounded text-white uppercase {{ $u->role === 'it' ? 'bg-[#2363a6]' : ($u->role === 'ketua_tim' ? 'bg-[#bd770d]' : ($u->role === 'verifikator' ? 'bg-[#7a5bbd]' : 'bg-[#647b85]')) }}">
                  {{ str_replace('_', ' ', $u->role) }}
                </span>
              </td>
              <td><span class="text-xs text-slate-400 font-bold font-mono">{{ $u->pokja?->code ?? '—' }}</span></td>
              <td class="text-center"><span class="text-xs text-slate-400 font-bold">{{ $u->google_id ? '✓' : '—' }}</span>
              </td>
              <td class="text-center">
                <button class="btn ghost" style="font-size:11px;padding:6px 10px"
                  onclick="editUser('{{ $u->id }}', '{{ addslashes($u->name) }}', '{{ $u->email }}', '{{ $u->pokja_id }}', '{{ $u->role }}')">Edit</button>
                @if($u->id !== auth()->id())
                  <form method="POST" action="{{ route('settings.users.destroy', $u->id) }}" style="display:inline"
                    onsubmit="return confirm('Hapus user {{ addslashes($u->name) }}?')">
                    @csrf @method('DELETE')
                    <button class="btn ghost danger" style="font-size:11px;padding:6px 10px">Hapus</button>
                  </form>
                @endif
              </td>
            </tr>
          @empty
            <tr class="empty-row">
              <td colspan="6" class="text-center text-slate-400 italic">Belum ada user.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div id="editUserModal" class="modal-bg">
      <div class="modal" style="max-width:520px">
        <div class="modal-h">
          <h3>Edit User</h3>
        </div>
        <form method="POST" id="editUserForm">
          @csrf @method('PUT')
          <div class="modal-b">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Nama</label>
                <input type="text" name="name" id="editName" required
                  class="w-full px-3 py-2 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Email</label>
                <input type="email" name="email" id="editEmail" required
                  class="w-full px-3 py-2 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
              </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Pokja</label>
                <select name="pokja_id" id="editPokja"
                  class="w-full px-3 py-2 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
                  <option value="">Semua (Admin)</option>
                  @foreach($pokjas as $p)
                    <option value="{{ $p->id }}">{{ $p->code }} - {{ $p->name }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Role</label>
                <select name="role" id="editRole" required
                  class="w-full px-3 py-2 border border-line rounded-lg text-sm bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
                  <option value="user">User</option>
                  <option value="ketua_tim">Ketua Tim</option>
                  <option value="it">IT</option>
                  <option value="verifikator">Verifikator</option>
                </select>
              </div>
            </div>
          </div>
          <div class="modal-f">
            <button type="button" class="btn ghost"
              onclick="document.getElementById('editUserModal').classList.remove('show')">Batal</button>
            <button type="submit" class="btn primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>

    <footer class="mt-6 pt-4 border-t border-line text-slate-400 text-xs">
      <span>BY Firmansyah diana</span>
    </footer>
  </main>

  <script>
    function editUser(id, name, email, pokjaId, role) {
      document.getElementById('editName').value = name;
      document.getElementById('editEmail').value = email;
      document.getElementById('editPokja').value = pokjaId || '';
      document.getElementById('editRole').value = role;
      document.getElementById('editUserForm').action = window.baseUrl + '/settings/users/' + id;
      document.getElementById('editUserModal').classList.add('show');
    }

    document.getElementById('editUserModal')?.addEventListener('click', function (e) {
      if (e.target === this) this.classList.remove('show');
    });
  </script>
@endsection