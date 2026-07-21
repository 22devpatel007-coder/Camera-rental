<?php
// Site Configuration
define('SITE_NAME', 'Camera Rental');

// Auto-detect Base URL (works regardless of project folder name)
$base_path = str_replace('\\', '/', dirname(dirname(__FILE__)));
$doc_root = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
define('BASE_URL', str_replace($doc_root, '', $base_path) . '/');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'camera_rental');
?>