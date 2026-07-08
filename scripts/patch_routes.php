<?php
$file = 'routes/web.php';
$content = file_get_contents($file);
if (strpos($content, 'deleteUpload') === false) {
    $content = str_replace(
        "Route::post('/upload-document', [RegulasiController::class, 'upload'])->name('document.upload');",
        "Route::post('/upload-document', [RegulasiController::class, 'upload'])->name('document.upload');\n    Route::delete('/upload-document/{id}', [RegulasiController::class, 'deleteUpload'])->name('document.delete');",
        $content
    );
    file_put_contents($file, $content);
    echo "Routes updated";
} else {
    echo "Route already exists";
}
