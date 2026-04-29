<?php
/**
 * BookNest - Header commun
 * Inclure en haut de chaque page : require_once 'back/header.php';
 * Définir $pageTitle avant d'inclure.
 */
require_once __DIR__ . '/config.php';
sessionStart();
$user  = currentUser();
$flash = getFlash();
$cartCount = 0; // mis à jour côté JS
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e(isset($pageTitle) ? $pageTitle : 'BookNest') ?> — Librairie en ligne</title>
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📚</text></svg>">
</head>
<body>

<header>
  <div class="nav-inner">
    <a href="index.php" class="logo">Book<span>Nest</span></a>
    <nav>
      <ul>
        <li><a href="index.php">Catalogue</a></li>
        <li><a href="cart.php">
          Panier
          <span id="cart-count" style="
            display:none;
            background:#C8472B;color:#fff;
            border-radius:99px;padding:1px 7px;
            font-size:.7rem;font-weight:700;margin-left:4px;
          ">0</span>
        </a></li>
        <?php if ($user): ?>
          <?php if ($user['role'] === 'admin'): ?>
            <li><a href="admin.php">Administration</a></li>
          <?php endif; ?>
          <li><a href="orders.php">Mes commandes</a></li>
          <li><a href="back/logout.php" style="color:var(--border)">Déconnexion</a></li>
          <li style="color:var(--border);font-size:.85rem">👤 <?= e($user['prenom']) ?></li>
        <?php else: ?>
          <li><a href="auth.php" class="btn-nav">Connexion</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </div>
</header>

<?php if ($flash): ?>
<div style="background:<?= $flash['type']==='success'?'#edf7ee':'#fdecea' ?>;color:<?= $flash['type']==='success'?'#2E7D32':'#C8472B' ?>;padding:12px 5%;font-size:.9rem;border-bottom:2px solid currentColor;">
  <?= e($flash['msg']) ?>
</div>
<?php endif; ?>
