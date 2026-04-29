<?php
$pageTitle = 'Mes commandes';
require_once 'back/config.php';
requireLogin();
require_once 'back/header.php';

$db   = getDB();
$uid  = currentUser()['id'];

$stmt = $db->prepare("
    SELECT c.*, COUNT(cd.id) AS nb_articles
    FROM commandes c
    LEFT JOIN commande_details cd ON cd.commande_id = c.id
    WHERE c.user_id = ?
    GROUP BY c.id
    ORDER BY c.created_at DESC
");
$stmt->execute([$uid]);
$commandes = $stmt->fetchAll();

$statusLabels = [
    'en_attente' => ['label' => 'En attente',  'color' => '#F59E0B'],
    'confirmee'  => ['label' => 'Confirmée',   'color' => '#3B82F6'],
    'expediee'   => ['label' => 'Expédiée',    'color' => '#8B5CF6'],
    'livree'     => ['label' => 'Livrée',      'color' => '#10B981'],
    'annulee'    => ['label' => 'Annulée',     'color' => '#EF4444'],
];
?>

<div class="page-hero">
  <div class="page-hero-inner">
    <div class="breadcrumb">
      <a href="index.php">Accueil</a> › <span>Mes commandes</span>
    </div>
    <h1>Mes commandes</h1>
    <p>Historique de toutes vos commandes passées sur BookNest.</p>
  </div>
</div>

<main>
  <?php if (empty($commandes)): ?>
    <div class="cart-empty">
      <div class="icon">📦</div>
      <h3>Aucune commande</h3>
      <p class="text-muted mt-1">Vous n'avez pas encore passé de commande.</p>
      <a href="index.php" class="btn btn-primary mt-3">Découvrir le catalogue</a>
    </div>
  <?php else: ?>
    <div class="section-header">
      <h2>Historique</h2>
      <span class="text-muted" style="font-size:.85rem"><?= count($commandes) ?> commande(s)</span>
    </div>

    <div style="display:flex;flex-direction:column;gap:1rem">
      <?php foreach ($commandes as $cmd): ?>
        <?php
          $st    = isset($statusLabels[$cmd['statut']]) ? $statusLabels[$cmd['statut']] : ['label' => $cmd['statut'], 'color' => '#999'];
          $date  = date('d/m/Y H:i', strtotime($cmd['created_at']));
          $detailId = 'detail-' . $cmd['id'];
        ?>
        <div style="background:var(--card-bg);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden">
          <!-- Header commande -->
          <div style="display:flex;justify-content:space-between;align-items:center;padding:1.2rem 1.5rem;flex-wrap:wrap;gap:1rem">
            <div>
              <div style="font-family:'Playfair Display',serif;font-weight:700;color:var(--dark)">
                Commande #<?= $cmd['id'] ?>
              </div>
              <div style="font-size:.82rem;color:var(--muted);margin-top:2px"><?= $date ?> — <?= $cmd['nb_articles'] ?> article(s)</div>
            </div>
            <div style="display:flex;align-items:center;gap:1rem">
              <span style="
                background: <?= $st['color'] ?>22;
                color: <?= $st['color'] ?>;
                padding: 4px 12px;
                border-radius: 99px;
                font-size: .78rem;
                font-weight: 700;
              "><?= $st['label'] ?></span>
              <div style="font-family:'Playfair Display',serif;font-size:1.1rem;font-weight:700">
                <?= number_format($cmd['total'], 2, '.', '') ?> DT
              </div>
              <button class="btn btn-outline btn-sm"
                data-accordion="<?= $detailId ?>"
                data-label-open="▶ Détails"
                data-label-close="▼ Masquer">
                ▶ Détails
              </button>
            </div>
          </div>

          <!-- Détails (accordion) -->
          <div id="<?= $detailId ?>" style="display:none;padding:0 1.5rem 1.5rem">
            <hr class="divider" style="margin-top:0">
            <?php
              $dstmt = $db->prepare("
                SELECT cd.*, l.titre, l.auteur
                FROM commande_details cd
                JOIN livres l ON l.id = cd.livre_id
                WHERE cd.commande_id = ?
              ");
              $dstmt->execute([$cmd['id']]);
              $details = $dstmt->fetchAll();
            ?>
            <?php foreach ($details as $d): ?>
              <div style="display:flex;justify-content:space-between;align-items:center;padding:.6rem 0;border-bottom:1px solid var(--border);font-size:.9rem">
                <div>
                  <strong><?= e($d['titre']) ?></strong>
                  <span style="color:var(--muted)"> — <?= e($d['auteur']) ?></span>
                </div>
                <div>
                  x<?= $d['quantite'] ?> &nbsp;
                  <strong><?= number_format($d['prix_unit'] * $d['quantite'], 2, '.','') ?> DT</strong>
                </div>
              </div>
            <?php endforeach; ?>
            <?php if ($cmd['adresse']): ?>
              <p style="font-size:.82rem;color:var(--muted);margin-top:1rem">
                📍 <?= e($cmd['adresse']) ?>
              </p>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</main>

<?php require_once 'back/footer.php'; ?>
