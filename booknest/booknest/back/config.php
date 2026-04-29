<?php
/**
 * BookNest - Configuration & Connexion BDD
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'booknest');
define('DB_CHARSET', 'utf8mb4');

define('SITE_NAME', 'BookNest');
define('SITE_URL', 'http://localhost/booknest');

/**
 * Retourne la connexion PDO (singleton)
 */
function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET;
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            http_response_code(500);
            die(json_encode(['error' => 'Connexion BDD échouée : ' . $e->getMessage()]));
        }
    }
    return $pdo;
}

/**
 * Session helpers
 */
function sessionStart() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function isLoggedIn() {
    sessionStart();
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    sessionStart();
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: auth.php');
        exit;
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: index.php');
        exit;
    }
}

function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'     => $_SESSION['user_id'],
        'prenom' => isset($_SESSION['prenom']) ? $_SESSION['prenom'] : '',
        'nom'    => isset($_SESSION['nom'])    ? $_SESSION['nom'] : '',
        'email'  => isset($_SESSION['email'])  ? $_SESSION['email'] : '',
        'role'   => isset($_SESSION['role'])   ? $_SESSION['role'] : 'client',
    ];
}

/**
 * Utilitaires
 */
function e($s) {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function setFlash($type, $msg) {
    sessionStart();
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

function getFlash() {
    sessionStart();
    $flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
    unset($_SESSION['flash']);
    return $flash;
}
