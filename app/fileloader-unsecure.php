<?php

// Example URL: http://localhost:8080/fileloader-unsecure.php?file=../../etc/passwd

const BASE_PATH = '/var/www/html/images';

// Get the filename from the URL
$filename = $_GET['file'];

// Load the file
$file = file_get_contents(BASE_PATH . $filename); // /ect/passwd

// Output the file
echo $file;
