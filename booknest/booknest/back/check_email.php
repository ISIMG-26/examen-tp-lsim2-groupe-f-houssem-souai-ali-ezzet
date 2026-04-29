<?php
/**
 * back/check_email.php — Vérification AJAX disponibilité email
 */
require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

$email = trim(isset($_GET['email']) ? $_GET['email'] : '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['taken' => false]);
    exit;
}

$db   = getDB();
$stmt = $db->prepare("SELECT id FROM utilisateurs WHERE email = ? LIMIT 1");
$stmt->execute([$email]);
$exists = (bool)$stmt->fetch();

echo json_encode(['taken' => $exists]);
