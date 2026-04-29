<?php
/**
 * back/update_order.php — Mise à jour statut commande (Admin)
 */
require_once __DIR__ . '/config.php';
requireAdmin();

$id     = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
$statut = trim(isset($_POST['statut']) ? $_POST['statut'] : '');
$allowed = ['en_attente','confirmee','expediee','livree','annulee'];

if ($id > 0 && in_array($statut, $allowed)) {
    $db = getDB();
    $db->prepare("UPDATE commandes SET statut=? WHERE id=?")->execute([$statut, $id]);
    setFlash('success', "Commande #{$id} mise à jour : {$statut}.");
}
redirect('../admin.php?tab=commandes');
