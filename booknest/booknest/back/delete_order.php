<?php
/**
 * back/delete_order.php — Suppression d'une commande (Admin)
 */
require_once __DIR__ . '/config.php';
requireAdmin();

$id = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
if ($id > 0) {
    $db = getDB();
    $db->prepare("DELETE FROM commandes WHERE id=?")->execute([$id]);
    setFlash('success', "Commande #{$id} supprimée.");
} else {
    setFlash('error', "ID invalide.");
}
redirect('../admin.php?tab=commandes');
