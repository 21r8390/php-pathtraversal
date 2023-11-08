<?php

// Example URL: http://localhost:8080/fileloader-wrong.php?file=%2e%2e%2f%2e%2e%2f%2e%2e%2fetc/passwd%00.png

// https://www.urldecoder.io/
// http://localhost:8080/fileloader-wrong.php?file=../../../etc/passwd

const BASE_PATH = '/var/www/html/images/';

// Get the filename from the URL
$filename = $_GET['file'];

// Validate backslashes
if (strpos($filename, '\\') !== false || strpos($filename, '/') !== false) {
    die('Invalid filename');
}

// Validate file extension
if (!str_ends_with($filename, '.png')) {
    die('Invalid filename');
}

// Load the file
$filename = BASE_PATH . $filename;
$file = file_get_contents($filename);

$info = getimagesize($filename);
header('Content-type: ' . $info['mime']);

// Output the file
echo $file;
