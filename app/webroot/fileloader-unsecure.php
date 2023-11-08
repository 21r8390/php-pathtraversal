<?php

// Example URL: http://localhost:8080/fileloader-unsecure.php?file=../../etc/passwd

const BASE_PATH = '/var/www/html/images/';

// Get the filename from the URL
$filename = $_GET['file'];

// Load the file
$filename = BASE_PATH . $filename;
$file = file_get_contents($filename);

$info = getimagesize($filename);
header('Content-type: ' . $info['mime']);

// Output the file
echo $file;
