<?php

// Example URL: http://localhost:8080/fileloader-wrong.php?file=1

const BASE_PATH = '/var/www/html/images';

// Get the index from the URL
$fileIndex = $_GET['file'];

// Connect to SQLite database
$db = new SQLite3('fileloader.db');

// Get the filename from the database
$statement = $db->prepare('SELECT filename FROM files WHERE id = :id');
$statement->bindValue(':id', $fileIndex);
$result = $statement->execute();
$filename = $result->fetchArray()[0];

// Load the file
$file = file_get_contents(BASE_PATH . $filename);

// Output the file
echo $file;
