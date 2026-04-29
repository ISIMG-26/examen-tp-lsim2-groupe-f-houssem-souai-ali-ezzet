<?php
/**
 * back/search.php — Recherche AJAX de livres
 */
require_once __DIR__ . '/config.php';
header('Content-Type: application/json; charset=utf-8');

$q = trim(isset($_GET['q']) ? $_GET['q'] : '');
if (strlen($q) < 2) {
    echo json_encode(['results' => []]);
    exit;
}

$db   = getDB();
$like = '%' . $q . '%';
$stmt = $db->prepare("
    SELECT id, titre, auteur, prix, stock, categorie
    FROM livres
    WHERE titre LIKE ? OR auteur LIKE ? OR categorie LIKE ? OR description LIKE ?
    ORDER BY titre
    LIMIT 12
");
$stmt->execute([$like, $like, $like, $like]);
$results = $stmt->fetchAll();

echo json_encode(['results' => $results]);
