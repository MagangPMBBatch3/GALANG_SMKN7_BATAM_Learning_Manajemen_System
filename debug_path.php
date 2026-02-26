<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\Lesson;
use Illuminate\Support\Facades\Storage;

$lessonId = 66;
$lesson = Lesson::find($lessonId);

if (!$lesson) {
    echo "Lesson $lessonId NOT FOUND\n";
    exit;
}

echo "Lesson ID: " . $lesson->id . "\n";
echo "Media URL: " . $lesson->media_url . "\n";

$path = $lesson->media_url;
// Remove domain
$path = preg_replace('/^https?:\/\/[^\/]+/', '', $path);
// Remove /storage/
$path = preg_replace('/^\/?(storage\/)?/', '', $path);

$fullPath = storage_path('app/public/' . $path);

echo "Resolved Path: " . $fullPath . "\n";

if (file_exists($fullPath)) {
    echo "File EXISTS at path.\n";
    echo "Mime Type: " . mime_content_type($fullPath) . "\n";
    echo "File Size: " . filesize($fullPath) . " bytes\n";
} else {
    echo "File NOT FOUND at path.\n";
    
    // Debug: list files in directory
    $dir = dirname($fullPath);
    if (is_dir($dir)) {
        echo "Directory exists. Files in " . $dir . ":\n";
        $files = scandir($dir);
        foreach ($files as $file) {
            echo " - $file\n";
        }
    } else {
        echo "Directory does NOT exist: $dir\n";
        echo "Public path: " . storage_path('app/public') . "\n";
        if (is_dir(storage_path('app/public'))) {
             echo "Root Storage/Public content:\n";
             print_r(scandir(storage_path('app/public')));
        }
    }
}
