<?php
session_start();
require_once __DIR__.'/config/db.php';

function auth() {
    if (!isset($_SESSION['user'])) {
        header('Location: /thesis-system/login.php');
        exit;
    }
}

function role($r) {
    auth();
    if ($_SESSION['user']['role'] !== $r) {
        die('Access denied');
    }
}

function require_profile_complete() {
    if (
        empty($_SESSION['user']['profile_pic']) ||
        empty($_SESSION['user']['signature'])
    ) {
        header("Location: /thesis-system/profile.php");
        exit;
    }
}

function log_action($action) {
    global $pdo;
    $pdo->prepare(
        "INSERT INTO activity_logs(user_id,action) VALUES (?,?)"
    )->execute([$_SESSION['user']['id'], $action]);
}

function validate_pdf($file) {
    return $file['type'] === 'application/pdf' && $file['size'] <= 10*1024*1024;
}
