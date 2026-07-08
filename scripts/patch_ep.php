<?php
$file = 'resources/views/ep/penilaian.blade.php';
$content = file_get_contents($file);

$content = str_replace(
"                  this.uploadHistory.push({
                    url: u.file,
                    filename: u.file.split('/').pop().split('?')[0],
                    uploaded_at: new Date(u.created_at).toLocaleString('id-ID'),
                    uploaded_by: u.user ? u.user.name : 'System',
                    source: 'Baru'
                  });",
"                  this.uploadHistory.push({
                    id: u.id,
                    is_link: false,
                    url: u.file,
                    filename: u.file.split('/').pop().split('?')[0],
                    uploaded_at: new Date(u.created_at).toLocaleString('id-ID'),
                    uploaded_by: u.user ? u.user.name : 'System'
                  });",
$content);

$content = str_replace(
"              this.uploadHistory.push({
                url: item.link,
                filename: item.link.split('/').pop().split('?')[0],
                uploaded_at: '-',
                uploaded_by: '-',
                source: 'Lama'
              });",
"              this.uploadHistory.push({
                id: item.id,
                is_link: true,
                url: item.link,
                filename: item.link.split('/').pop().split('?')[0],
                uploaded_at: '-',
                uploaded_by: '-'
              });",
$content);

$content = str_replace(
"            } catch (err) {
              alert('Terjadi kesalahan saat mengupload berkas.');
            }
            this.uploading = false;
          },

          openPreview(item, title) {",
"            } catch (err) {
              alert('Terjadi kesalahan saat mengupload berkas.');
            }
            this.uploading = false;
          },

          async deleteFile(h) {
            if (!confirm('Apakah Anda yakin ingin menghapus file ini?')) return;
            try {
              const res = await fetch(`/upload-document/\${h.id}`, {
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

          openPreview(item, title) {",
$content);

file_put_contents($file, $content);
