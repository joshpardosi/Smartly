<?php
ini_set('Display_errors',1);
ini_set('Display_startup_errors',1);
error_reporting(E_ALL);
session_start(); // Memulai session

// Periksa apakah pengguna sudah login


// Mendapatkan nama file dari parameter GET
$file = $_GET['file'];

// Tentukan path ke folder uploads
$filepath = 'uploads/' . basename($file);

// Periksa apakah file ada dan bisa diakses
if (file_exists($filepath)) {
//  header('Content-Description: File Transfer');
    // header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
    // header('Expires: 0');
    // header('Cache-Control: must-revalidate');
    // header('Pragma: public');
    // header('Content-Length: ' . filesize($filepath));
    flush();
    readfile($filepath); 
    exit();
} else {
    echo "File tidak ditemukan.";
}
?>