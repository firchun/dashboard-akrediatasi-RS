<?php
$file = 'app/Http/Controllers/RegulasiController.php';
$content = file_get_contents($file);
$content = preg_replace('/    \{\n        \$reg = Regulasi::findOrFail\(\$id\);\n        \$reg->delete\(\);\n\n        return response\(\)->json\(\[\'success\' => true\]\);\n    \}/', '', $content, 1);
file_put_contents($file, $content);
