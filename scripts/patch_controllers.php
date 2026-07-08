<?php
// Patch RegulasiController
$file = 'app/Http/Controllers/RegulasiController.php';
$content = file_get_contents($file);
$replacement = <<<PHP
        if (isset(\$data['link']) && \$data['link'] !== '' && \$data['link'] !== \$reg->link && !empty(\$reg->link)) {
            \App\Models\UploadFile::create([
                'jenis_upload' => 'regulasi',
                'file' => \$data['link'],
                'id_user' => auth()->id(),
                'related_id' => \$reg->id
            ]);
            unset(\$data['link']);
        }

        if (auth()->user()->role === 'verifikator' || auth()->user()->isAdmin()) {
PHP;
$content = str_replace("        if (auth()->user()->role === 'verifikator' || auth()->user()->isAdmin()) {", $replacement, $content);
file_put_contents($file, $content);

// Patch EpItemController
$file = 'app/Http/Controllers/EpItemController.php';
$content = file_get_contents($file);
$replacement = <<<PHP
        \$newLink = \$request->link;
        if (\$newLink && \$newLink !== \$item->link && !empty(\$item->link)) {
            \App\Models\UploadFile::create([
                'jenis_upload' => 'ep',
                'file' => \$newLink,
                'id_user' => auth()->id(),
                'related_id' => \$item->id
            ]);
            \$request->merge(['link' => \$item->link]); // prevent overwrite
        }

        \$item->update([
PHP;
$content = str_replace("        \$item->update([", $replacement, $content);
file_put_contents($file, $content);

echo "Controllers patched\n";
