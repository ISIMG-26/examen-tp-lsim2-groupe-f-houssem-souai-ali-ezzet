<?php
$pageTitle = 'Administration';
require_once 'back/config.php';
requireAdmin();
require_once 'back/header.php';

$db  = getDB();
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'dashboard';

// Stats
$totalLivres    = $db->query("SELECT COUNT(*) FROM livres")->fetchColumn();
$totalUsers     = $db->query("SELECT COUNT(*) FROM utilisateurs WHERE role='client'")->fetchColumn();
$totalCommandes = $db->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$totalCA        = $db->query("SELECT COALESCE(SUM(total),0) FROM commandes WHERE statut='livree'")->fetchColumn();

// Livres
$livres = $db->query("SELECT * FROM livres ORDER BY created_at DESC")->fetchAll();

// Commandes
$commandes = $db->query("
    SELECT c.*, u.prenom, u.nom
    FROM commandes c
    JOIN utilisateurs u ON u.id = c.user_id
    ORDER BY c.created_at DESC
    LIMIT 50
")->fetchAll();

// Utilisateurs
$users = $db->query("SELECT * FROM utilisateurs ORDER BY created_at DESC")->fetchAll();

$cats = ['Littérature','Science-Fiction','Fantasy','Policier','Histoire','Philosophie','Informatique','Développement Personnel','Autre'];
?>

<div class="page-hero">
  <div class="page-hero-inner">
    <h1>Administration</h1>
    <p>Gérez le catalogue, les commandes et les utilisateurs.</p>
  </div>
</div>

<main>
  <div class="admin-layout">

    <!-- Sidebar -->
    <aside class="admin-sidebar">
      <h3>📚 BookNest Admin</h3>
      <a href="admin.php?tab=dashboard" class="admin-nav-item <?= $tab==='dashboard'?'active':'' ?>">📊 Tableau de bord</a>
      <a href="admin.php?tab=livres"    class="admin-nav-item <?= $tab==='livres'   ?'active':'' ?>">📖 Livres</a>
      <a href="admin.php?tab=commandes" class="admin-nav-item <?= $tab==='commandes'?'active':'' ?>">🛒 Commandes</a>
      <a href="admin.php?tab=users"     class="admin-nav-item <?= $tab==='users'    ?'active':'' ?>">👥 Utilisateurs</a>
      <hr style="border-color:rgba(255,255,255,.1);margin:1rem 0">
      <a href="index.php" class="admin-nav-item">← Retour au site</a>
    </aside>

    <!-- Contenu -->
    <div class="admin-content">

      <?php if ($tab === 'dashboard'): ?>
        <!-- DASHBOARD -->
        <div class="stats-row">
          <div class="stat-card">
            <div class="stat-label">Livres</div>
            <div class="stat-value"><?= $totalLivres ?></div>
            <div class="stat-sub">dans le catalogue</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Clients</div>
            <div class="stat-value"><?= $totalUsers ?></div>
            <div class="stat-sub">inscrits</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Commandes</div>
            <div class="stat-value"><?= $totalCommandes ?></div>
            <div class="stat-sub">passées</div>
          </div>
          <div class="stat-card">
            <div class="stat-label">Chiffre d'affaires</div>
            <div class="stat-value"><?= number_format($totalCA, 0, '.', ' ') ?></div>
            <div class="stat-sub">DT (livrées)</div>
          </div>
        </div>
        <div class="section-header"><h2>Dernières commandes</h2></div>
        <table class="data-table">
          <thead><tr><th>#</th><th>Client</th><th>Total</th><th>Statut</th><th>Date</th></tr></thead>
          <tbody>
            <?php foreach (array_slice($commandes, 0, 5) as $cmd): ?>
              <tr>
                <td>#<?= $cmd['id'] ?></td>
                <td><?= e($cmd['prenom'] . ' ' . $cmd['nom']) ?></td>
                <td><?= number_format($cmd['total'], 2) ?> DT</td>
                <td><span class="tag tag-brown"><?= e($cmd['statut']) ?></span></td>
                <td><?= date('d/m/Y', strtotime($cmd['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      <?php elseif ($tab === 'livres'): ?>
        <!-- LIVRES -->
        <div class="section-header">
          <h2>Gestion des livres</h2>
          <button class="btn btn-primary btn-sm" onclick="openModal('modal-book')">+ Ajouter un livre</button>
        </div>

        <table class="data-table">
          <thead><tr><th>Titre</th><th>Auteur</th><th>Catégorie</th><th>Prix</th><th>Stock</th><th>Actions</th></tr></thead>
          <tbody>
            <?php foreach ($livres as $l): ?>
              <tr>
                <td><strong><?= e($l['titre']) ?></strong></td>
                <td><?= e($l['auteur']) ?></td>
                <td><span class="tag tag-brown"><?= e($l['categorie']) ?></span></td>
                <td><?= number_format($l['prix'],2) ?> DT</td>
                <td><?= $l['stock'] > 0 ? '<span class="tag tag-green">' . $l['stock'] . '</span>' : '<span class="tag tag-red">0</span>' ?></td>
                <td>
                  <div class="table-actions">
                    <button class="btn btn-outline btn-sm" onclick="editBook(<?= htmlspecialchars(json_encode($l)) ?>)">✏️</button>
                    <form method="POST" action="back/delete_book.php" style="display:inline"
                          onsubmit="return confirm('Supprimer ce livre ?')">
                      <input type="hidden" name="id" value="<?= $l['id'] ?>">
                      <button class="btn btn-danger btn-sm">🗑</button>
                    </form>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      <?php elseif ($tab === 'commandes'): ?>
        <!-- COMMANDES -->
        <div class="section-header"><h2>Toutes les commandes</h2></div>
        <table class="data-table">
          <thead><tr><th>#</th><th>Client</th><th>Total</th><th>Adresse</th><th>Statut</th><th>Date</th><th>Action</th></tr></thead>
          <tbody>
            <?php foreach ($commandes as $cmd): ?>
              <tr>
                <td>#<?= $cmd['id'] ?></td>
                <td><?= e($cmd['prenom'] . ' ' . $cmd['nom']) ?></td>
                <td><?= number_format($cmd['total'], 2) ?> DT</td>
                <td style="max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?= e(isset($cmd['adresse']) ? $cmd['adresse'] : '—') ?></td>
                <td>
                  <form method="POST" action="back/update_order.php">
                    <input type="hidden" name="id" value="<?= $cmd['id'] ?>">
                    <select name="statut" onchange="this.form.submit()" style="padding:4px 8px;border:1.5px solid var(--border);border-radius:4px;background:var(--cream);font-size:.82rem">
                      <?php foreach (['en_attente','confirmee','expediee','livree','annulee'] as $s): ?>
                        <option value="<?= $s ?>" <?= $cmd['statut']===$s?'selected':'' ?>><?= $s ?></option>
                      <?php endforeach; ?>
                    </select>
                  </form>
                </td>
                <td><?= date('d/m/Y', strtotime($cmd['created_at'])) ?></td>
                <td>
                  <form method="POST" action="back/delete_order.php" onsubmit="return confirm('Supprimer la commande #<?= $cmd['id'] ?> ?')">
                    <input type="hidden" name="id" value="<?= $cmd['id'] ?>">
                    <button class="btn btn-danger btn-sm">🗑</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      <?php elseif ($tab === 'users'): ?>
        <!-- UTILISATEURS -->
        <div class="section-header"><h2>Utilisateurs inscrits</h2></div>
        <table class="data-table">
          <thead><tr><th>#</th><th>Nom</th><th>Email</th><th>Téléphone</th><th>Rôle</th><th>Inscrit le</th></tr></thead>
          <tbody>
            <?php foreach ($users as $u): ?>
              <tr>
                <td><?= $u['id'] ?></td>
                <td><?= e($u['prenom'] . ' ' . $u['nom']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><?= e(isset($u['telephone']) ? $u['telephone'] : '—') ?></td>
                <td><span class="tag <?= $u['role']==='admin' ? 'tag-red' : 'tag-brown' ?>"><?= $u['role'] ?></span></td>
                <td><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php endif; ?>

    </div>
  </div>
</main>

<!-- MODAL AJOUTER/ÉDITER LIVRE -->
<div class="modal-overlay" id="modal-book">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modal-book-title">Ajouter un livre</h3>
      <button class="modal-close">✕</button>
    </div>
    <form method="POST" action="back/save_book.php" id="book-form" novalidate>
      <input type="hidden" name="id" id="book-id">
      <div class="form-row">
        <div class="form-group">
          <label for="book-titre">Titre *</label>
          <input type="text" id="book-titre" name="titre" placeholder="Titre du livre">
          <span class="field-error" id="book-titre-error"></span>
        </div>
        <div class="form-group">
          <label for="book-auteur">Auteur *</label>
          <input type="text" id="book-auteur" name="auteur" placeholder="Nom de l'auteur">
          <span class="field-error" id="book-auteur-error"></span>
        </div>
      </div>
      <div class="form-group">
        <label for="book-description">Description</label>
        <textarea id="book-description" name="description" rows="3" placeholder="Résumé du livre…"></textarea>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="book-prix">Prix (DT) *</label>
          <input type="text" id="book-prix" name="prix" placeholder="12.90">
          <span class="field-error" id="book-prix-error"></span>
        </div>
        <div class="form-group">
          <label for="book-stock">Stock *</label>
          <input type="number" id="book-stock" name="stock" min="0" placeholder="0">
          <span class="field-error" id="book-stock-error"></span>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="book-categorie">Catégorie *</label>
          <select id="book-categorie" name="categorie">
            <option value="">-- Choisir --</option>
            <?php foreach ($cats as $c): ?>
              <option value="<?= e($c) ?>"><?= e($c) ?></option>
            <?php endforeach; ?>
          </select>
          <span class="field-error" id="book-categorie-error"></span>
        </div>
        <div class="form-group">
          <label for="book-editeur">Éditeur</label>
          <input type="text" id="book-editeur" name="editeur" placeholder="Gallimard…">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label for="book-isbn">ISBN</label>
          <input type="text" id="book-isbn" name="isbn" placeholder="978-…">
        </div>
        <div class="form-group">
          <label for="book-annee">Année</label>
          <input type="number" id="book-annee" name="annee" placeholder="2024" min="1000" max="2099">
        </div>
      </div>
      <button type="submit" class="btn btn-primary btn-full">Enregistrer</button>
    </form>
  </div>
</div>

<script>
function editBook(book) {
  document.getElementById('modal-book-title').textContent = 'Modifier le livre';
  document.getElementById('book-id').value          = book.id;
  document.getElementById('book-titre').value       = book.titre;
  document.getElementById('book-auteur').value      = book.auteur;
  document.getElementById('book-description').value = book.description || '';
  document.getElementById('book-prix').value        = book.prix;
  document.getElementById('book-stock').value       = book.stock;
  document.getElementById('book-categorie').value   = book.categorie || '';
  document.getElementById('book-editeur').value     = book.editeur || '';
  document.getElementById('book-isbn').value        = book.isbn || '';
  document.getElementById('book-annee').value       = book.annee || '';
  openModal('modal-book');
}
document.querySelector('[onclick="openModal(\'modal-book\')"]')?.addEventListener('click', () => {
  document.getElementById('modal-book-title').textContent = 'Ajouter un livre';
  document.getElementById('book-form').reset();
  document.getElementById('book-id').value = '';
});
</script>

<?php require_once 'back/footer.php'; ?>
