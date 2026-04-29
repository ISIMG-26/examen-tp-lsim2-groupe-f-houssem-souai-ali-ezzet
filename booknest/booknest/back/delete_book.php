<?php
/**
 * back/delete_book.php — Suppression d'un livre (Admin)
 */
require_once __DIR__ . '/config.php';
requireAdmin();

$id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
if ($id > 0) {
    $db = getDB();
    $stmt = $db->prepare("SELECT titre FROM livres WHERE id=?");
    $stmt->execute([$id]);
    $livre = $stmt->fetch();
    if ($livre) {
        $db->prepare("DELETE FROM livres WHERE id=?")->execute([$id]);
        setFlash('success', "Livre \"{$livre['titre']}\" supprimé.");
    } else {
        setFlash('error', "Livre introuvable.");
    }
}
redirect('../admin.php?tab=livres');
