<?php
/**
 * back/order.php — Enregistrement d'une commande
 */
require_once __DIR__ . '/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../cart.php');
}

$adresse   = trim(isset($_POST['adresse']) ? $_POST['adresse'] : '');
$cart_json = trim(isset($_POST['cart_data']) ? $_POST['cart_data'] : '');

if (!$adresse || !$cart_json) {
    setFlash('error', 'Données de commande incomplètes.');
    redirect('../cart.php');
}

$cart = json_decode($cart_json, true);
if (!is_array($cart) || empty($cart)) {
    setFlash('error', 'Panier vide ou invalide.');
    redirect('../cart.php');
}

$db  = getDB();
$uid = currentUser()['id'];

// Calculer le total depuis la base (sécurité)
$total = 0;
foreach ($cart as $item) {
    $stmt = $db->prepare("SELECT prix, stock FROM livres WHERE id = ? LIMIT 1");
    $stmt->execute([(int)$item['id']]);
    $livre = $stmt->fetch();
    if (!$livre || $livre['stock'] < 1) {
        setFlash('error', 'Un article de votre panier est épuisé ou introuvable.');
        redirect('../cart.php');
    }
    $total += $livre['prix'] * max(1, (int)(isset($item['qty']) ? $item['qty'] : 1));
}
$shipping = $total > 50 ? 0 : 7;
$total   += $shipping;

// Créer la commande
$db->beginTransaction();
try {
    $ins = $db->prepare("INSERT INTO commandes (user_id, total, adresse) VALUES (?,?,?)");
    $ins->execute([$uid, $total, $adresse]);
    $cmdId = $db->lastInsertId();

    foreach ($cart as $item) {
        $stmt = $db->prepare("SELECT prix FROM livres WHERE id = ?");
        $stmt->execute([(int)$item['id']]);
        $prix = $stmt->fetchColumn();

        $qty = max(1, (int)(isset($item['qty']) ? $item['qty'] : 1));

        $det = $db->prepare("INSERT INTO commande_details (commande_id, livre_id, quantite, prix_unit) VALUES (?,?,?,?)");
        $det->execute([$cmdId, (int)$item['id'], $qty, $prix]);

        // Décrémenter le stock
        $db->prepare("UPDATE livres SET stock = GREATEST(0, stock - ?) WHERE id = ?")->execute([$qty, (int)$item['id']]);
    }

    $db->commit();
    setFlash('success', "Commande #{$cmdId} passée avec succès ! Total : " . number_format($total, 2) . " DT");
    redirect('../orders.php');
} catch (Exception $e) {
    $db->rollBack();
    setFlash('error', 'Erreur lors de la commande. Veuillez réessayer.');
    redirect('../cart.php');
}
