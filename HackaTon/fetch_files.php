<?php
header("Content-Type: application/json");

$dir = "uploads/";
$files = array();

if (is_dir($dir)) {
    $files = array_values(array_diff(scandir($dir), array('.', '..')));

    $fileList = [];
    foreach ($files as $file) {
        $fileList[] = [
            "name" => $file,
            "url" => $dir . $file
        ];
    }
    echo json_encode($fileList);
} else {
    echo json_encode([]);
}
?>