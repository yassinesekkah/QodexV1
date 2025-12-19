-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.4.3 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour codex
CREATE DATABASE IF NOT EXISTS `codex` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `codex`;

-- Listage des données de la table codex.categories : ~3 rows (environ)
DELETE FROM `categories`;
INSERT INTO `categories` (`id_categorie`, `nom_categorie`) VALUES
	(1, 'Informatique'),
	(2, 'Mathematiques'),
	(3, 'Histoire');

-- Listage des données de la table codex.questions : ~0 rows (environ)
DELETE FROM `questions`;
INSERT INTO `questions` (`id_question`, `texte_question`, `reponse_correcte`, `points`, `id_quiz`) VALUES
	(1, 'Quel est le résultat de 2+2?', '4', 5, 1),
	(2, 'Résoudre x+5=10', '5', 10, 1),
	(3, 'Quelle balise HTML pour un titre?', '<h1>', 5, 2),
	(4, 'Comment changer la couleur en CSS?', 'color', 10, 2),
	(5, 'En quelle année a commencé la Première Guerre mondiale?', '1914', 5, 3),
	(6, 'Qui était le président des USA en 1945?', 'Harry Truman', 10, 3);

-- Listage des données de la table codex.quiz : ~0 rows (environ)
DELETE FROM `quiz`;
INSERT INTO `quiz` (`id_quiz`, `titre_quiz`, `description`, `id_categorie`, `duree_minutes`, `id_enseignant`) VALUES
	(1, 'Quiz Algebre', 'Test sur les bases de l\'algèbre', 1, 30, 1),
	(2, 'Quiz HTML/CSS', 'Test sur le développement web front-end', 2, 45, 5),
	(3, 'Quiz Histoire Moderne', 'Questions sur l\'Histoire du XXème siècle', 3, 40, 1),
	(5, 'sql_quiz', 'Description du quiz', 1, 60, 1);

-- Listage des données de la table codex.resultats : ~0 rows (environ)
DELETE FROM `resultats`;
INSERT INTO `resultats` (`id_resultat`, `score`, `date_passage`, `id_etudiant`, `id_quiz`) VALUES
	(1, 80, '2025-12-01 10:00:00', 3, 1),
	(2, 90, '2025-12-01 11:00:00', 4, 1),
	(3, 70, '2025-12-02 09:30:00', 5, 1),
	(4, 85, '2025-12-02 10:00:00', 3, 2),
	(5, 95, '2025-12-02 10:30:00', 4, 2);

-- Listage des données de la table codex.utilisateurs : ~5 rows (environ)
DELETE FROM `utilisateurs`;
INSERT INTO `utilisateurs` (`id_utilisateur`, `nom`, `email`, `motDePasse`, `ROLE`) VALUES
	(1, 'Ahmed Benali', 'ahmed@gmail.com', '12345', 'enseignant'),
	(2, 'Yassine Sekkah', 'yassine@gmail.com', '12345', 'etudiant'),
	(3, 'Khadija Amrani', 'khadija@gmail.com', '12345', 'etudiant'),
	(4, 'Omar Idrissi', 'omar@gmail.com', '12345', 'etudiant'),
	(5, 'Malika Elhassani', 'sara@gmail.com', '12345', 'enseignant');

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
