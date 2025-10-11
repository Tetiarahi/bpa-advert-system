<?php

/*
|--------------------------------------------------------------------------
| BPA Advert Management System - Local Development
|--------------------------------------------------------------------------
|
| This file handles requests for local development.
| For production, use proper web server configuration.
|
*/

// Check if we're in local development
if (php_sapi_name() === 'cli-server') {
    // PHP built-in server
    $uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

    // Serve static files directly
    if ($uri !== '/' && file_exists(__DIR__ . '/public' . $uri)) {
        return false;
    }

    // Route everything else to Laravel
    require_once __DIR__ . '/public/index.php';
} else {
    // Regular web server - redirect to public
    $publicPath = __DIR__ . '/public';

    if (is_dir($publicPath)) {
        // Get the request URI
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        // Remove any leading slash
        $path = ltrim($requestUri, '/');

        // If it's the root request, load the public index
        if (empty($path) || $path === 'index.php') {
            require_once $publicPath . '/index.php';
            exit;
        }

        // For other requests, check if file exists in public
        $filePath = $publicPath . '/' . $path;
        if (file_exists($filePath) && is_file($filePath)) {
            // Serve the file from public directory
            $mimeType = mime_content_type($filePath);
            header('Content-Type: ' . $mimeType);
            readfile($filePath);
            exit;
        }

        // For Laravel routes, load the public index
        require_once $publicPath . '/index.php';
        exit;
    } else {
        echo "Error: Laravel public directory not found.";
    }
}
