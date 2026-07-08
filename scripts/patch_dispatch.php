<?php
$file = 'resources/views/pokja/index.blade.php';
$content = file_get_contents($file);

$old = "this.\$dispatch('upload-success', { type: this.uploadType, id: this.uploadId, url: data.url, status: data.status, history: data.history });";
$new = "this.\$dispatch('upload-success', { type: this.uploadType, id: this.uploadId, url: data.url, status: data.status, upload: data.upload });";

$content = str_replace($old, $new, $content);
file_put_contents($file, $content);
echo "Patched\n";
