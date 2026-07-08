@extends('layouts.admin')

@section('title', 'Penilaian EP - ' . $pokja->code)

@php
  use App\Http\Controllers\DashboardController as DC;
  $NILAI = ["", "TL", "TS", "TT", "TDD"];

  $isAdmin = auth()->user()->isAdmin();
  $isUser = auth()->user()->role === 'user';
  $verifikator = auth()->user()->role === 'verifikator';
@endphp

@section('content')
  <main class="wrap pt-6"
    x-data="epPageApp('{{ $pokja->code }}', {{ Js::from($pokja->standars) }}, {{ Js::from($pokja->epItems) }})" x-cloak>
    <div class="mb-4">
      <a href="{{ route('pokja.index') }}"
        class="text-xs font-semibold text-teal hover:text-teal-deep flex items-center gap-1">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <path d="M19 12H5M12 19l-7-7 7-7" />
        </svg> Kembali ke Halaman Utama Pokja
      </a>
    </div>

    <div class="board p-5 mb-5 shadow-custom bg-card border border-line rounded-[14px]">
      <div class="grid grid-cols-[auto_1fr] gap-5 items-center">
        <div class="w-[50px] h-[50px]" x-html="ringSVG(epScore, 50, 6, epColor, '#e6edee')"></div>
        <div>
          <h2 class="m-0 text-lg font-bold text-ink">Penilaian EP · {{ $pokja->code }}</h2>
          <div class="mt-1.5 text-xs text-slate-500 font-medium">
            Total EP: <b class="text-ink-soft" x-text="epCounts.total"></b> ·
            Prediksi Skor EP: <b class="text-ink-soft" :style="`color:${epColor}`" x-text="`${epScore}%`"></b>
          </div>
        </div>
      </div>
      <div
        class="mt-4 pt-3 border-t border-line-soft grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-1.5 text-[11px] text-slate-500">
        <div><span
            class="inline-block w-[38px] text-center font-bold text-[10px] px-1.5 py-0.5 rounded n-TL mr-1">TL</span> <b
            class="text-slate-700">Terpenuhi Lengkap</b> — Skor 10. Tercapai ≥80%.</div>
        <div><span
            class="inline-block w-[38px] text-center font-bold text-[10px] px-1.5 py-0.5 rounded n-TS mr-1">TS</span> <b
            class="text-slate-700">Terpenuhi Sebagian</b> — Skor 5. Tercapai 20–79%.</div>
        <div><span
            class="inline-block w-[38px] text-center font-bold text-[10px] px-1.5 py-0.5 rounded n-TT mr-1">TT</span> <b
            class="text-slate-700">Tidak Terpenuhi</b> — Skor 0. Tercapai &lt;20%.</div>
        <div><span
            class="inline-block w-[38px] text-center font-bold text-[10px] px-1.5 py-0.5 rounded n-TDD mr-1">TDD</span> <b
            class="text-slate-700">Tidak Dapat Dibuktikan</b> — Dikecualikan dari skor.</div>
      </div>
      <div class="mt-2 text-[11px] text-slate-400">
        Rumus: <code>(TL×10 + TS×5) ÷ (EP berlaku ×10) × 100%</code>
      </div>
    </div>

    <div class="board p-5 mb-5 shadow-custom bg-card border border-line rounded-[14px] mt-6">
      <div class="mb-4 border-b border-line pb-3 flex flex-wrap items-center justify-between gap-3">
        <div>
          <h3 class="m-0 text-base font-bold text-ink flex items-center gap-2"><span
              class="w-1 h-[14px] rounded-[2px] bg-teal"></span>Daftar Elemen Penilaian (EP)</h3>
          <div class="ep-counts mt-2" style="font-size:11px;">
            <span class="c c-tot" x-text="`${epCounts.total} EP`"></span>
            <span class="c c-tl" x-text="`TL ${epCounts.tl}`"></span>
            <span class="c c-ts" x-text="`TS ${epCounts.ts}`"></span>
            <span class="c c-tt" x-text="`TT ${epCounts.tt}`"></span>
            <span class="c c-tdd" x-text="`TDD ${epCounts.tdd}`"></span>
          </div>
        </div>
        <div class="flex gap-2">
          <button class="btn ghost" @click="openStandarModal()">+ Tambah Standar</button>
          <button class="btn ghost" @click="openEpModal('add')">+ Tambah EP</button>
          <button class="btn ghost" @click="openEpImportModal()">⤵ Impor Excel</button>
        </div>
      </div>

      <div class="overflow-x-auto w-full pb-2">
        <table class="eptbl read-only-tbl w-full">
          <thead>
            <tr>
              <th class="colw-epkode">Standar</th>
              <th class="colw-epkode" style="width:50px">No urut</th>
              <th>Elemen Penilaian</th>
              <th style="width:30px">R</th>
              <th style="width:30px">D</th>
              <th style="width:30px">O</th>
              <th style="width:30px">W</th>
              <th style="width:30px">S</th>
              <th class="colw-epnilai" style="width:60px">Nilai</th>
              <th style="min-width:120px">Fakta & Analisis</th>
              <th style="min-width:120px">Rekomendasi</th>
              <th style="min-width:120px">Pengingat</th>
              <th class="colw-eppic">PIC</th>
              <th class="colw-epdok">Dokumen</th>
              <th class="col-act" style="width:70px"></th>
            </tr>
          </thead>
          <template x-for="(std, sIndex) in standars" :key="std.id">
            <tbody class="standar-group">
              <tr class="bg-slate-100 border-t-2 border-slate-300">
                <td class="ep-kode py-2 align-top"><span class="font-mono font-bold text-xs text-ink"
                    x-text="std.kode || '-'"></span></td>
                <td></td>
                <td colspan="12" class="ep-ur py-2"><span
                    class="text-xs font-bold text-slate-700 whitespace-pre-wrap leading-relaxed"
                    x-text="std.uraian || '-'"></span></td>
                <td class="text-right py-2 pr-2">
                  <button class="row-del" @click="deleteStandar(std.id)" title="Hapus Standar">×</button>
                </td>
              </tr>
              <!-- EP Items for this standar -->
              <template x-for="(ep, index) in std.ep_items" :key="ep.id">
                <tr class="border-b border-line-soft hover:bg-slate-50 transition-colors">
                  <td></td>
                  <td class="ep-kode py-2 text-center"><span class="font-mono font-bold text-xs text-ink"
                      x-text="ep.no_urut || '-'"></span></td>
                  <td class="ep-ur py-2"><span class="text-xs text-slate-500 whitespace-pre-wrap leading-relaxed"
                      x-text="ep.uraian || '-'"></span></td>
                  <td class="py-2 text-center text-[10px] font-bold text-[#2363a6]"><template
                      x-if="ep.bukti_r"><span>R</span></template></td>
                  <td class="py-2 text-center text-[10px] font-bold text-teal"><template
                      x-if="ep.bukti_d"><span>D</span></template></td>
                  <td class="py-2 text-center text-[10px] font-bold text-st-proses"><template
                      x-if="ep.bukti_o"><span>O</span></template></td>
                  <td class="py-2 text-center text-[10px] font-bold text-[#7a5bbd]"><template
                      x-if="ep.bukti_w"><span>W</span></template></td>
                  <td class="py-2 text-center text-[10px] font-bold text-st-selesai"><template
                      x-if="ep.bukti_s"><span>S</span></template></td>

                  <td class="py-2 text-center">
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-md"
                      :class="ep.nilai ? `n-${ep.nilai}` : 'bg-slate-100 text-slate-400'" style="display:inline-block"
                      x-text="ep.nilai || '—'"></span>
                  </td>
                  <td class="py-2"><span class="text-xs text-slate-500 line-clamp-2" :title="ep.fakta_analisis"
                      x-text="ep.fakta_analisis || '-'"></span></td>
                  <td class="py-2"><span class="text-xs text-slate-500 line-clamp-2" :title="ep.rekomendasi"
                      x-text="ep.rekomendasi || '-'"></span></td>
                  <td class="py-2"><span class="text-xs text-slate-500 line-clamp-2" :title="ep.pengingat"
                      x-text="ep.pengingat || '-'"></span></td>

                  <td class="py-2"><span class="text-[10px] text-slate-500 font-medium whitespace-nowrap"
                      x-text="ep.pic || '-'"></span></td>
                  <td class="py-2">
                    <div class="doklink">
                      <a class="dl-icon" :class="(ep.link || (ep.upload_files && ep.upload_files.length > 0)) ? 'on' : 'cursor-not-allowed opacity-50'" :href="ep.link || null"
                        :target="(ep.link || (ep.upload_files && ep.upload_files.length > 0)) ? '_blank' : null" rel="noopener"
                        :title="(ep.link || (ep.upload_files && ep.upload_files.length > 0)) ? 'Buka dokumen' : 'Belum ada dokumen'"
                        @click.prevent="(ep.link || (ep.upload_files && ep.upload_files.length > 0)) ? openPreview(ep, std.kode + ' EP ' + (ep.no_urut || '')) : null">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                          stroke-width="2.2">
                          <path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.5 1.5" />
                          <path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.5-1.5" />
                        </svg>
                      </a>
                      <button type="button" class="btn-upload ml-1"
                        @click="openUploadModal('ep', ep, ep.no_urut || ep.uraian)" title="Upload Dokumen">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                          stroke-width="2.2">
                          <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
                          <polyline points="17 8 12 3 7 8" />
                          <line x1="12" y1="3" x2="12" y2="15" />
                        </svg>
                      </button>
                    </div>
                  </td>
                  <td class="text-right py-2 whitespace-nowrap">
                    <button class="row-edit" @click="openEpModal('edit', ep)" title="Edit">✎</button>
                    <button class="row-del" @click="deleteEpItem(ep.id, index)" title="Hapus">×</button>
                  </td>
                </tr>
              </template>
            </tbody>
          </template>
          <template x-if="epItems.length === 0">
            <tbody>
              <tr class="empty-row">
                <td colspan="15" class="text-center text-slate-400 italic py-8">Belum ada EP. Silakan Impor Excel atau
                  tambah manual.</td>
              </tr>
            </tbody>
          </template>
        </table>
      </div>
      <div class="bukti-legend mt-4 text-[11px] text-slate-500 p-3 bg-slate-50 rounded-lg border border-line-soft">
        <b>R</b> Regulasi · <b>D</b> Dokumen bukti · <b>O</b> Observasi · <b>W</b> Wawancara · <b>S</b> Simulasi
      </div>
    </div>

    <!-- Modal Tambah Standar -->
    <div class="modal-bg" :class="stdModal ? 'show' : ''" @click.self="stdModal = false">
      <div class="modal max-w-[480px]">
        <div class="modal-h">
          <h3 class="text-ink font-bold text-base">Tambah Standar Baru</h3>
        </div>
        <form @submit.prevent="submitStandar">
          <div class="modal-b flex flex-col gap-3 px-4.5 py-4">
            <div>
              <label class="block text-[11px] font-bold text-slate-400 mb-1">Kode Standar</label>
              <input type="text" x-model="stdForm.kode" required placeholder="Contoh: PROGNAS 1"
                class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12" />
            </div>
            <div>
              <label class="block text-[11px] font-bold text-slate-400 mb-1">Uraian Standar</label>
              <textarea x-model="stdForm.uraian" required rows="3" placeholder="Rumah sakit melaksanakan..."
                class="w-full px-3 py-2 border border-line rounded-lg text-[13px] bg-white focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12 resize-y"></textarea>
            </div>
          </div>
          <div class="modal-f">
            <button type="button" class="btn ghost" @click="stdModal = false">Batal</button>
            <button type="submit" class="btn primary" :disabled="savingStd"
              x-text="savingStd ? 'Menyimpan...' : 'Simpan'"></button>
          </div>
        </form>
      </div>
    </div>

    <!-- EP Item Modal (Add / Edit) -->
    <div class="modal-bg" :class="epModal ? 'show' : ''" @click.self="epModal = false">
      <div class="modal max-w-[600px]">
        <div class="modal-h">
          <h3 class="text-ink font-bold text-base"><span
              x-text="epModalMode === 'add' ? 'Tambah EP Baru' : 'Edit EP'"></span></h3>
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
                <input type="text" x-model="epForm.no_urut" required placeholder="1, 2, a, b..."
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
                    :class="epForm.bukti_w ? 'bg-[#7a5bbd] text-white border-transparent' : 'bg-white text-slate-400 border-line'">
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

    <!-- Upload Document Modal -->
    <div class="modal-bg" :class="uploadModal ? 'show' : ''" @click.self="uploadModal = false">
      <div class="modal max-w-[400px]">
        <div class="modal-h">
          <h3 class="text-ink font-bold text-base">Unggah Dokumen Bukti</h3>
        </div>
        <form @submit.prevent="submitUpload">
          <div class="modal-b px-4.5 py-5">
            <div class="text-[13px] text-slate-600 mb-4">
              Pilih dokumen untuk <strong class="text-teal" x-text="uploadTargetName"></strong>.
            </div>

            <!-- History List -->
            <div class="mb-5" x-show="uploadHistory.length > 0">
              <h4 class="text-xs font-bold text-slate-700 mb-2">Riwayat File:</h4>
              <div class="flex flex-col gap-2 max-h-[200px] overflow-y-auto pr-1">
                <template x-for="(h, i) in uploadHistory" :key="i">
                  <div class="flex items-start justify-between bg-slate-50 p-2.5 rounded-lg border border-line-soft">
                    <div class="overflow-hidden flex-1 mr-2">
                      <a :href="h.url" target="_blank"
                        class="text-teal font-medium text-xs truncate hover:underline block mb-0.5" x-text="h.filename"
                        :title="h.filename"></a>
                      <div class="text-[10px] text-slate-400 flex items-center gap-1.5">
                        <span x-text="h.uploaded_at"></span>
                        <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                        <span x-text="h.uploaded_by"></span>
                      </div>
                    </div>
                    <button type="button" @click="deleteFile(h)" class="text-red-500 hover:text-red-700 p-1.5 rounded-md hover:bg-red-50 transition" title="Hapus File">
                      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>
                    </button>
                  </div>
                </template>
              </div>
            </div>
            <hr class="border-line mb-4" x-show="uploadHistory.length > 0" />

            <h4 class="text-xs font-bold text-slate-700 mb-2">Tambah Dokumen Baru:</h4>
            <input type="file" x-ref="uploadFile" required
              class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-teal-50 file:text-teal hover:file:bg-teal-100 transition" />
          </div>
          <div class="modal-f">
            <button type="button" class="btn ghost" @click="uploadModal = false">Batal</button>
            <button type="submit" class="btn primary" :disabled="uploading"
              x-text="uploading ? 'Mengunggah...' : 'Unggah'"></button>
          </div>
        </form>
      </div>
    </div>

    <!-- Import Modal -->
    <div class="modal-bg" :class="epImpModal ? 'show' : ''" @click.self="epImpModal = false">
      <div class="modal max-w-[700px]">
        <div class="modal-h">
          <h3 class="text-ink font-bold text-base">Impor Daftar EP - <span class="text-teal font-mono"
              x-text="code"></span></h3>
        </div>
        <form @submit.prevent="submitEpImport">
          <div class="modal-b flex flex-col gap-3 px-4.5 py-4">
            <p class="text-[12px] text-slate-500">
              Paste baris-baris dari Excel (Kolom: Standar, No Urut, Uraian, R,D,O,W,S, Nilai, Fakta, Rekomendasi,
              Pengingat). Kosongkan kolom Standar untuk baris EP, kosongkan kolom No Urut untuk baris Standar induk.
            </p>
            <label class="flex items-center space-x-2 mt-2">
              <input type="checkbox" x-model="epImpReplace"
                class="w-4 h-4 text-red-500 border-line rounded focus:ring-red-500">
              <span class="text-[12px] font-bold text-red-500">Ganti seluruh EP & Standar yang ada (Hapus data
                lama)</span>
            </label>
            <textarea x-model="epImpText" required rows="10" placeholder="Copy dari Excel dan paste di sini..."
              class="w-full px-3 py-2 border border-line rounded-lg text-[12px] font-mono whitespace-pre bg-slate-50 focus:outline-none focus:border-teal focus:ring-2 focus:ring-teal/12"></textarea>
          </div>
          <div class="modal-f">
            <button type="button" class="btn ghost" @click="epImpModal = false">Batal</button>
            <button type="submit" class="btn primary" :disabled="importing"
              x-text="importing ? 'Mengimpor...' : 'Impor Data'"></button>
          </div>
        </form>
      </div>
    </div>

    <!-- Document Preview Modal -->
    <div class="modal-bg" :class="previewModal ? 'show' : ''" @click.self="closePreview" style="z-index: 150;">
      <div class="modal max-w-[900px] w-full h-[90vh] flex flex-col"
        style="height: 90vh !important; display: flex; flex-direction: column;">
        <div class="modal-h flex items-center justify-between border-b border-line px-4.5 py-3">
          <div>
            <h3 class="text-ink font-bold text-base" x-text="previewTitle">Pratinjau Dokumen</h3>
            <p class="text-[11px] text-slate-400 mt-0.5" x-text="previewUrl" style="word-break: break-all;"></p>
          </div>
          <button type="button" class="modal-close text-2xl font-bold text-slate-400 hover:text-slate-600"
            @click="closePreview">×</button>
        </div>

        <!-- File Selector Header -->
        <div
          class="bg-white border-b border-line-soft px-4.5 py-3 overflow-x-auto whitespace-nowrap hide-scrollbar flex gap-2"
          x-show="previewFiles.length > 1">
          <template x-for="(f, i) in previewFiles" :key="i">
            <button type="button" @click="changePreviewFile(f.url)"
              class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition"
              :class="previewUrl === f.url ? 'bg-teal text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
              <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                class="opacity-75">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <polyline points="14 2 14 8 20 8" />
              </svg>
              <span x-text="'File ' + (previewFiles.length - i)"></span>
            </button>
          </template>
        </div>

        <div class="modal-b flex-1 overflow-auto bg-slate-50 p-4.5"
          style="flex: 1; overflow-y: auto; background-color: #f8fafc;">
          <!-- Loading indicator -->
          <div x-show="previewLoading" class="flex flex-col items-center justify-center py-24 text-slate-400">
            <svg class="animate-spin h-8 w-8 text-teal mb-3" fill="none" viewBox="0 0 24 24"
              style="animation: spin 1s linear infinite; height: 32px; width: 32px; color: #0C7C8C;">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"
                style="opacity: 0.25;"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                style="opacity: 0.75;"></path>
            </svg>
            <span class="text-xs font-semibold">Mengunduh & menyiapkan dokumen...</span>
          </div>

          <!-- Preview views based on type -->
          <div x-show="!previewLoading" class="h-full">
            <!-- PDF container -->
            <template x-if="previewType === 'pdf'">
              <iframe :src="previewUrl" class="w-full h-full border-0 rounded-lg bg-white shadow-sm"
                style="width: 100%; height: 100%; min-height: 60vh;"></iframe>
            </template>

            <!-- Word Container -->
            <div x-show="previewType === 'docx'" x-ref="previewDocxContainer"
              class="bg-white p-6 rounded-lg shadow-sm border border-line-soft overflow-auto" style="min-height: 100%;">
            </div>

            <!-- Excel Container -->
            <div x-show="previewType === 'xlsx'" x-ref="previewXlsxContainer" class="flex flex-col gap-6"
              style="min-height: 100%;"></div>

            <!-- Image Container -->
            <template x-if="previewType === 'image'">
              <div class="flex items-center justify-center bg-white p-4 rounded-lg shadow-sm border border-line-soft">
                <img :src="previewUrl" class="max-w-full max-h-[70vh] object-contain rounded" />
              </div>
            </template>

            <!-- Unsupported / Fallback -->
            <template x-if="previewType === 'unsupported'">
              <div class="text-center py-20 bg-white p-8 rounded-lg shadow-sm border border-line-soft">
                <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                  width="48" height="48">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h4 class="text-sm font-bold text-slate-700 mb-1">Pratinjau Tidak Didukung</h4>
                <p class="text-xs text-slate-400 mb-4">Format berkas ini tidak dapat dipratinjau secara langsung.</p>
                <a :href="previewUrl" target="_blank"
                  class="btn primary inline-flex items-center gap-1.5 px-4 py-2 text-xs">
                  Buka di Tab Baru
                </a>
              </div>
            </template>
          </div>
        </div>
        <div class="modal-f flex items-center justify-end gap-2 border-t border-line px-4.5 py-3"
          style="display: flex; align-items: center; justify-content: flex-end; gap: 8px;">
          <a :href="previewUrl" target="_blank" download class="btn ghost text-xs flex items-center gap-1.5"
            style="display: inline-flex; align-items: center; gap: 6px;">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
              <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
              <polyline points="7 10 12 15 17 10" />
              <line x1="12" y1="15" x2="12" y2="3" />
            </svg>
            Unduh File
          </a>
          <button type="button" class="btn primary text-xs" @click="closePreview">Tutup</button>
        </div>
      </div>
    </div>
  </main>

  @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jszip@3.10.1/dist/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/docx-preview@0.1.15/dist/docx-preview.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
    <script>
      document.addEventListener('alpine:init', () => {
        window.csrfToken = '{{ csrf_token() }}';
        Alpine.data('epPageApp', (code, standarsData, epItemsData) => ({
          code: code,
          standars: standarsData,
          epItems: epItemsData,

          async reloadData() {
            try {
              const res = await fetch(`${window.baseUrl}/pokja/${this.code}/data-ep`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
              });
              if (res.ok) {
                const data = await res.json();
                this.standars = data.standars;
                this.epItems = data.epItems;
              }
            } catch (e) {
              console.error("Gagal reload data:", e);
            }
          },

          stdModal: false,
          stdForm: { kode: '', uraian: '' },
          savingStd: false,

          epModal: false,
          epModalMode: 'add',
          epForm: { id: null, pokja_code: code, standar_id: '', no_urut: '', uraian: '', bukti_r: false, bukti_d: false, bukti_o: false, bukti_w: false, bukti_s: false, nilai: '', fakta_analisis: '', rekomendasi: '', pengingat: '', pic: '', link: '' },
          savingEp: false,

          epImpModal: false,
          epImpText: '',
          epImpReplace: false,
          importing: false,

          uploadModal: false,
          uploadType: '',
          uploadId: null,
          uploadTargetName: '',
          uploading: false,
          uploadHistory: [],

          previewModal: false,
          previewUrl: '',
          previewTitle: '',
          previewType: '',
          previewLoading: false,
          previewFiles: [],

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

          openStandarModal() {
            this.stdForm = { kode: '', uraian: '' };
            this.stdModal = true;
          },

          async submitStandar() {
            this.savingStd = true;
            try {
              const res = await fetch(`${window.baseUrl}/pokja/${this.code}/standar`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': window.csrfToken },
                body: JSON.stringify(this.stdForm)
              });
              if (res.ok) {
                this.stdModal = false;
                await this.reloadData();
              } else {
                const err = await res.text();
                alert('Gagal menyimpan standar: ' + res.status + ' ' + (err.substring(0, 100)));
              }
            } catch (e) {
              alert('Terjadi kesalahan jaringan');
            }
            this.savingStd = false;
          },

          openEpModal(mode, epData = null) {
            this.epModalMode = mode;
            if (mode === 'add') {
              this.epForm = { id: null, pokja_code: this.code, standar_id: '', no_urut: '', uraian: '', bukti_r: false, bukti_d: false, bukti_o: false, bukti_w: false, bukti_s: false, nilai: '', fakta_analisis: '', rekomendasi: '', pengingat: '', pic: '', link: '' };
            } else {
              this.epForm = { ...epData, pokja_code: this.code };
            }
            this.epModal = true;
          },

          async submitEp() {
            this.savingEp = true;
            try {
              const url = this.epModalMode === 'add' ? `${window.baseUrl}/pokja/${this.code}/ep` : `${window.baseUrl}/ep/${this.epForm.id}`;
              const method = this.epModalMode === 'add' ? 'POST' : 'PUT';

              const payload = { ...this.epForm };
              delete payload.history;
              delete payload.upload_files;
              delete payload.pokja;
              delete payload.standar;

              const res = await fetch(url, {
                method: method,
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': window.csrfToken },
                body: JSON.stringify(payload)
              });

              if (res.ok) {
                this.epModal = false;
                await this.reloadData();
              } else {
                const err = await res.text();
                alert('Gagal menyimpan EP: ' + res.status + ' ' + (err.substring(0, 100)));
              }
            } catch (e) {
              alert('Terjadi kesalahan jaringan saat menyimpan EP.');
            }
            this.savingEp = false;
          },

          async deleteEpItem(id, index) {
            if (!confirm('Hapus EP ini?')) return;
            const res = await fetch(`${window.baseUrl}/ep/${id}`, {
              method: 'DELETE',
              credentials: 'same-origin',
              headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': window.csrfToken }
            });
            if (res.ok) await this.reloadData();
          },

          async deleteStandar(id) {
            if (!confirm('Hapus standar ini beserta seluruh elemen penilaian (EP) di dalamnya?')) return;
            const res = await fetch(`${window.baseUrl}/standar/${id}`, {
              method: 'DELETE',
              credentials: 'same-origin',
              headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': window.csrfToken }
            });
            if (res.ok) {
              await this.reloadData();
            } else {
              alert('Gagal menghapus standar.');
            }
          },

          openEpImportModal() {
            this.epImpText = '';
            this.epImpReplace = false;
            this.epImpModal = true;
          },

          async submitEpImport() {
            if (!this.epImpText.trim()) { alert('Isi daftar EP terlebih dahulu.'); return; }
            this.importing = true;
            try {
              const res = await fetch(`${window.baseUrl}/pokja/${this.code}/ep/import`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': window.csrfToken },
                body: JSON.stringify({ lines: this.epImpText, replace: this.epImpReplace })
              });
              if (res.ok) {
                this.epImpModal = false;
                await this.reloadData();
              } else {
                alert('Gagal mengimpor EP.');
              }
            } catch (e) {
              alert('Gagal mengimpor EP.');
            }
            this.importing = false;
          },

          openUploadModal(type, item, targetName) {
            this.uploadType = type;
            this.uploadId = item.id;
            this.uploadTargetName = targetName;

            this.uploadHistory = [];
            const seenUrls = new Set();

            if (item.history && Array.isArray(item.history)) {
              item.history.forEach(h => {
                if (h.url && !seenUrls.has(h.url)) {
                  seenUrls.add(h.url);
                  this.uploadHistory.push({
                    url: h.url,
                    filename: h.filename || h.url.split('/').pop(),
                    uploaded_at: h.uploaded_at,
                    uploaded_by: h.uploaded_by || 'System',
                    source: 'Lama'
                  });
                }
              });
            }

            if (item.upload_files && Array.isArray(item.upload_files)) {
              item.upload_files.forEach(u => {
                if (u.file && !seenUrls.has(u.file)) {
                  seenUrls.add(u.file);
                  this.uploadHistory.push({
                    id: u.id,
                    is_link: false,
                    url: u.file,
                    filename: u.file.split('/').pop().split('?')[0],
                    uploaded_at: new Date(u.created_at).toLocaleString('id-ID'),
                    uploaded_by: u.user ? u.user.name : 'System'
                  });
                }
              });
            }

            if (item.link && !seenUrls.has(item.link)) {
              seenUrls.add(item.link);
              this.uploadHistory.push({
                id: item.id,
                is_link: true,
                url: item.link,
                filename: item.link.split('/').pop().split('?')[0],
                uploaded_at: '-',
                uploaded_by: '-'
              });
            }

            // Sort history descending by uploaded_at loosely
            this.uploadHistory.reverse();

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
              const res = await fetch(`${window.baseUrl}/upload-document`, {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
              });
              const data = await res.json();
              if (data.success) {
                this.uploadModal = false;
                await this.reloadData();
              } else {
                alert('Upload gagal: ' + (data.message || 'Error'));
              }
            } catch (err) {
              alert('Terjadi kesalahan.');
            }
            this.uploading = false;
          },

          async deleteFile(h) {
            if (!confirm('Apakah Anda yakin ingin menghapus file ini?')) return;
            try {
              const res = await fetch(`${window.baseUrl}/upload-document/${h.id}`, {
                method: 'DELETE',
                headers: { 
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ is_link: h.is_link, type: this.uploadType })
              });
              const data = await res.json();
              if (!res.ok) throw new Error(data.message || 'Gagal menghapus file');
              
              if (data.success) {
                this.uploadHistory = this.uploadHistory.filter(x => x !== h);
                await this.reloadData();
                if (this.uploadHistory.length === 0) {
                  this.uploadModal = false;
                }
              }
            } catch (err) {
              alert(err.message);
            }
          },

          async openPreview(item, title) {
            this.previewTitle = title || 'Pratinjau Dokumen';
            this.previewFiles = [];
            const seenUrls = new Set();

            if (item.history && Array.isArray(item.history)) {
              item.history.forEach(h => {
                if (h.url && !seenUrls.has(h.url)) {
                  seenUrls.add(h.url);
                  this.previewFiles.push({
                    url: h.url,
                    filename: h.filename || h.url.split('/').pop(),
                    uploaded_at: h.uploaded_at,
                    source: 'Lama'
                  });
                }
              });
            }

            if (item.upload_files && Array.isArray(item.upload_files)) {
              item.upload_files.forEach(u => {
                if (u.file && !seenUrls.has(u.file)) {
                  seenUrls.add(u.file);
                  this.previewFiles.push({
                    url: u.file,
                    filename: u.file.split('/').pop().split('?')[0],
                    uploaded_at: new Date(u.created_at).toLocaleString('id-ID'),
                    source: 'Baru'
                  });
                }
              });
            }

            if (item.link && !seenUrls.has(item.link)) {
              seenUrls.add(item.link);
              this.previewFiles.push({
                url: item.link,
                filename: item.link.split('/').pop().split('?')[0],
                uploaded_at: '-',
                source: 'Lama'
              });
            }
            this.previewFiles.reverse();

            this.previewModal = true;
            if (this.previewFiles.length > 0) {
              this.changePreviewFile(this.previewFiles[0].url);
            } else if (item.link) {
              this.changePreviewFile(item.link);
            }
          },

          changePreviewFile(url) {
            this.previewUrl = url;
            this.previewLoading = true;
            this.previewType = '';

            const ext = url.split('.').pop().toLowerCase().split('?')[0];

            if (['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp'].includes(ext)) {
              this.previewType = 'image';
              this.previewLoading = false;
            } else if (ext === 'pdf') {
              this.previewType = 'pdf';
              this.previewLoading = false;
            } else if (ext === 'docx') {
              this.previewType = 'docx';
              this.$nextTick(async () => {
                try {
                  const response = await fetch(this.previewUrl);
                  if (!response.ok) throw new Error('Gagal mengunduh berkas Word.');
                  const arrayBuffer = await response.arrayBuffer();
                  const container = this.$refs.previewDocxContainer;
                  container.innerHTML = '';
                  await docx.renderAsync(arrayBuffer, container);
                } catch (err) {
                  this.$refs.previewDocxContainer.innerHTML = `<div class="text-center py-12 text-red-500 font-semibold">Gagal memuat pratinjau Word: ${err.message}</div>`;
                } finally {
                  this.previewLoading = false;
                }
              });
            } else if (['xlsx', 'xls'].includes(ext)) {
              this.previewType = 'xlsx';
              this.$nextTick(async () => {
                try {
                  const response = await fetch(this.previewUrl);
                  if (!response.ok) throw new Error('Gagal mengunduh berkas Excel.');
                  const arrayBuffer = await response.arrayBuffer();
                  const workbook = XLSX.read(arrayBuffer, { type: 'array' });

                  let htmlContent = '';
                  workbook.SheetNames.forEach((sheetName) => {
                    const worksheet = workbook.Sheets[sheetName];
                    const rawHtml = XLSX.utils.sheet_to_html(worksheet, { header: '', footer: '' });

                    htmlContent += `
                    <div class="mb-6 bg-white p-4 rounded-lg shadow-sm border border-line-soft overflow-auto">
                      <h4 class="text-xs font-bold text-teal mb-3 pb-1.5 border-b border-line flex items-center gap-1.5">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Sheet: ${sheetName}
                      </h4>
                      <div class="excel-table-wrapper">${rawHtml}</div>
                    </div>
                  `;
                  });
                  this.$refs.previewXlsxContainer.innerHTML = htmlContent || '<div class="text-center py-12 text-slate-400">Lembar kerja kosong.</div>';
                } catch (err) {
                  this.$refs.previewXlsxContainer.innerHTML = `<div class="text-center py-12 text-red-500 font-semibold">Gagal memuat pratinjau Excel: ${err.message}</div>`;
                } finally {
                  this.previewLoading = false;
                }
              });
            } else {
              this.previewType = 'unsupported';
              this.previewLoading = false;
            }
          },

          closePreview() {
            this.previewModal = false;
            this.previewUrl = '';
            this.previewTitle = '';
            this.previewType = '';
            if (this.$refs.previewDocxContainer) this.$refs.previewDocxContainer.innerHTML = '';
            if (this.$refs.previewXlsxContainer) this.$refs.previewXlsxContainer.innerHTML = '';
          },

          ringSVG(pct, size, stroke, color, bg) {
            const r = (size - stroke) / 2;
            const c = Math.PI * (r * 2);
            const val = pct < 0 ? 0 : (pct > 100 ? 100 : pct);
            const dashoffset = ((100 - val) / 100) * c;
            return `<svg width="${size}" height="${size}" viewBox="0 0 ${size} ${size}" style="transform:rotate(-90deg)">
            <circle r="${r}" cx="${size / 2}" cy="${size / 2}" fill="transparent" stroke="${bg}" stroke-width="${stroke}"></circle>
            <circle r="${r}" cx="${size / 2}" cy="${size / 2}" fill="transparent" stroke="${color}" stroke-width="${stroke}" stroke-dasharray="${c}" stroke-dashoffset="${dashoffset}" stroke-linecap="round"></circle>
          </svg>`;
          }
        }));
      });
    </script>
  @endpush

  @push('styles')
    <style>
      .excel-table-wrapper {
        overflow-x: auto;
        max-width: 100%;
      }

      .excel-table-wrapper table {
        border-collapse: collapse;
        min-width: 100%;
        font-size: 11px;
        font-family: sans-serif;
        color: #1e293b;
      }

      .excel-table-wrapper th,
      .excel-table-wrapper td {
        border: 1px solid #e2e8f0;
        padding: 5px 9px;
        text-align: left;
        min-width: 60px;
      }

      .excel-table-wrapper tr:first-child {
        background-color: #f1f5f9;
        font-weight: 600;
      }

      .excel-table-wrapper tr:nth-child(even) {
        background-color: #f8fafc;
      }

      .excel-table-wrapper tr:hover {
        background-color: #f1f5f9;
      }
    </style>
  @endpush
@endsection