<?php
$pageTitle = 'Connexion / Inscription';
require_once 'back/config.php';

// Rediriger si déjà connecté
if (isLoggedIn()) {
    redirect('index.php');
}

require_once 'back/header.php';

$loginError = '';
$regError   = '';
$regSuccess = '';

/* ---- TRAITEMENT CONNEXION ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email    = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password = trim(isset($_POST['password']) ? $_POST['password'] : '');

    if ($email && $password) {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            sessionStart();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['prenom']  = $user['prenom'];
            $_SESSION['nom']     = $user['nom'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];
            setFlash('success', 'Bienvenue, ' . $user['prenom'] . ' !');
            redirect('index.php');
        } else {
            $loginError = 'Email ou mot de passe incorrect.';
        }
    } else {
        $loginError = 'Veuillez remplir tous les champs.';
    }
}

/* ---- TRAITEMENT INSCRIPTION ---- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $prenom    = trim(isset($_POST['prenom']) ? $_POST['prenom'] : '');
    $nom       = trim(isset($_POST['nom']) ? $_POST['nom'] : '');
    $email     = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $password  = trim(isset($_POST['password']) ? $_POST['password'] : '');
    $password2 = trim(isset($_POST['password2']) ? $_POST['password2'] : '');
    $telephone = trim(isset($_POST['telephone']) ? $_POST['telephone'] : '');

    if (!$prenom || !$nom || !$email || !$password) {
        $regError = 'Tous les champs obligatoires doivent être remplis.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $regError = 'Adresse email invalide.';
    } elseif (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $regError = 'Le mot de passe doit avoir 8 caractères, une majuscule et un chiffre.';
    } elseif ($password !== $password2) {
        $regError = 'Les mots de passe ne correspondent pas.';
    } else {
        $db   = getDB();
        $stmt = $db->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $regError = 'Cette adresse email est déjà utilisée.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $ins  = $db->prepare("INSERT INTO utilisateurs (prenom, nom, email, password, telephone) VALUES (?,?,?,?,?)");
            $ins->execute([$prenom, $nom, $email, $hash, $telephone]);
            $regSuccess = 'Compte créé avec succès ! Vous pouvez maintenant vous connecter.';
        }
    }
}

$activeTab = (isset($_GET['tab']) ? $_GET['tab'] : '') === 'register' || $regError || $regSuccess ? 'register' : 'login';
?>

<div class="auth-wrapper">
  <div class="auth-card">

    <div class="auth-logo">Book<span>Nest</span></div>
    <p class="auth-subtitle">Votre librairie en ligne préférée</p>

    <!-- ONGLETS -->
    <div class="tab-switcher">
      <button class="tab-btn <?= $activeTab === 'login' ? 'active' : '' ?>" id="tab-login">Connexion</button>
      <button class="tab-btn <?= $activeTab === 'register' ? 'active' : '' ?>" id="tab-register">Inscription</button>
    </div>

    <!-- ========================
         FORMULAIRE CONNEXION
         ======================== -->
    <div id="form-login" <?= $activeTab === 'register' ? 'style="display:none"' : '' ?>>

      <?php if ($loginError): ?>
        <div class="alert alert-error visible"><?= e($loginError) ?></div>
      <?php endif; ?>

      <form method="POST" id="login-form" novalidate>
        <input type="hidden" name="action" value="login">

        <div class="form-group">
          <label for="login-email">Adresse email *</label>
          <input type="email" id="login-email" name="email"
                 value="<?= e(isset($_POST['email']) ? $_POST['email'] : '') ?>"
                 placeholder="votre@email.com">
          <span class="field-error" id="login-email-error"></span>
        </div>

        <div class="form-group">
          <label for="login-password">Mot de passe *</label>
          <input type="password" id="login-password" name="password" placeholder="••••••••">
          <span class="field-error" id="login-password-error"></span>
        </div>

        <button type="submit" class="btn btn-primary btn-full mt-2">Se connecter</button>
      </form>

      <p class="text-center mt-3" style="font-size:.85rem;color:var(--muted)">
        Pas encore de compte ?
        <a href="#" onclick="document.getElementById('tab-register').click();return false;" style="color:var(--brown)">Créer un compte</a>
      </p>
    </div>

    <!-- ========================
         FORMULAIRE INSCRIPTION
         ======================== -->
    <div id="form-register" <?= $activeTab === 'login' ? 'style="display:none"' : '' ?>>

      <?php if ($regError): ?>
        <div class="alert alert-error visible"><?= e($regError) ?></div>
      <?php endif; ?>
      <?php if ($regSuccess): ?>
        <div class="alert alert-success visible"><?= e($regSuccess) ?></div>
      <?php endif; ?>

      <form method="POST" id="register-form" novalidate>
        <input type="hidden" name="action" value="register">

        <div class="form-row">
          <div class="form-group">
            <label for="reg-prenom">Prénom *</label>
            <input type="text" id="reg-prenom" name="prenom"
                   value="<?= e(isset($_POST['prenom']) ? $_POST['prenom'] : '') ?>" placeholder="Jean">
            <span class="field-error" id="reg-prenom-error"></span>
          </div>
          <div class="form-group">
            <label for="reg-nom">Nom *</label>
            <input type="text" id="reg-nom" name="nom"
                   value="<?= e(isset($_POST['nom']) ? $_POST['nom'] : '') ?>" placeholder="Dupont">
            <span class="field-error" id="reg-nom-error"></span>
          </div>
        </div>

        <div class="form-group">
          <label for="reg-email">Adresse email *</label>
          <input type="email" id="reg-email" name="email"
                 value="<?= e(isset($_POST['email']) ? $_POST['email'] : '') ?>" placeholder="jean@example.com">
          <span class="field-error" id="reg-email-error"></span>
          <div class="email-status" id="email-status"></div>
        </div>

        <div class="form-group">
          <label for="reg-phone">Téléphone</label>
          <input type="tel" id="reg-phone" name="telephone"
                 value="<?= e(isset($_POST['telephone']) ? $_POST['telephone'] : '') ?>" placeholder="+216 XX XXX XXX">
          <span class="field-error" id="reg-phone-error"></span>
        </div>

        <div class="form-group">
          <label for="reg-password">Mot de passe *</label>
          <input type="password" id="reg-password" name="password" placeholder="••••••••">
          <div class="form-hint">Min. 8 caractères, une majuscule et un chiffre.</div>
          <span class="field-error" id="reg-password-error"></span>
        </div>

        <div class="form-group">
          <label for="reg-password2">Confirmer le mot de passe *</label>
          <input type="password" id="reg-password2" name="password2" placeholder="••••••••">
          <span class="field-error" id="reg-password2-error"></span>
        </div>

        <button type="submit" class="btn btn-primary btn-full mt-2">Créer mon compte</button>
      </form>
    </div>

  </div>
</div>

<?php require_once 'back/footer.php'; ?>
