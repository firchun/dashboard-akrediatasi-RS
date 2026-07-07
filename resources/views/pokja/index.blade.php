@extends('layouts.admin')

@section('title', 'Pokja - Regulasi Akreditasi')

@php
  use App\Http\Controllers\DashboardController as DC;
  $today = now()->format('Y-m-d');
  $groupLabels = [
    'MANAJEMEN' => 'A · Kelompok Manajemen Rumah Sakit',
    'PASIEN' => 'B · Pelayanan Berfokus pada Pasien',
    'SKP' => 'C · Sasaran Keselamatan Pasien',
    'PROGNAS' => 'D · Program Nasional',
  ];
  $JENIS = ["SK/Kebijakan", "Pedoman", "Panduan", "SPO", "Program", "Lainnya"];
  $STATUS = ["Belum", "Proses", "Review", "Selesai"];
  $NILAI = ["", "TL", "TS", "TT", "TDD"];
  $BUKTI = ["R", "D", "O", "W", "S"];
  $BUKTI_LABEL = ["R" => "Regulasi", "D" => "Dokumen bukti", "O" => "Observasi", "W" => "Wawancara", "S" => "Simulasi"];

  $isAdmin = auth()->user()->isAdmin();
  $isUser = auth()->user()->role === 'user';
  $verifikator = auth()->user()->role === 'verifikator';
  $userPokjaId = auth()->user()->pokja_id;
@endphp

@section('content')
  <main class="wrap pt-6" x-data="dashboardApp()" x-cloak>
    @auth
      @if($isAdmin)
        <div class="flex flex-wrap items-center gap-2.5 mb-2">
          <div
            class="flex items-center bg-white border border-line rounded-xl px-3 py-1.5 focus-within:border-teal focus-within:ring-2 focus-within:ring-teal/12 flex-1 min-w-[200px] shadow-sm">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"
              class="text-slate-400 mr-2">
              <circle cx="11" cy="11" r="7" />
              <path d="m21 21-4.3-4.3" />
            </svg>
            <input type="text" x-model="search" placeholder="Cari nama regulasi, PIC, atau kode pokja…"
              class="w-full bg-transparent border-none focus:outline-none text-sm text-ink" />
          </div>
          <select x-model="groupFilter"
            class="border border-line shadow-sm rounded-xl bg-white px-3 py-1.5 text-sm text-ink focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
            <option value="">Semua kelompok</option>
            <option value="MANAJEMEN">Manajemen RS</option>
            <option value="PASIEN">Pelayanan Pasien</option>
            <option value="SKP">Sasaran Keselamatan Pasien</option>
            <option value="PROGNAS">Program Nasional</option>
          </select>
          <select x-model="statusFilter"
            class="border border-line shadow-sm rounded-xl bg-white px-3 py-1.5 text-sm text-ink focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
            <option value="">Semua status</option>
            <option value="Belum">Belum</option>
            <option value="Proses">Proses</option>
            <option value="Review">Review</option>
            <option value="Selesai">Selesai</option>
          </select>
          <button class="btn ghost shadow-sm bg-white" @click="toggleAll()"
            x-text="allExpanded ? 'Tutup semua' : 'Buka semua'"></button>
        </div>
      @endif
    @endauth

    <div id="visHint" class="text-xs text-slate-400 mx-0.5 mt-1 mb-2.5"
      x-text="`Menampilkan ${totalVisibleReg} dari ${totalReg} regulasi · {{ $pokjas->count() }} pokja`">
    </div>

    <div id="kelompokContainer">
      @php $lastGroup = null; @endphp
      @foreach($pokjas as $pokja)
        @php
          if (!$isAdmin && !$verifikator && $userPokjaId !== $pokja->id)
            continue;
          $epDistinct = $pokja->code === 'PPK' && !$setting->is_pendidikan;
        @endphp

        @if($lastGroup !== $pokja->group)
          @php $lastGroup = $pokja->group; @endphp
          <div class="section-head mt-5.5" x-show="hasVisibleInGroup('{{ $pokja->group }}')">
            <h2><span class="bar bg-ink-soft"></span> {{ $groupLabels[$pokja->group] ?? $pokja->group }}</h2>
          </div>
        @endif

        <div class="pokja"
          x-data="pokjaComponent('{{ $pokja->code }}', '{{ $pokja->group }}', '{{ addslashes($pokja->name) }}', {{ $epDistinct ? 'true' : 'false' }}, {{ Js::from($pokja->regulasis) }}, {{ Js::from($pokja->standars) }}, {{ Js::from($pokja->epItems) }})"
          x-show="isVisible" :class="{'open': expanded}" :style="`border-left-color: ${tierColor}`">

          <div class="pk-head" @click="expanded = !expanded">
            <div class="pk-ring" x-html="ringSVG(stats.pct, 50, 6, tierColor, '#e6edee')"></div>
            <div class="pk-id">
              <div class="code">{{ $pokja->code }} <span :style="`color: ${tierColor}`" x-text="`· ${stats.pct}%`"></span>
              </div>
              <div class="name">{{ $pokja->name }}</div>
            </div>
            <div class="pk-stats">
              @if(!$epDistinct)
                <span class="chip ep" :style="`color:#fff;background:${epColor}`" x-text="`EP ${epScore}%`"></span>
              @endif
              <template x-if="stats.selesai"><span class="chip s-selesai"
                  x-text="`${stats.selesai} selesai`"></span></template>
              <template x-if="stats.proses"><span class="chip s-proses" x-text="`${stats.proses} proses`"></span></template>
              <template x-if="stats.review"><span class="chip s-review" x-text="`${stats.review} review`"></span></template>
              <template x-if="stats.belum"><span class="chip s-belum" x-text="`${stats.belum} belum`"></span></template>
              <template x-if="stats.overdue"><span class="chip s-overdue"
                  x-text="`${stats.overdue} lewat`"></span></template>
            </div>
            <div class="caret">▸</div>
          </div>

          <div class="pk-body">
            @if($epDistinct)
              <div class="ep-panel">
                <div class="ep-head">
                  <span class="t">Prediksi Skor EP</span>
                  <span class="pk-na">Bab PPK hanya berlaku untuk RS pendidikan/wahana pendidikan — tidak dihitung.</span>
                </div>
              </div>
            @else
              <div class="ep-panel">
                <div class="ep-head">
                  <span class="t">Prediksi Skor EP</span>
                  <span class="ep-bigscore" :style="`color:#fff;background:${epColor}`" x-text="`${epScore}%`"></span>
                  <span class="ep-thr" :class="epScore >= 80 ? 'ok' : 'no'"
                    x-text="epScore >= 80 ? '≥80% ✓' : 'belum 80%'"></span>
                </div>
                <div class="mt-4 p-4 bg-slate-50 border border-line-soft rounded-xl text-center">
                  <div class="ep-counts flex justify-center mb-4 gap-2 text-xs">
                    <span class="c c-tot px-2 py-1 rounded bg-slate-200 text-slate-700 font-bold"
                      x-text="`${epCounts.total} EP`"></span>
                    <span class="c c-tl px-2 py-1 rounded bg-teal-100 text-teal-800 font-bold"
                      x-text="`TL ${epCounts.tl}`"></span>
                    <span class="c c-ts px-2 py-1 rounded bg-amber-100 text-amber-800 font-bold"
                      x-text="`TS ${epCounts.ts}`"></span>
                    <span class="c c-tt px-2 py-1 rounded bg-red-100 text-red-800 font-bold"
                      x-text="`TT ${epCounts.tt}`"></span>
                    <span class="c c-tdd px-2 py-1 rounded bg-slate-200 text-slate-600 font-bold"
                      x-text="`TDD ${epCounts.tdd}`"></span>
                  </div>
                  <p class="text-sm text-slate-500 mb-4">Pengelolaan tabel dan penilaian EP berada di halaman terpisah.</p>
                  <a href="{{ route('ep.penilaian', $pokja->code) }}"
                    class="inline-flex items-center justify-center h-[34px] px-4 rounded-[10px] text-[13px] font-bold transition bg-teal text-white hover:bg-teal-deep">Buka
                    Halaman Penilaian EP →</a>
                </div>
              </div>
            @endif

            <div class="overflow-x-auto w-full pb-2">
              <table class="tbl read-only-tbl">
                <thead>
                  <tr>
                    <th class="colw-nama">Nama Regulasi</th>
                    <th class="colw-jenis">Jenis</th>
                    <th class="colw-pic">PIC</th>
                    <th class="colw-status">Status</th>
                    <th class="colw-dok">Dokumen</th>
                    <th class="col-act" style="width:70px"></th>
                  </tr>
                </thead>
                <tbody>
                  <template x-for="(reg, index) in regulasis" :key="reg.id">
                    <tr x-show="isRegulasiVisible(reg)">
                      <td class="cell-nama py-2"><span class="text-[13px] font-semibold text-ink"
                          x-text="reg.nama || '-'"></span></td>
                      <td class="col-jenis py-2"><span class="text-xs text-slate-500" x-text="reg.jenis || '-'"></span></td>
                      <td class="py-2"><span class="text-xs text-slate-500 font-medium" x-text="reg.pic || '-'"></span></td>
                      <td class="py-2">
                        <span class="text-[11px] font-bold px-2 py-1 rounded-md" :class="{
                                   'bg-st-selesaibg text-st-selesai': reg.status === 'Selesai',
                                   'bg-st-prosesbg text-st-proses': reg.status === 'Proses',
                                   'bg-st-reviewbg text-st-review': reg.status === 'Review',
                                   'bg-st-belumbg text-st-belum': reg.status === 'Belum' || !reg.status
                                }" x-text="reg.status || 'Belum'"></span>
                      </td>
                      <td class="col-dok py-2">
                        <div class="doklink">
                          <a class="dl-icon" :class="reg.link ? 'on' : 'cursor-not-allowed opacity-50'"
                            :href="reg.link || null" :target="reg.link ? '_blank' : null" rel="noopener"
                            :title="reg.link ? 'Buka dokumen' : 'Belum ada link'"
                            @click="!reg.link && $event.preventDefault()">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2.2">
                              <path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.5 1.5" />
                              <path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.5-1.5" />
                            </svg>
                          </a>
                          <button x-show="!reg.is_verified" type="button" class="btn-upload ml-1"
                            @click="openUploadModal('regulasi', reg.id, reg.nama || 'Tanpa Nama')" title="Upload Dokumen">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                              stroke-width="2.2">
                              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                              <polyline points="17 8 12 3 7 8" />
                              <line x1="12" y1="3" x2="12" y2="15" />
                            </svg>
                          </button>
                          <template x-if="reg.history && reg.history.length > 1">
                            <div class="relative inline-block ml-1" x-data="{ openHist: false }"
                              @click.away="openHist = false">
                              <button type="button" class="btn-upload" @click="openHist = !openHist" title="Riwayat Versi"
                                style="border-color:var(--color-teal); color:var(--color-teal); background:var(--color-teal-100)">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                  stroke-width="2.5">
                                  <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                              </button>
                              <div x-show="openHist"
                                class="absolute right-0 mt-1 w-56 bg-white rounded-lg shadow-lg border border-line z-10 py-1">
                                <div class="px-3 py-1 border-b border-line text-[10px] font-bold text-slate-400 uppercase">
                                  Riwayat Dokumen</div>
                                <template x-for="h in [...reg.history].reverse()" :key="h.version">
                                  <a :href="h.url" target="_blank"
                                    class="block px-3 py-2 text-[11px] text-ink hover:bg-slate-50 border-b border-line last:border-0 flex justify-between items-center">
                                    <div>
                                      <span class="font-bold text-teal" x-text="'v' + h.version"></span>
                                      <span class="text-slate-500 ml-1" x-text="h.uploaded_at"></span>
                                    </div>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                      stroke-width="2">
                                      <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6" />
                                      <polyline points="15 3 21 3 21 9" />
                                      <line x1="10" y1="14" x2="21" y2="3" />
                                    </svg>
                                  </a>
                                </template>
                              </div>
                            </div>
                          </template>
                        </div>
                      </td>
                      <td class="text-right py-2 whitespace-nowrap">
                        <button class="row-edit" @click="openEditRegulasi(reg)" title="Edit">✎</button>
                        <button class="row-del" @click="deleteRegulasi(reg.id, index)" title="Hapus">×</button>
                      </td>
                    </tr>
                  </template>
                  <template x-if="regulasis.length === 0 || !hasVisibleRegulasis">
                    <tr class="empty-row">
                      <td colspan="7">Belum ada regulasi yang sesuai.</td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>

            <div class="mt-3 no-print">
              <button class="btn ghost" @click="openAddRegulasi()">+ Tambah regulasi</button>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <footer
      class="mt-8 pt-4 border-t border-line text-xs font-semibold text-slate-400 flex justify-between items-center flex-wrap gap-2">
      <span>Standar acuan: STARKES Kemenkes (KMK HK.01.07/MENKES/1596/2024) · 16 BAB/Pokja | BY Firmansyah diana</span>
      @auth
        <span>{{ auth()->user()->name }} · {{ auth()->user()->pokja?->name ?? 'Semua' }}</span>
      @endauth
    </footer>

    <!-- EP Import Modal -->
    <div class="modal-bg" :class="epImpModal ? 'show' : ''" @click.self="epImpModal = false">
      <div class="modal">
        <div class="modal-h">
          <h3>Impor daftar EP — <span class="modal-pokja" x-text="epImpCode"></span></h3>
          <p>Tempel daftar Elemen Penilaian dari Instrumen Survei. Satu EP per baris. Pisahkan kolom dengan <b>Tab</b>
            atau <b>;</b> dengan urutan: <b>Kode · Uraian · Bukti(RDOWS) · Nilai(TL/TS/TT/TDD)</b>.</p>
        </div>
        <div class="modal-b">
          <textarea x-model="epImpText"
            class="w-full min-h-[140px] text-sm font-mono border border-line rounded-lg p-3 bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12"
            placeholder="Kode&#9;Uraian&#9;Bukti&#9;Nilai"></textarea>
          <div class="text-[11px] text-slate-500 font-mono mt-1.5 whitespace-pre-wrap">Contoh (Tab antar kolom):
            SKP 1 - EP 1 Regulasi identifikasi pasien R TL
            SKP 1 - EP 2 Identifikasi sebelum tindakan D,O TS</div>
          <label class="flex items-center gap-2 mt-4 text-sm font-semibold text-ink cursor-pointer select-none">
            <input type="checkbox" x-model="epImpReplace"
              class="rounded text-teal border-line focus:ring-teal/20 w-4 h-4" /> Ganti seluruh EP pokja ini
          </label>
        </div>
        <div class="modal-f">
          <button class="btn ghost" @click="epImpModal = false">Batal</button>
          <button class="btn primary" @click="submitEpImport()" :disabled="importing"
            x-text="importing ? 'Mengimpor...' : 'Impor'"></button>
        </div>
      </div>
    </div>

    <!-- Upload Modal -->
    <div class="modal-bg" :class="uploadModal ? 'show' : ''" @click.self="uploadModal = false">
      <div class="modal max-w-[400px]">
        <div class="modal-h">
          <h3>Upload Dokumen</h3>
          <p class="text-xs text-slate-400 mt-1 break-all" x-text="uploadTargetName"></p>
        </div>
        <form @submit.prevent="submitUpload">
          <div class="modal-b px-4.5 py-4">
            <input type="file" x-ref="uploadFile" required
              class="w-full text-sm border border-line p-2 rounded-lg bg-slate-50 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-teal/10 file:text-teal hover:file:bg-teal/20" />
          </div>
          <div class="modal-f">
            <button type="button" class="btn ghost" @click="uploadModal = false">Batal</button>
            <button type="submit" class="btn primary" :disabled="uploading"
              x-text="uploading ? 'Uploading...' : 'Upload'"></button>
          </div>
        </form>
      </div>
    </div>

    <!-- Regulasi Modal (Add / Edit) -->
    <div class="modal-bg" :class="regModal ? 'show' : ''" @click.self="regModal = false">
      <div class="modal max-w-[480px]">
        <div class="modal-h">
          <h3 class="text-ink font-bold text-base"><span
              x-text="regModalMode === 'add' ? 'Tambah Regulasi Baru — ' : 'Edit Regulasi — '"></span><span
              class="text-teal font-mono" x-text="regForm.pokja_code"></span></h3>
        </div>
        <form @submit.prevent="submitRegulasi">
          <div class="modal-b flex flex-col gap-3 px-4.5 py-4">
            <div>
              <label class="block text-[11px] font-bold text-slate-400 mb-1">Nama Regulasi</label>
              <input type="text" x-model="regForm.nama" required placeholder="Nama regulasi..."
                class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Jenis</label>
                <select x-model="regForm.jenis" required
                  class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
                  @foreach($JENIS as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                  @endforeach
                </select>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">PIC</label>
                <input type="text" x-model="regForm.pic" placeholder="Penanggung jawab"
                  class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
              </div>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-400 mb-1">Status Sistem</label>
              <div
                class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-slate-50 text-slate-500 flex items-center justify-between">
                <span
                  x-text="(!regForm.pic || !regForm.link) ? 'Belum (Otomatis)' : (regForm.is_verified ? 'Selesai' : 'Proses')"></span>
                @if(auth()->user()->role === 'verifikator' || auth()->user()->isAdmin())
                  <label class="flex items-center space-x-1 cursor-pointer"
                    :class="(!regForm.pic || !regForm.link) ? 'opacity-50' : ''">
                    <input type="checkbox" x-model="regForm.is_verified" :disabled="!regForm.pic || !regForm.link"
                      class="w-4 h-4 text-teal border-line rounded focus:ring-teal cursor-pointer disabled:cursor-not-allowed"
                      title="Verifikasi">
                    <span class="text-[11px] font-bold text-teal">Verifikasi</span>
                  </label>
                @endif
              </div>
            </div>
          </div>
          <div class="modal-f">
            <button type="button" class="btn ghost" @click="regModal = false">Batal</button>
            <button type="submit" class="btn primary" :disabled="savingReg"
              x-text="savingReg ? 'Menyimpan...' : 'Simpan'"></button>
          </div>
        </form>
      </div>
    </div>

    <!-- EP Item Modal (Add / Edit) -->
    <div class="modal-bg" :class="epModal ? 'show' : ''" @click.self="epModal = false">
      <div class="modal max-w-[500px]">
        <div class="modal-h">
          <h3 class="text-ink font-bold text-base"><span
              x-text="epModalMode === 'add' ? 'Tambah EP Baru — ' : 'Edit EP — '"></span><span class="text-teal font-mono"
              x-text="epForm.pokja_code"></span></h3>
        </div>
        <form @submit.prevent="submitEp">
          <div class="modal-b flex flex-col gap-3 px-4.5 py-4">
            <div class="grid grid-cols-[1fr_1fr_1fr] gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Standar</label>
                <select x-model="epForm.standar_id" required
                  class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
                  <option value="">-- Pilih Standar --</option>
                  <template x-for="std in standars" :key="std.id">
                    <option :value="std.id" x-text="std.kode"></option>
                  </template>
                </select>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">No Urut</label>
                <input type="text" x-model="epForm.no_urut" required placeholder="1, 2, 3..."
                  class="w-full px-3 py-2 border border-line rounded-lg text-[13px] font-mono bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Nilai</label>
                <select x-model="epForm.nilai"
                  class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12">
                  @foreach($NILAI as $n)
                    <option value="{{ $n }}">{{ $n === '' ? '— Belum Dinilai —' : $n }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-400 mb-1">Uraian Elemen Penilaian</label>
              <textarea x-model="epForm.uraian" required rows="2" placeholder="Uraian elemen penilaian..."
                class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12 resize-y leading-relaxed"></textarea>
            </div>

            <div class="grid grid-cols-[1fr_1fr] gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Bukti (RDOWS)</label>
                <div class="flex gap-1.5 flex-wrap mt-1.5">
                  <label
                    class="cursor-pointer inline-flex items-center justify-center w-7 h-7 rounded border font-mono text-xs font-bold transition duration-150 select-none"
                    :class="epForm.bukti_r ? 'bg-[#2363a6] text-white border-transparent' : 'bg-white text-slate-400 border-line'">
                    <input type="checkbox" class="hidden" x-model="epForm.bukti_r" />R
                  </label>
                  <label
                    class="cursor-pointer inline-flex items-center justify-center w-7 h-7 rounded border font-mono text-xs font-bold transition duration-150 select-none"
                    :class="epForm.bukti_d ? 'bg-teal text-white border-transparent' : 'bg-white text-slate-400 border-line'">
                    <input type="checkbox" class="hidden" x-model="epForm.bukti_d" />D
                  </label>
                  <label
                    class="cursor-pointer inline-flex items-center justify-center w-7 h-7 rounded border font-mono text-xs font-bold transition duration-150 select-none"
                    :class="epForm.bukti_o ? 'bg-st-proses text-white border-transparent' : 'bg-white text-slate-400 border-line'">
                    <input type="checkbox" class="hidden" x-model="epForm.bukti_o" />O
                  </label>
                  <label
                    class="cursor-pointer inline-flex items-center justify-center w-7 h-7 rounded border font-mono text-xs font-bold transition duration-150 select-none"
                    :class="epForm.bukti_w ? 'bg-[#7a5bbd text-white border-transparent' : 'bg-white text-slate-400 border-line'">
                    <input type="checkbox" class="hidden" x-model="epForm.bukti_w" />W
                  </label>
                  <label
                    class="cursor-pointer inline-flex items-center justify-center w-7 h-7 rounded border font-mono text-xs font-bold transition duration-150 select-none"
                    :class="epForm.bukti_s ? 'bg-st-selesai text-white border-transparent' : 'bg-white text-slate-400 border-line'">
                    <input type="checkbox" class="hidden" x-model="epForm.bukti_s" />S
                  </label>
                </div>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">PIC</label>
                <input type="text" x-model="epForm.pic" placeholder="Penanggung jawab"
                  class="w-full px-3 py-2 mt-1 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
              </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
              <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Fakta & Analisis</label>
                <textarea x-model="epForm.fakta_analisis" rows="2"
                  class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12 resize-y"></textarea>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Rekomendasi</label>
                <textarea x-model="epForm.rekomendasi" rows="2"
                  class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12 resize-y"></textarea>
              </div>
              <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">Pengingat</label>
                <textarea x-model="epForm.pengingat" rows="2"
                  class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12 resize-y"></textarea>
              </div>
            </div>
          </div>
          <div class="modal-f">
            <button type="button" class="btn ghost" @click="epModal = false">Batal</button>
            <button type="submit" class="btn primary" :disabled="savingEp"
              x-text="savingEp ? 'Menyimpan...' : 'Simpan'"></button>
          </div>
        </form>
      </div>
    </div>

  </main>

  @push('styles')
    <style>
      [x-cloak] {
        display: none !important;
      }

      .row-edit {
        @apply border-none bg-transparent text-slate-400 text-sm p-1 px-1.5 rounded-lg transition duration-150 hover:text-teal hover:bg-[#eef7f8];
      }

      .read-only-tbl td {
        @apply border-b border-line-soft align-middle;
      }

      .bg-\[\#7a5bbd {
        background-color: #7a5bbd;
      }
    </style>
  @endpush

  @push('scripts')
    <script>
      document.addEventListener('alpine:init', () => {
        window.csrfToken = '{{ csrf_token() }}';
        const today = todayISO();

        Alpine.data('dashboardApp', () => ({
          search: '',
          groupFilter: '',
          statusFilter: '',
          allExpanded: false,
          today: today,
          totalReg: 0,
          totalVisibleReg: 0,

          uploadModal: false,
          uploadType: '',
          uploadId: null,
          uploadTargetName: '',
          uploading: false,

          regModal: false,
          regModalMode: 'add',
          regForm: { id: null, pokja_code: '', nama: '', jenis: 'Panduan', pic: '', target: '', status: 'Belum', link: '' },
          savingReg: false,

          epModal: false,
          epModalMode: 'add',
          epForm: { id: null, pokja_code: '', standar_id: '', no_urut: '', uraian: '', bukti_r: false, bukti_d: false, bukti_o: false, bukti_w: false, bukti_s: false, nilai: '', fakta_analisis: '', rekomendasi: '', pengingat: '', pic: '', link: '' },
          savingEp: false,

          epImpModal: false,
          epImpCode: '',
          epImpText: '',
          epImpReplace: false,
          importing: false,

          init() {
            // Setup listener for total recalculation
            window.addEventListener('recalc-totals', () => {
              let total = 0, vis = 0;
              document.querySelectorAll('.pokja').forEach(el => {
                if (el.__x) {
                  const data = el.__x.$data;
                  total += data.regulasis.length;
                  vis += data.regulasis.filter(r => data.isRegulasiVisible(r)).length;
                }
              });
              this.totalReg = total;
              this.totalVisibleReg = vis;
            });
            setTimeout(() => window.dispatchEvent(new Event('recalc-totals')), 100);
            this.$watch('search', () => window.dispatchEvent(new Event('recalc-totals')));
            this.$watch('groupFilter', () => window.dispatchEvent(new Event('recalc-totals')));
            this.$watch('statusFilter', () => window.dispatchEvent(new Event('recalc-totals')));
          },

          toggleAll() {
            this.allExpanded = !this.allExpanded;
            this.$dispatch('toggle-all', { expanded: this.allExpanded });
          },

          hasVisibleInGroup(group) {
            if (this.groupFilter && this.groupFilter !== group) return false;
            let hasVis = false;
            document.querySelectorAll('.pokja').forEach(el => {
              if (el.__x && el.__x.$data.group === group && el.__x.$data.isVisible) hasVis = true;
            });
            return hasVis;
          },

          openUploadModal(type, id, targetName) {
            this.uploadType = type;
            this.uploadId = id;
            this.uploadTargetName = targetName;
            if (this.$refs.uploadFile) this.$refs.uploadFile.value = '';
            this.uploadModal = true;
          },

          async submitUpload() {
            const fileInput = this.$refs.uploadFile;
            if (!fileInput.files.length) return;

            this.uploading = true;
            const formData = new FormData();
            formData.append('_token', window.csrfToken);
            formData.append('type', this.uploadType);
            formData.append('id', this.uploadId);
            formData.append('file', fileInput.files[0]);

            try {
              const res = await fetch(`${window.baseUrl}/upload-document`, { method: 'POST', body: formData });
              const data = await res.json();
              if (data.success) {
                this.$dispatch('upload-success', { type: this.uploadType, id: this.uploadId, url: data.url, status: data.status, history: data.history });
                this.uploadModal = false;
              } else {
                alert('Upload gagal: ' + (data.message || 'Error'));
              }
            } catch (err) {
              alert('Terjadi kesalahan saat mengupload berkas.');
            }
            this.uploading = false;
          },

          openRegulasiModal(mode, code, regData = null) {
            this.regModalMode = mode;
            if (mode === 'add') {
              this.regForm = { id: null, pokja_code: code, nama: '', jenis: 'Panduan', pic: '', target: '', status: 'Belum', link: '' };
            } else {
              this.regForm = { ...regData, pokja_code: code };
            }
            this.regModal = true;
          },

          async submitRegulasi() {
            this.savingReg = true;
            try {
              const url = this.regModalMode === 'add' ? `${window.baseUrl}/pokja/${this.regForm.pokja_code}/regulasi` : `${window.baseUrl}/regulasi/${this.regForm.id}`;
              const method = this.regModalMode === 'add' ? 'POST' : 'PUT';

              const res = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                body: JSON.stringify(this.regForm)
              });

              if (res.ok) {
                const newData = await res.json();
                if (this.regModalMode === 'add') {
                  this.$dispatch('regulasi-added', { code: this.regForm.pokja_code, data: newData });
                } else {
                  this.$dispatch('regulasi-updated', { code: this.regForm.pokja_code, data: newData });
                }
                this.regModal = false;
                window.dispatchEvent(new Event('recalc-totals'));
              } else {
                const err = await res.json();
                alert('Gagal menyimpan regulasi: ' + (err.message || ''));
              }
            } catch (e) {
              alert('Gagal menyimpan regulasi.');
            }
            this.savingReg = false;
          },

          openEpModal(mode, code, epData = null) {
            this.epModalMode = mode;
            if (mode === 'add') {
              this.epForm = { id: null, pokja_code: code, standar_id: '', no_urut: '', uraian: '', bukti_r: false, bukti_d: false, bukti_o: false, bukti_w: false, bukti_s: false, nilai: '', fakta_analisis: '', rekomendasi: '', pengingat: '', pic: '', link: '' };
            } else {
              this.epForm = { ...epData, pokja_code: code };
            }
            this.epModal = true;
          },

          async submitEp() {
            this.savingEp = true;
            try {
              const url = this.epModalMode === 'add' ? `${window.baseUrl}/pokja/${this.epForm.pokja_code}/ep` : `${window.baseUrl}/ep/${this.epForm.id}`;
              const method = this.epModalMode === 'add' ? 'POST' : 'PUT';

              const res = await fetch(url, {
                method: method,
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                body: JSON.stringify(this.epForm)
              });

              if (res.ok) {
                const newData = await res.json();
                if (this.epModalMode === 'add') {
                  this.$dispatch('ep-added', { code: this.epForm.pokja_code, data: newData });
                } else {
                  this.$dispatch('ep-updated', { code: this.epForm.pokja_code, data: newData });
                }
                this.epModal = false;
              } else {
                alert('Gagal menyimpan EP.');
              }
            } catch (e) {
              alert('Gagal menyimpan EP.');
            }
            this.savingEp = false;
          },

          openEpImportModal(code) {
            this.epImpCode = code;
            this.epImpText = '';
            this.epImpReplace = false;
            this.epImpModal = true;
          },

          async submitEpImport() {
            if (!this.epImpText.trim()) { alert('Isi daftar EP terlebih dahulu.'); return; }
            this.importing = true;
            try {
              const res = await fetch(`${window.baseUrl}/pokja/${this.epImpCode}/ep/import`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                body: JSON.stringify({ lines: this.epImpText, replace: this.epImpReplace })
              });
              if (res.ok) {
                const epRes = await fetch(`${window.baseUrl}/pokja/${this.epImpCode}/ep`);
                const eps = await epRes.json();
                this.$dispatch('eps-reloaded', { code: this.epImpCode, data: eps });
                this.epImpModal = false;
              } else {
                alert('Gagal mengimpor EP.');
              }
            } catch (e) {
              alert('Gagal mengimpor EP.');
            }
            this.importing = false;
          }
        }));

        Alpine.data('pokjaComponent', (code, group, name, epDistinct, regulasisData, standarsData, epItemsData) => ({
          code: code,
          group: group,
          name: name,
          epDistinct: epDistinct,
          regulasis: regulasisData,
          standars: standarsData,
          epItems: epItemsData,
          expanded: false,

          init() {
            window.addEventListener('toggle-all', e => {
              if (this.isVisible) this.expanded = e.detail.expanded;
            });
            window.addEventListener('regulasi-added', e => {
              if (e.detail.code === this.code) this.regulasis.push(e.detail.data);
            });
            window.addEventListener('regulasi-updated', e => {
              if (e.detail.code === this.code) {
                const index = this.regulasis.findIndex(r => r.id === e.detail.data.id);
                if (index !== -1) this.regulasis.splice(index, 1, e.detail.data);
              }
            });
            window.addEventListener('ep-added', e => {
              if (e.detail.code === this.code) this.epItems.push(e.detail.data);
            });
            window.addEventListener('ep-updated', e => {
              if (e.detail.code === this.code) {
                const index = this.epItems.findIndex(ep => ep.id === e.detail.data.id);
                if (index !== -1) this.epItems.splice(index, 1, e.detail.data);
              }
            });
            window.addEventListener('eps-reloaded', e => {
              if (e.detail.code === this.code) this.epItems = e.detail.data;
            });
            window.addEventListener('upload-success', e => {
              if (e.detail.type === 'regulasi') {
                let reg = this.regulasis.find(r => r.id == e.detail.id);
                if (reg) {
                  reg.link = e.detail.url;
                  reg.history = e.detail.history;
                  if (e.detail.status) reg.status = e.detail.status;
                }
              } else if (e.detail.type === 'ep') {
                let ep = this.epItems.find(r => r.id == e.detail.id);
                if (ep) {
                  ep.link = e.detail.url;
                  ep.history = e.detail.history;
                  fetch(`${window.baseUrl}/ep/${ep.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                    body: JSON.stringify(ep)
                  });
                }
              }
            });
          },

          isRegulasiVisible(reg) {
            if (this.statusFilter && reg.status !== this.statusFilter) {
              return false;
            }
            if (this.search) {
              const q = this.search.toLowerCase();
              if (!(reg.nama || '').toLowerCase().includes(q) && !(reg.pic || '').toLowerCase().includes(q) && !this.code.toLowerCase().includes(q)) {
                return false;
              }
            }
            return true;
          },

          get isVisible() {
            if (this.groupFilter && this.groupFilter !== this.group) return false;
            const visReg = this.regulasis.filter(r => this.isRegulasiVisible(r)).length;
            if ((this.search || this.statusFilter) && visReg === 0) return false;
            return true;
          },

          get hasVisibleRegulasis() {
            return this.regulasis.filter(r => this.isRegulasiVisible(r)).length > 0;
          },

          get stats() {
            let sel = 0, pro = 0, rev = 0, bel = 0;
            this.regulasis.forEach(reg => {
              if (reg.status === 'Selesai') sel++;
              else if (reg.status === 'Proses') pro++;
              else if (reg.status === 'Review') rev++;
              else bel++;
            });
            const tot = this.regulasis.length;
            const pct = tot === 0 ? 0 : Math.round((sel / tot) * 100);
            return { total: tot, pct, selesai: sel, proses: pro, review: rev, belum: bel };
          },

          get tierColor() {
            const p = this.stats.pct;
            if (this.stats.total === 0 || p === 0) return '#94a3ad';
            if (p < 50) return '#cf4a39';
            if (p < 80) return '#cf8616';
            if (p < 100) return '#2f74b5';
            return '#157a52';
          },

          get epCounts() {
            let tl = 0, ts = 0, tt = 0, tdd = 0;
            this.epItems.forEach(ep => {
              if (ep.nilai === 'TL') tl++;
              else if (ep.nilai === 'TS') ts++;
              else if (ep.nilai === 'TT') tt++;
              else if (ep.nilai === 'TDD') tdd++;
            });
            return { total: this.epItems.length, tl, ts, tt, tdd };
          },

          get epScore() {
            const c = this.epCounts;
            const valid = c.total - c.tdd;
            if (valid <= 0) return 0;
            return Math.round(((c.tl * 10 + c.ts * 5) / (valid * 10)) * 100);
          },

          get epColor() {
            const s = this.epScore;
            if (s >= 80) return '#157a52';
            if (s >= 60) return '#cf8616';
            if (s > 0) return '#cf4a39';
            return '#94a3ad';
          },

          openAddRegulasi() {
            this.openRegulasiModal('add', this.code);
          },

          openEditRegulasi(reg) {
            this.openRegulasiModal('edit', this.code, reg);
          },

          saveRegLink(reg, url) {
            reg.link = url;
            fetch(`${window.baseUrl}/regulasi/${reg.id}`, {
              method: 'PUT',
              headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
              body: JSON.stringify(reg)
            });
          },

          async deleteRegulasi(id, index) {
            if (!confirm('Hapus regulasi ini?')) return;
            const res = await fetch(`${window.baseUrl}/regulasi/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': window.csrfToken } });
            if (res.ok) {
              this.regulasis.splice(index, 1);
              window.dispatchEvent(new Event('recalc-totals'));
            }
          },

          openAddEp() {
            this.openEpModal('add', this.code);
          },

          openEditEp(ep) {
            this.openEpModal('edit', this.code, ep);
          },

          async deleteEpItem(id, index) {
            if (!confirm('Hapus EP ini?')) return;
            const res = await fetch(`${window.baseUrl}/ep/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': window.csrfToken } });
            if (res.ok) this.epItems.splice(index, 1);
          }
        }));
      });
    </script>
  @endpush
@endsection