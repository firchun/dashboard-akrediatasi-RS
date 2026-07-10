@extends('layouts.admin')

@section('title', 'File Manager - Akreditasi RS')

@section('content')
  <div x-data="fileManager()" class="px-5 py-6 mx-auto max-w-[1240px]">
    <div class="mb-6 flex items-center justify-between">
      <div>
        <h1 class="text-2xl font-bold text-ink">File Manager</h1>
        <p class="text-sm text-slate-500 mt-1">Kelola seluruh file dokumen unggahan.</p>
      </div>
    </div>

    <!-- Loading State -->
    <div x-show="loading" class="flex items-center justify-center py-12">
      <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-teal"></div>
    </div>

    <div x-show="!loading" style="display: none;">
      <!-- Breadcrumbs -->
      <div
        class="bg-white px-4 py-3 rounded-xl border border-line flex items-center gap-2 text-sm text-slate-600 mb-6 overflow-x-auto whitespace-nowrap shadow-sm">
        <template x-for="(crumb, i) in breadcrumbs" :key="i">
          <div class="flex items-center gap-2">
            <template x-if="i > 0">
              <svg class="text-slate-400" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                stroke-width="2">
                <polyline points="9 18 15 12 9 6"></polyline>
              </svg>
            </template>

            <template x-if="i === breadcrumbs.length - 1">
              <span class="font-bold text-ink" x-text="crumb.title"></span>
            </template>
            <template x-if="i !== breadcrumbs.length - 1">
              <a href="#" @click.prevent="navigate(crumb.query)" class="text-teal hover:underline font-medium"
                x-text="crumb.title"></a>
            </template>
          </div>
        </template>
      </div>

      <!-- Folders -->
      <template x-if="folders.length > 0">
        <div>
          <h2 class="text-sm font-bold text-slate-400 mb-4 uppercase tracking-wider">Folder</h2>
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 mb-8">
            <template x-for="folder in folders" :key="folder.name">
              <a href="#" @click.prevent="navigate(folder.query)"
                class="bg-white border border-line rounded-xl p-4 hover:border-teal/50 hover:shadow-md transition group flex items-start gap-4">
                <div class="p-3 bg-teal/10 rounded-lg text-teal group-hover:bg-teal group-hover:text-white transition">
                  <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
                  </svg>
                </div>
                <div class="flex-1 min-w-0">
                  <div class="font-bold text-ink truncate group-hover:text-teal transition" x-text="folder.name"></div>
                  <div class="text-xs text-slate-500 mt-1 truncate" x-text="folder.desc"></div>
                </div>
              </a>
            </template>
          </div>
        </div>
      </template>

      <!-- Files -->
      <template x-if="files.length > 0">
        <div>
          <h2 class="text-sm font-bold text-slate-400 mb-4 uppercase tracking-wider">Daftar File</h2>
          <div class="bg-white border border-line rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
              <table class="w-full text-left text-[13px]">
                <thead
                  class="bg-slate-50/50 border-b border-line text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                  <tr>
                    <th class="px-5 py-3">Nama File</th>
                    <th class="px-5 py-3">Jenis</th>
                    <th class="px-5 py-3">Uploader</th>
                    <th class="px-5 py-3">Waktu Upload</th>
                    <th class="px-5 py-3 text-right">Aksi</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-line">
                  <template x-for="file in files" :key="file.id">
                    <tr class="hover:bg-slate-50/50 transition">
                      <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                          <div class="text-slate-400" x-html="getFileIcon(file.file)"></div>
                          <div class="font-medium text-ink truncate max-w-[200px] md:max-w-[400px]"
                            :title="file.filename">
                            <span x-text="file.filename"></span>
                            <template x-if="file.is_virtual">
                              <span
                                class="ml-2 inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-100 text-amber-700">Link
                                Manual</span>
                            </template>
                          </div>
                        </div>
                      </td>
                      <td class="px-5 py-3">
                        <span
                          class="inline-block px-2 py-1 rounded bg-slate-100 text-slate-600 font-medium text-xs uppercase"
                          x-text="file.jenis_upload"></span>
                      </td>
                      <td class="px-5 py-3">
                        <template x-if="file.user_name !== 'Sistem' && file.user_name !== 'Link Manual'">
                          <div class="flex items-center gap-2">
                            <template x-if="file.user_avatar">
                              <img :src="file.user_avatar" class="w-5 h-5 rounded-full object-cover">
                            </template>
                            <template x-if="!file.user_avatar">
                              <div
                                class="w-5 h-5 rounded-full bg-slate-200 flex items-center justify-center text-[9px] font-bold text-slate-500"
                                x-text="file.user_name.substring(0, 1)"></div>
                            </template>
                            <span class="text-ink" x-text="file.user_name"></span>
                          </div>
                        </template>
                        <template x-if="file.user_name === 'Sistem' || file.user_name === 'Link Manual'">
                          <span class="text-slate-400 italic" x-text="file.user_name"></span>
                        </template>
                      </td>
                      <td class="px-5 py-3 text-slate-500" x-text="file.created_at"></td>
                      <td class="px-5 py-3 text-right">
                        <a :href="file.file" target="_blank"
                          class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-teal/10 text-teal hover:bg-teal hover:text-white transition font-medium text-xs">
                          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                          </svg>
                          Buka
                        </a>
                      </td>
                    </tr>
                  </template>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </template>

      <template x-if="!folders.length && files.length === 0">
        <div class="bg-white border border-line rounded-xl p-12 text-center">
          <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 text-slate-300 mb-4">
            <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
              <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"></path>
            </svg>
          </div>
          <h3 class="text-ink font-bold text-lg mb-1">Folder Kosong</h3>
          <p class="text-slate-500 text-sm">Tidak ada dokumen di sini.</p>
        </div>
      </template>
    </div>
  </div>

  <script>
    const initFileManager = () => {
      Alpine.data('fileManager', () => ({
        loading: true,
        breadcrumbs: [],
        folders: [],
        files: [],
        currentQuery: {},

        init() {
          // Parse current URL query
          const urlParams = new URLSearchParams(window.location.search);
          for (const [key, value] of urlParams) {
            this.currentQuery[key] = value;
          }

          this.fetchData();

          // Handle back button
          window.addEventListener('popstate', (e) => {
            if (e.state && e.state.query) {
              this.currentQuery = e.state.query;
            } else {
              this.currentQuery = {};
            }
            this.fetchData(false);
          });
        },

        async fetchData(pushState = true) {
          this.loading = true;
          try {
            const queryStr = new URLSearchParams(this.currentQuery).toString();
            const res = await fetch(`{{ route('file.manager.data') }}?${queryStr}`);
            if (!res.ok) throw new Error('Gagal memuat data');

            const data = await res.json();
            this.breadcrumbs = data.breadcrumbs;
            this.folders = data.folders;
            this.files = data.files;

            if (pushState) {
              const newUrl = queryStr ? `${window.location.pathname}?${queryStr}` : window.location.pathname;
              window.history.pushState({ query: this.currentQuery }, '', newUrl);
            }
          } catch (err) {
            // alert(err.message);
          }
          this.loading = false;
        },

        navigate(query) {
          this.currentQuery = query;
          this.fetchData();
        },

        getFileIcon(url) {
          if (!url) return '';
          const isUrl = !url.includes('storage/uploads/');
          const ext = url.split('.').pop().split('?')[0].toLowerCase();

          if (isUrl) {
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.5 1.5"></path><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.5-1.5"></path></svg>`;
          }
          if (['pdf'].includes(ext)) {
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#e11d48" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>`;
          }
          if (['doc', 'docx'].includes(ext)) {
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>`;
          }
          if (['xls', 'xlsx'].includes(ext)) {
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="8" y1="13" x2="16" y2="17"></line><line x1="16" y1="13" x2="8" y2="17"></line></svg>`;
          }
          if (['jpg', 'jpeg', 'png', 'webp'].includes(ext)) {
            return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9333ea" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>`;
          }
          return `<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>`;
        }
      }));
    };
    if (window.Alpine) initFileManager();
    else document.addEventListener('alpine:init', initFileManager);
  </script>
@endsection