<?php
$file = 'app/Http/Controllers/RegulasiController.php';
$content = file_get_contents($file);

$old = <<<PHP
    public function deleteUpload(Request \$request, \$id)
    {
        \$isLink = \$request->boolean('is_link');
        \$type = \$request->input('type');

        if (\$isLink) {
            if (\$type === 'ep') {
                \$item = \App\Models\EpItem::find(\$id);
            } else {
                \$item = Regulasi::find(\$id);
            }
            if (\$item) {
                \$item->update(['link' => null]);
            }
        } else {
            \$upload = \App\Models\UploadFile::find(\$id);
            if (\$upload) {
                \$upload->delete();
            }
        }

        return response()->json(['success' => true]);
    }
PHP;

$new = <<<PHP
    public function deleteUpload(Request \$request, \$id)
    {
        \$isLink = \$request->boolean('is_link');
        \$type = \$request->input('type');
        \$item = null;
        \$pokjaCode = '';

        if (\$isLink) {
            if (\$type === 'ep') {
                \$item = \App\Models\EpItem::with(['uploadFiles', 'pokja'])->find(\$id);
            } else {
                \$item = Regulasi::with(['uploadFiles', 'pokja'])->find(\$id);
            }
            if (\$item) {
                \$item->update(['link' => null]);
                \$item->refresh();
                \$pokjaCode = \$item->pokja->code ?? '';
            }
        } else {
            \$upload = \App\Models\UploadFile::find(\$id);
            if (\$upload) {
                if (\$type === 'ep') {
                    \$item = \App\Models\EpItem::with(['uploadFiles', 'pokja'])->find(\$upload->related_id);
                } else {
                    \$item = Regulasi::with(['uploadFiles', 'pokja'])->find(\$upload->related_id);
                }
                \$upload->delete();
                if (\$item) {
                    \$item->load('uploadFiles');
                    \$item->upload_files = \$item->uploadFiles; // Ensure frontend gets the array as 'upload_files' if it expects it
                    \$pokjaCode = \$item->pokja->code ?? '';
                }
            }
        }

        if (\$item) {
            // Append upload_files explicitly since model returns uploadFiles by default
            \$item->setAttribute('upload_files', \$item->uploadFiles);
        }

        return response()->json([
            'success' => true,
            'item' => \$item,
            'pokja_code' => \$pokjaCode
        ]);
    }
PHP;

$content = str_replace($old, $new, $content);
file_put_contents($file, $content);
echo "Patched\n";
