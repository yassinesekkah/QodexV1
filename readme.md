# Qodex

Plateforme Sécurisée de Gestion de Quiz – PHP & MySQL

## 1. Présentation Générale

Qodex est une plateforme web sécurisée dédiée à la création et à la gestion de quiz en ligne.
Développée avec PHP (PDO) et MySQL, elle permet aux enseignants de créer, modifier, supprimer et organiser des quiz composés de questions à choix multiples.

Le projet respecte les bonnes pratiques de développement web (sécurité, structuration du code, base de données relationnelle) et peut servir de base pour une application pédagogique ou professionnelle.

## 2. Fonctionnalités Principales
### 2.1 Module Enseignant

Authentification sécurisée par session

Création de quiz avec :

Titre

Description

Catégorie

Durée

Ajout dynamique de questions

Questions à choix multiples (4 options)

Définition de la réponse correcte

Modification des quiz via le même formulaire (Add / Edit)

Suppression des quiz avec confirmation

Tableau de bord listant les quiz créés

### 2.2 Module Étudiant (En cours / à venir)

Accès aux quiz disponibles

Participation aux quiz

Calcul automatique des scores

Historique des résultats

## 3. Technologies Utilisées
Couche	Technologie
Backend	PHP 8+ (PDO)
Base de données	MySQL
Frontend	HTML5, Tailwind CSS
JavaScript	Vanilla JS
Icônes	Font Awesome
Environnement	Laragon
## 4. Architecture du Projet

Le projet est structuré de manière modulaire afin de séparer la logique métier, la configuration et l’interface utilisateur.

qodex/
│
├── auth/                # Authentification
├── enseignants/         # Gestion des quiz (CRUD)
├── config/              # Connexion base de données
├── includes/            # Header / Footer
├── assets/              # CSS / JS
├── index.php            # Point d’entrée
└── README.md

## 5. Modélisation de la Base de Données
### 5.1 Table quiz
CREATE TABLE quiz (
    id_quiz INT AUTO_INCREMENT PRIMARY KEY,
    titre_quiz VARCHAR(255) NOT NULL,
    description TEXT,
    id_categorie INT NOT NULL,
    id_enseignant INT NOT NULL,
    duree_minutes INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

### 5.2 Table questions
CREATE TABLE questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    id_quiz INT NOT NULL,
    texte_question VARCHAR(255) NOT NULL,
    option1 VARCHAR(255) NOT NULL,
    option2 VARCHAR(255) NOT NULL,
    option3 VARCHAR(255) NOT NULL,
    option4 VARCHAR(255) NOT NULL,
    reponse_correcte INT NOT NULL
);

Relations

Un quiz contient plusieurs questions (One-to-Many)

La clé étrangère id_quiz assure l’intégrité des données

## 6. Sécurité

Gestion des rôles (enseignant / étudiant)

Sessions PHP sécurisées

Protection CSRF via token

Requêtes préparées (PDO) contre les injections SQL

Protection XSS avec htmlspecialchars()

Accès restreint aux pages sensibles

## 7. Installation et Configuration
### 7.1 Prérequis

PHP 8 ou plus

MySQL

Laragon / XAMPP / WAMP

### 7.2 Étapes d’installation

Cloner le projet

git clone https://github.com/votre-utilisateur/QodexV1.git


Importer la base de données MySQL

Configurer la connexion dans :

config/database.php


Lancer le serveur et accéder à :

http://localhost/QodexV1

## 8. Tests

Tests manuels des fonctionnalités CRUD

Vérification de l’intégrité des données

Tests de validation des formulaires

Tests de sécurité (sessions, accès)

## 9. Améliorations Futures

Module étudiant complet

Statistiques et résultats détaillés

Pagination et recherche

Ajout d’images aux questions

API REST

Déploiement en production

## 10. Auteur

Yassine Sekkah
Projet de Développement Web
PHP • MySQL • Sécurité Web

## 11. Licence

Projet réalisé à des fins éducatives et pédagogiques.
Toute utilisation commerciale nécessite une autorisation préalable.