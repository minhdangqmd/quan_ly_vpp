<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function getBaseUrl() {
    // Get the directory of the current script from SCRIPT_NAME
    // Example: /VanPhongPham/login.php -> /VanPhongPham
    // Example: /VanPhongPham/admin/donhang.php -> /VanPhongPham
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    
    // If we're at root (/), return empty
    if ($scriptDir == '/' || $scriptDir == '\\') {
        return '';
    }
    
    // Get the first part of the path (the project folder name)
    $pathParts = explode('/', trim($scriptDir, '/'));
    if (count($pathParts) > 0 && !empty($pathParts[0])) {
        return '/' . $pathParts[0];
    }
    
    return '';
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $baseUrl = getBaseUrl();
        header("Location: " . $baseUrl . "/login.php");
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] != $role) {
        $baseUrl = getBaseUrl();
        header("Location: " . $baseUrl . "/index.php");
        exit();
    }
}

function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'],
            'ten_dang_nhap' => $_SESSION['username'],
            'email' => $_SESSION['email'],
            'vai_tro' => $_SESSION['user_role']
        ];
    }
    return null;
}
?>

