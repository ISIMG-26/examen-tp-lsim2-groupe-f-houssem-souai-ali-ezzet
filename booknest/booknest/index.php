<?php
$pageTitle = 'Catalogue';
require_once 'back/header.php';

$db  = getDB();
$cat = isset($_GET['cat']) ? $_GET['cat'] : '';

// Récupérer toutes les catégories
$cats = $db->query("SELECT DISTINCT categorie FROM livres ORDER BY categorie")->fetchAll();

// Requête livres
if ($cat) {
    $stmt = $db->prepare("SELECT * FROM livres WHERE categorie = ? ORDER BY titre");
    $stmt->execute([$cat]);
} else {
    $stmt = $db->query("SELECT * FROM livres ORDER BY titre");
}
$livres = $stmt->fetchAll();

// Couleurs de couvertures
$colors = ['#8B5E3C','#1A1208','#C8472B','#5C4033','#4A3728','#2C3E50','#6D4C41'];
?>

<!-- HERO -->
<div class="hero">
  <div class="hero-content">
    <h1>La lecture, <em>une aventure</em> sans fin.</h1>
    <p>Des milliers de titres à portée de main. Littérature, science-fiction, histoire, informatique et bien plus.</p>
    <div class="hero-actions">
      <a href="#catalogue" class="btn btn-primary">Parcourir le catalogue</a>
      <?php if (!isLoggedIn()): ?>
        <a href="auth.php?tab=register" class="btn btn-secondary">Créer un compte</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<main id="catalogue">

  <!-- RECHERCHE AJAX -->
  <div class="search-section">
    <h2 style="margin-bottom:1rem">Rechercher un livre</h2>
    <div class="search-bar">
      <input type="text" id="search-input" placeholder="Titre, auteur, mot-clé…" autocomplete="off">
      <button id="search-btn">Rechercher</button>
    </div>
    <div id="search-results"></div>
  </div>

  <!-- FILTRES PAR CATEGORIE -->
  <div class="section-header">
    <h2>Tous nos livres <?= $cat ? '— <em style="font-size:1rem;color:var(--brown)">' . e($cat) . '</em>' : '' ?></h2>
    <span class="text-muted" style="font-size:.85rem"><?= count($livres) ?> titre(s)</span>
  </div>

  <div class="filters">
    <span class="filter-label">Filtrer :</span>
    <button class="filter-btn <?= !$cat ? 'active' : '' ?>" data-category="all">Tous</button>
    <?php foreach ($cats as $c): ?>
      <button class="filter-btn <?= $cat === $c['categorie'] ? 'active' : '' ?>"
              data-category="<?= e($c['categorie']) ?>">
        <?= e($c['categorie']) ?>
      </button>
    <?php endforeach; ?>
  </div>

  <!-- GRILLE DE PRODUITS -->
  <?php if (empty($livres)): ?>
    <div class="cart-empty">
      <div class="icon">📭</div>
      <h3>Aucun livre disponible</h3>
      <p class="text-muted">Revenez bientôt, le catalogue est en cours de mise à jour.</p>
    </div>
  <?php else: ?>
    <div class="products-grid">
      <?php foreach ($livres as $i => $livre): ?>
        <?php
          $color   = $colors[$livre['id'] % count($colors)];
          $inStock = $livre['stock'] > 0;
          $isNew   = $livre['id'] >= (max(array_column($livres,'id')) - 2);
        ?>
        <div class="product-card" data-category="<?= e($livre['categorie']) ?>">
          <div class="book-cover" style="background: linear-gradient(135deg, <?= $color ?> 0%, #1A1208 100%)">
            <div class="book-stripe"></div>
            <?php if ($isNew): ?><div class="badge-new">Nouveau</div><?php endif; ?>
            <div class="book-cover-inner">
              <div class="book-title-cover"><?= e($livre['titre']) ?></div>
              <div class="book-author-cover"><?= e($livre['auteur']) ?></div>
            </div>
          </div>
          <div class="card-body">
            <div class="card-category"><?= e(isset($livre['categorie']) ? $livre['categorie'] : 'Divers') ?></div>
            <div class="card-title"><?= e($livre['titre']) ?></div>
            <div class="card-author"><?= e($livre['auteur']) ?></div>
            <?php if ($livre['description']): ?>
              <p style="font-size:.78rem;color:var(--muted);margin-bottom:.8rem;
                         display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                <?= e($livre['description']) ?>
              </p>
            <?php endif; ?>
            <div class="card-footer">
              <div>
                <div class="price"><?= number_format($livre['prix'], 2, '.', '') ?> DT</div>
                <div class="stock-badge <?= $inStock ? '' : 'out' ?>">
                  <?= $inStock ? '✓ En stock (' . $livre['stock'] . ')' : '✗ Épuisé' ?>
                </div>
              </div>
              <?php if ($inStock): ?>
                <button class="btn btn-primary btn-sm add-to-cart"
                  data-id="<?= (int)$livre['id'] ?>"
                  data-title="<?= htmlspecialchars($livre['titre'], ENT_QUOTES) ?>"
                  data-author="<?= htmlspecialchars($livre['auteur'], ENT_QUOTES) ?>"
                  data-price="<?= (float)$livre['prix'] ?>">
                  + Panier
                </button>
              <?php else: ?>
                <button class="btn btn-outline btn-sm" disabled>Épuisé</button>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</main>

<?php require_once 'back/footer.php'; ?>
