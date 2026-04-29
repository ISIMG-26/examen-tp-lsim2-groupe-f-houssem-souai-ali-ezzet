<!-- FOOTER -->
<footer>
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="logo">Book<span style="color:var(--accent)">Nest</span></div>
        <p>Votre librairie en ligne. Découvrez des milliers de livres en littérature, science-fiction, histoire, informatique et bien plus encore.</p>
      </div>
      <div class="footer-col">
        <h4>Navigation</h4>
        <ul>
          <li><a href="index.php">Catalogue</a></li>
          <li><a href="cart.php">Panier</a></li>
          <li><a href="auth.php">Mon compte</a></li>
          <li><a href="orders.php">Mes commandes</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h4>Catégories</h4>
        <ul>
          <li><a href="index.php?cat=Littérature">Littérature</a></li>
          <li><a href="index.php?cat=Science-Fiction">Science-Fiction</a></li>
          <li><a href="index.php?cat=Fantasy">Fantasy</a></li>
          <li><a href="index.php?cat=Informatique">Informatique</a></li>
          <li><a href="index.php?cat=Histoire">Histoire</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?= date('Y') ?> BookNest — Tous droits réservés.</span>
      <span>Paiement sécurisé &nbsp;|&nbsp; Livraison rapide</span>
    </div>
  </div>
</footer>

<div id="cart-notification"></div>
<script src="js/main.js"></script>
</body>
</html>
