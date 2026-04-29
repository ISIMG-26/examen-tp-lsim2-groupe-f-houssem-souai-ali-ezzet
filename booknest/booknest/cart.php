<?php
$pageTitle = 'Panier';
require_once 'back/header.php';
?>

<div class="page-hero">
  <div class="page-hero-inner">
    <div class="breadcrumb">
      <a href="index.php">Accueil</a> › <span>Panier</span>
    </div>
    <h1>Mon Panier</h1>
    <p>Vérifiez vos articles avant de passer commande.</p>
  </div>
</div>

<main>
  <div class="cart-layout">

    <!-- Articles -->
    <div>
      <div class="section-header">
        <h2>Articles</h2>
        <button class="btn btn-outline btn-sm" onclick="localStorage.removeItem('bn_cart');renderCartPage()">
          Vider le panier
        </button>
      </div>
      <div id="cart-items">
        <!-- Rendu par JS -->
        <div class="loading-placeholder"><span class="spinner"></span> Chargement…</div>
      </div>
    </div>

    <!-- Résumé commande -->
    <div id="cart-summary">
      <div class="order-summary">
        <h3>Résumé de la commande</h3>

        <div class="summary-row">
          <span>Articles (<span id="summary-count">0</span>)</span>
          <span id="summary-subtotal">0.00 DT</span>
        </div>
        <div class="summary-row">
          <span>Livraison</span>
          <span id="summary-shipping">7.00 DT</span>
        </div>
        <div class="summary-total">
          <span>Total</span>
          <span id="summary-total">0.00 DT</span>
        </div>

        <div class="alert alert-info mt-3" style="display:block;font-size:.8rem">
          🎉 Livraison gratuite dès 50 DT d'achat !
        </div>

        <?php if (isLoggedIn()): ?>
          <!-- Formulaire de commande -->
          <hr class="divider">
          <form method="POST" action="back/order.php" id="order-form" onsubmit="return prepareOrder()">
            <div class="form-group">
              <label for="adresse">Adresse de livraison *</label>
              <textarea id="adresse" name="adresse" rows="3"
                        placeholder="N° rue, Ville, Code postal…"
                        style="resize:vertical"></textarea>
              <span class="field-error" id="adresse-error"></span>
            </div>
            <input type="hidden" name="cart_data" id="cart_data">
            <button type="submit" class="btn btn-primary btn-full">
              Confirmer la commande
            </button>
          </form>
        <?php else: ?>
          <a href="auth.php" class="btn btn-primary btn-full mt-3">
            Connectez-vous pour commander
          </a>
        <?php endif; ?>
      </div>
    </div>

  </div>
</main>

<script>
function prepareOrder() {
  const adresse = document.getElementById('adresse');
  const errEl   = document.getElementById('adresse-error');
  if (!adresse.value.trim()) {
    adresse.classList.add('error');
    errEl.textContent = 'L\'adresse de livraison est obligatoire.';
    errEl.classList.add('visible');
    return false;
  }
  const cart = Cart.get();
  if (cart.length === 0) {
    alert('Votre panier est vide.');
    return false;
  }
  document.getElementById('cart_data').value = JSON.stringify(cart);
  return true;
}
</script>

<?php require_once 'back/footer.php'; ?>
