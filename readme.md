Qodex — Projet Base de Données

Ce projet consiste à créer et gérer une base de données pour une application de quiz.
La base permet d’administrer les utilisateurs, les quiz, les questions et les scores.

📌 Tables principales

utilisateur : contient les informations des utilisateurs (enseignants et étudiants).

quiz : liste des quiz créés par les enseignants.

question : questions associées à chaque quiz.

choix : réponses possibles pour chaque question.

score : scores obtenus par les étudiants après avoir passé un quiz.

🎯 Objectifs du projet

Gérer les utilisateurs.

Permettre à un enseignant de créer un quiz.

Ajouter des questions et des choix de réponse.

Enregistrer les résultats des étudiants.

Consulter les scores.

🛠️ Outils utilisés

MySQL / MariaDB

HeidiSQL (gestion de la base de données)

Laragon (serveur local)

🚀 Mise en place

Installer Laragon et MySQL.

Créer la base de données dans HeidiSQL.

Créer les tables nécessaires.

Insérer les données de test.

📂 Fonctionnement général

Un enseignant peut créer un quiz.

Chaque quiz contient plusieurs questions.

Chaque question possède plusieurs choix dont un seul est correct.

Un étudiant répond aux questions et obtient un score.

Le score est enregistré dans la base de données.