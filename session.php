<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


function getBaseUrl() {

    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);


    $scriptDir = str_replace('\\', '/', $scriptDir);
    $scriptDir = rtrim($scriptDir, '/');


    if ($scriptDir === '' || $scriptDir === '/') {
        return '';
    }

    return $scriptDir;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $baseUrl = getBaseUrl();
        header("Location: " . ($baseUrl === '' ? '/login.php' : $baseUrl . "/login.php"));
        exit();
    }
}

function requireRole($role) {
    requireLogin();
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
        $baseUrl = getBaseUrl();
        header("Location: " . ($baseUrl === '' ? '/index.php' : $baseUrl . "/index.php"));
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
