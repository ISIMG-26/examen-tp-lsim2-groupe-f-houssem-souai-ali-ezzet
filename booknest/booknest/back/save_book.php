<?php
/**
 * back/save_book.php — Ajout ou modification d'un livre (Admin)
 */
require_once __DIR__ . '/config.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../admin.php?tab=livres');
}

$id          = (int)(isset($_POST['id']) ? $_POST['id'] : 0);
$titre       = trim(isset($_POST['titre']) ? $_POST['titre'] : '');
$auteur      = trim(isset($_POST['auteur']) ? $_POST['auteur'] : '');
$description = trim(isset($_POST['description']) ? $_POST['description'] : '');
$prix        = trim(isset($_POST['prix']) ? $_POST['prix'] : '');
$stock       = (int)(isset($_POST['stock']) ? $_POST['stock'] : 0);
$categorie   = trim(isset($_POST['categorie']) ? $_POST['categorie'] : '');
$editeur     = trim(isset($_POST['editeur']) ? $_POST['editeur'] : '');
$isbn        = trim(isset($_POST['isbn']) ? $_POST['isbn'] : '');
$annee       = (int)(isset($_POST['annee']) ? $_POST['annee'] : 0);

// Validation minimale côté serveur
if (!$titre || !$auteur || !$prix || !$categorie) {
    setFlash('error', 'Champs obligatoires manquants.');
    redirect('../admin.php?tab=livres');
}
if (!is_numeric($prix) || $prix < 0) {
    setFlash('error', 'Prix invalide.');
    redirect('../admin.php?tab=livres');
}

$db = getDB();

if ($id > 0) {
    // UPDATE
    $stmt = $db->prepare("
        UPDATE livres SET titre=?,auteur=?,description=?,prix=?,stock=?,categorie=?,editeur=?,isbn=?,annee=?
        WHERE id=?
    ");
    $stmt->execute([$titre, $auteur, $description, $prix, $stock, $categorie, $editeur, $isbn, $annee ?: null, $id]);
    setFlash('success', "Livre \"$titre\" mis à jour avec succès.");
} else {
    // INSERT
    $stmt = $db->prepare("
        INSERT INTO livres (titre,auteur,description,prix,stock,categorie,editeur,isbn,annee)
        VALUES (?,?,?,?,?,?,?,?,?)
    ");
    $stmt->execute([$titre, $auteur, $description, $prix, $stock, $categorie, $editeur, $isbn, $annee ?: null]);
    setFlash('success', "Livre \"$titre\" ajouté avec succès.");
}

redirect('../admin.php?tab=livres');
