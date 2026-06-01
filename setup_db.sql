-- ============================================
-- UATM GASA — Script SQL complet
-- Exécuter dans phpMyAdmin → onglet SQL
-- ============================================

CREATE DATABASE IF NOT EXISTS uatm_gasa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE uatm_gasa;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    nom           VARCHAR(100) NOT NULL,
    prenom        VARCHAR(100) NOT NULL,
    email         VARCHAR(150) UNIQUE NOT NULL,
    mot_de_passe  VARCHAR(255) NOT NULL,
    role          ENUM('etudiant','professeur','admin') DEFAULT 'etudiant',
    photo         VARCHAR(255) DEFAULT NULL,
    telephone     VARCHAR(20)  DEFAULT NULL,
    actif         TINYINT(1)   DEFAULT 1,
    created_at    DATETIME     DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS filieres (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    nom  VARCHAR(100) NOT NULL,
    code VARCHAR(20) UNIQUE NOT NULL
);

CREATE TABLE IF NOT EXISTS memoires (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    titre            VARCHAR(255) NOT NULL,
    resume           TEXT         DEFAULT NULL,
    fichier_pdf      VARCHAR(255) DEFAULT NULL,
    statut           ENUM('brouillon','soumis','en_attente','en_retour','valide','rejete') DEFAULT 'brouillon',
    etudiant_id      INT NOT NULL,
    professeur_id    INT DEFAULT NULL,
    filiere_id       INT DEFAULT NULL,
    date_creation    DATETIME DEFAULT CURRENT_TIMESTAMP,
    date_soumission  DATETIME DEFAULT NULL,
    date_validation  DATETIME DEFAULT NULL,
    commentaire      TEXT DEFAULT NULL,
    FOREIGN KEY (etudiant_id)   REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (professeur_id) REFERENCES utilisateurs(id) ON DELETE SET NULL,
    FOREIGN KEY (filiere_id)    REFERENCES filieres(id)     ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS notifications (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id  INT NOT NULL,
    message         TEXT NOT NULL,
    type            ENUM('info','succes','alerte','erreur') DEFAULT 'info',
    lu              TINYINT(1) DEFAULT 0,
    date_envoi      DATETIME   DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE
);

-- Filières
INSERT IGNORE INTO filieres (nom, code) VALUES
('Génie Logiciel',            'GL'),
('Informatique',              'INFO'),
('Intelligence Artificielle', 'IA'),
('Réseaux',                   'RT');

-- Utilisateurs (mot de passe = password123)
INSERT IGNORE INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES
('DUPONT',    'Jean',   'jean@uatm.bj',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('KONAN',     'Marie',  'marie@uatm.bj',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('NGUESSAN',  'Paul',   'paul@uatm.bj',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('DIABY',     'Awa',    'awa@uatm.bj',    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('ADJOUMANI', 'Koffi',  'koffi@uatm.bj',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant'),
('KOUASSI',   'Martin', 'prof@uatm.bj',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'professeur'),
('ADMIN',     'Super',  'admin@uatm.bj',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Mémoires
INSERT IGNORE INTO memoires (titre, statut, etudiant_id, professeur_id, filiere_id, date_creation, date_soumission) VALUES
('Système de gestion des mémoires en ligne', 'soumis',     1, 6, 1, '2024-05-12 00:00:00', '2024-05-12 10:30:00'),
('Application mobile pour la bibliothèque',  'en_attente', 1, 6, 2, '2024-05-11 00:00:00', '2024-05-11 00:00:00'),
('Étude sur l\'intelligence artificielle',   'brouillon',  1, 6, 3, '2024-05-01 00:00:00', NULL),
('Application mobile de e-learning',         'soumis',     2, 6, 2, '2024-05-11 00:00:00', '2024-05-11 09:15:00'),
('Plateforme de vote électronique',          'soumis',     3, 6, 4, '2024-05-10 00:00:00', '2024-05-10 14:20:00'),
('Application de gestion de bibliothèque',   'soumis',     5, 6, 2, '2024-05-09 00:00:00', '2024-05-09 08:10:00');

-- Notifications
INSERT IGNORE INTO notifications (utilisateur_id, message, type, lu) VALUES
(1, 'Votre mémoire a été soumis avec succès.',   'succes', 0),
(1, 'Votre professeur a ajouté un commentaire.', 'info',   0),
(6, 'Nouveau mémoire soumis par Jean DUPONT.',   'info',   0),
(6, 'Nouveau mémoire soumis par Marie KONAN.',   'info',   0),
(6, 'Nouveau mémoire soumis par Paul N\'GUESSAN.','info',  0);
