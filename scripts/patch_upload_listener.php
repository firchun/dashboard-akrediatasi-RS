<?php
$file = 'resources/views/pokja/index.blade.php';
$content = file_get_contents($file);

$old = <<<PHP
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
                  fetch(`\${window.baseUrl}/ep/\${ep.id}`, {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.csrfToken },
                    body: JSON.stringify(ep)
                  });
                }
              }
            });
PHP;

$new = <<<PHP
            window.addEventListener('upload-success', e => {
              if (e.detail.type === 'regulasi') {
                let reg = this.regulasis.find(r => r.id == e.detail.id);
                if (reg) {
                  if (!reg.upload_files) reg.upload_files = [];
                  if (e.detail.upload) reg.upload_files.push(e.detail.upload);
                  if (e.detail.status) reg.status = e.detail.status;
                }
              } else if (e.detail.type === 'ep') {
                let ep = this.epItems.find(r => r.id == e.detail.id);
                if (ep) {
                  if (!ep.upload_files) ep.upload_files = [];
                  if (e.detail.upload) ep.upload_files.push(e.detail.upload);
                }
              }
            });
PHP;

$content = str_replace($old, $new, $content);
file_put_contents($file, $content);
echo "Patched\n";
