<?php

// Example URL: http://localhost:8080/fileloader-wrong.php?file=../../../../../etc/passwd%00.png
// https://www.urldecoder.io/

const BASE_PATH = '/var/www/html/images';

// Get the filename from the URL
$filename = $_GET['file'];

// Validate the canonical path
$realPath = realpath(BASE_PATH . '/' . $filename); // /etc/passwd
$basePath = dirname($realPath); // /ect/
if ($basePath !== BASE_PATH) {
    die('Invalid filename');
}

// Validate file extension
if (!str_ends_with($realPath, '.png')) {
    die('Invalid filename');
}

// Load the file
$file = file_get_contents($realPath);

$info = getimagesize($realPath);
header('Content-type: ' . $info['mime']);

// Output the file
echo $file;
