-- Supprime la base de données si elle existe déjà
DROP DATABASE IF EXISTS ListeLicencie;

-- Crée une nouvelle base de données
CREATE DATABASE IF NOT EXISTS ListeLicencie;

-- Utilise la base de données ListeLicencie
USE ListeLicencie;

-- Table Categorie
CREATE TABLE categorie (
    codeCat VARCHAR(15) PRIMARY KEY,
    ecoleDeFoot BOOLEAN DEFAULT FALSE
);

-- Table equipe
CREATE TABLE equipe (
    codeCat VARCHAR(15),
    nomEquipe VARCHAR(25) NOT NULL,
    nomEducateur VARCHAR(30),
    prenomEducateur VARCHAR(30),
    senior1 BOOLEAN,
    senior2 BOOLEAN,
    PRIMARY KEY (nomEquipe),
    FOREIGN KEY (codeCat) REFERENCES categorie(codeCat)
);

-- Table licencie
CREATE TABLE licencie (
    idLicence INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    numLicence VARCHAR(20),
    fonction ENUM('Joueur', 'Gardien', 'Educateur', 'Arbitre'),
    nom VARCHAR(30),
    prenom VARCHAR(30),
    dateNaissance DATE,
    nomEquipe VARCHAR(25),
    adresse VARCHAR(150),
    cp VARCHAR(5),
    ville VARCHAR(50),
    numTel VARCHAR(20),
    mail VARCHAR(50),
    codeCat VARCHAR(15),
    dateSaisieLicence DATE,
    nomSaisieLicence VARCHAR(50),
    UNIQUE(numLicence, fonction),
    FOREIGN KEY (codeCat) REFERENCES categorie(codeCat),
    FOREIGN KEY (nomEquipe) REFERENCES equipe(nomEquipe)
);

-- Table equipement
    CREATE TABLE equipement (
        idEquipement INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        tailleEquipement VARCHAR(10),
        dateDistribution DATE,
        nomEquipement VARCHAR(60),
        distribue TINYINT(1) NOT NULL DEFAULT 0,
        typeEquipement VARCHAR(30),
        idLicence INT NOT NULL,
        coupe TINYINT(1) NOT NULL DEFAULT 0,
        saison VARCHAR(20),
        FOREIGN KEY (idLicence) REFERENCES licencie(idLicence)
    );

-- Table cotisation
CREATE TABLE cotisation (
    numCotisation INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
    idLicence INT NOT NULL,
    montant INT,
    complet BOOLEAN DEFAULT FALSE, 
    moyenPaiement ENUM('Espèces', 'Chèque', 'CB', 'Virement'),
    FOREIGN KEY (idLicence) REFERENCES licencie(idLicence)
);

-- Table ajoutePaiement
CREATE TABLE ajoutePaiement (
    numPaiement INT AUTO_INCREMENT PRIMARY KEY,
    numCotisation INT,
    numeroVersement ENUM('1', '2', '3', '4'),
    montantR INT,
    datePaiement DATE,
    FOREIGN KEY (numCotisation) REFERENCES cotisation(numCotisation)
);

-- Table utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nomUtilisateur VARCHAR(50) NOT NULL,
    motDePasse VARCHAR(255) NOT NULL
);

-- Insertion des utilisateurs
INSERT INTO utilisateurs (nomUtilisateur, motDePasse) VALUES ('FCOSK06', '$2y$10$ZFxV2kQKp7ixPHKPEZUoA.hxbPsZUES90F7L1XFKrIayDAOsjA5IC');

INSERT INTO categorie (codeCat, ecoleDeFoot) VALUES
    ('U7', TRUE), 
    ('U9', TRUE), 
    ('U11', TRUE), 
    ('U13', TRUE), 
    ('U14', FALSE),
    ('U15', FALSE), 
    ('U16', FALSE), 
    ('U18', FALSE), 
    ('U19', FALSE),
    ('Seniors', FALSE), 
    ('Seniors F', FALSE), 
    ('Vétérans', FALSE), 
    ('Super vétérans', FALSE);

-- Insertion des équipes
INSERT INTO equipe (codeCat, nomEquipe, senior1, senior2) VALUES
    ('U7', 'U7 A', FALSE, FALSE),
    ('U7', 'U7 B', FALSE, FALSE),
    ('U7', 'U7 C', FALSE, FALSE),
    ('U7', 'U7 D', FALSE, FALSE),
    ('U9', 'U9 A', FALSE, FALSE),
    ('U9', 'U9 B', FALSE, FALSE),
    ('U9', 'U9 C', FALSE, FALSE),
    ('U9', 'U9 D', FALSE, FALSE),
    ('U9', 'U9 E', FALSE, FALSE),
    ('U11', 'U11-1', FALSE, FALSE),
    ('U11', 'U11-2', FALSE, FALSE),
    ('U11', 'U11-3', FALSE, FALSE),
    ('U13', 'U13-1', FALSE, FALSE),
    ('U13', 'U13-2', FALSE, FALSE),
    ('U13', 'U13-3', FALSE, FALSE),
    ('U13', 'U13-4', FALSE, FALSE),
    ('U13', 'U13-F', FALSE, FALSE),
    ('U14', 'U14-R2', FALSE, FALSE),
    ('U18', 'U18-D1', FALSE, FALSE),
    ('U18', 'U18-F', FALSE, FALSE),
    ('U19', 'U19-R2', FALSE, FALSE),
    ('Seniors', 'Seniors-1', TRUE, FALSE),
    ('Seniors', 'Seniors-2', FALSE, TRUE),
    ('Seniors', 'Seniors-3', FALSE, FALSE),
    ('Seniors', 'Seniors-F', FALSE, FALSE),
    ('Vétérans', 'Vétérans 1', FALSE, FALSE),
    ('Super vétérans', 'Super Vétérans 1', FALSE, FALSE);

-- Mise à jour de la table cotisation
UPDATE cotisation c
SET complet = TRUE
WHERE montant = (
    SELECT SUM(ap.montantR)
    FROM ajoutePaiement ap
    WHERE ap.numCotisation = c.numCotisation
);

-- Mise à jour du mot de passe de l'utilisateur
UPDATE utilisateurs 
SET motDePasse = '$2y$10$ZFxV2kQKp7ixPHKPEZUoA.hxbPsZUES90F7L1XFKrIayDAOsjA5IC' 
WHERE nomUtilisateur = 'FCOSK06';
