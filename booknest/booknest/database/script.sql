-- ============================================================
-- BookNest - Base de données MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS booknest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE booknest;

-- ============================================================
-- TABLE: utilisateurs
-- ============================================================
CREATE TABLE IF NOT EXISTS utilisateurs (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    prenom     VARCHAR(80)  NOT NULL,
    nom        VARCHAR(80)  NOT NULL,
    email      VARCHAR(150) NOT NULL UNIQUE,
    password   VARCHAR(255) NOT NULL,
    telephone  VARCHAR(20),
    role       ENUM('client','admin') DEFAULT 'client',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: livres
-- ============================================================
CREATE TABLE IF NOT EXISTS livres (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    titre       VARCHAR(200) NOT NULL,
    auteur      VARCHAR(150) NOT NULL,
    description TEXT,
    prix        DECIMAL(8,2) NOT NULL,
    stock       INT DEFAULT 0,
    categorie   VARCHAR(80),
    isbn        VARCHAR(20),
    annee       YEAR,
    editeur     VARCHAR(120),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: commandes
-- ============================================================
CREATE TABLE IF NOT EXISTS commandes (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    total       DECIMAL(10,2) NOT NULL,
    statut      ENUM('en_attente','confirmee','expediee','livree','annulee') DEFAULT 'en_attente',
    adresse     VARCHAR(255),
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- TABLE: commande_details (ligne de commande)
-- ============================================================
CREATE TABLE IF NOT EXISTS commande_details (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    commande_id INT NOT NULL,
    livre_id    INT NOT NULL,
    quantite    INT NOT NULL DEFAULT 1,
    prix_unit   DECIMAL(8,2) NOT NULL,
    FOREIGN KEY (commande_id) REFERENCES commandes(id) ON DELETE CASCADE,
    FOREIGN KEY (livre_id)    REFERENCES livres(id)    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- ============================================================
-- DONNÉES INITIALES
-- ============================================================

-- Admin par défaut (mot de passe : Admin1234)
INSERT INTO utilisateurs (prenom, nom, email, password, role) VALUES
('Admin', 'BookNest', 'admin@booknest.tn', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Livres de démonstration
INSERT INTO livres (titre, auteur, description, prix, stock, categorie, annee, editeur) VALUES
('Le Petit Prince',      'Antoine de Saint-Exupéry',
 'Un conte poétique et philosophique sous l''apparence d''un conte pour enfants.',
 12.90, 25, 'Littérature', 1943, 'Gallimard'),

('L''Étranger',          'Albert Camus',
 'Premier roman d''Albert Camus, fondateur de la philosophie de l''absurde.',
 10.50, 18, 'Littérature', 1942, 'Gallimard'),

('1984',                 'George Orwell',
 'Roman dystopique décrivant une société totalitaire sous surveillance permanente.',
 14.90, 30, 'Science-Fiction', 1949, 'Secker & Warburg'),

('Harry Potter T.1',     'J.K. Rowling',
 'La saga du jeune sorcier qui découvre le monde magique.',
 16.00, 40, 'Fantasy', 1997, 'Bloomsbury'),

('Dune',                 'Frank Herbert',
 'Épopée de science-fiction sur la planète désertique Arrakis.',
 18.50, 15, 'Science-Fiction', 1965, 'Chilton Books'),

('Les Misérables',       'Victor Hugo',
 'Fresque romanesque de la France du XIXe siècle autour de Jean Valjean.',
 22.00, 12, 'Littérature', 1862, 'A. Lacroix'),

('Sapiens',              'Yuval Noah Harari',
 'Histoire de l''humanité depuis l''âge de pierre jusqu''au XXIe siècle.',
 19.90, 22, 'Histoire', 2011, 'Albin Michel'),

('Le Seigneur des Anneaux', 'J.R.R. Tolkien',
 'L''épopée fantasy par excellence, voyage en Terre du Milieu.',
 24.90, 8, 'Fantasy', 1954, 'Allen & Unwin'),

('Python pour les débutants', 'Eric Matthes',
 'Apprenez la programmation Python de zéro avec des exemples pratiques.',
 29.90, 35, 'Informatique', 2019, 'No Starch Press'),

('L''Art de la Guerre',  'Sun Tzu',
 'Traité de stratégie militaire et de philosophie vieux de 2500 ans.',
 9.90, 50, 'Philosophie', -500, 'Divers'),

('Atomic Habits',        'James Clear',
 'Comment de petites habitudes peuvent provoquer des résultats remarquables.',
 21.00, 28, 'Développement Personnel', 2018, 'Avery'),

('Sherlock Holmes',      'Arthur Conan Doyle',
 'Les aventures du célèbre détective de Baker Street.',
 13.50, 20, 'Policier', 1887, 'Strand Magazine');

-- Commande exemple
INSERT INTO commandes (user_id, total, statut, adresse) VALUES
(1, 27.40, 'livree', '12 Rue de la Paix, Tunis 1001');

INSERT INTO commande_details (commande_id, livre_id, quantite, prix_unit) VALUES
(1, 1, 1, 12.90),
(1, 2, 1, 10.50);
