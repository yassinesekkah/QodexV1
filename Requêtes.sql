-- Requete 01
USE codex;
INSERT INTO quiz (titre_quiz, DESCRIPTION, id_categorie, duree_minutes, id_enseignant)
VALUES ('sql_quiz', 'Description du quiz', 1, 60, 1);

-- Requete 02
USE codex;
UPDATE quiz
set duree_minutes = 30
WHERE id_quiz = 1;

-- Requete 03
USE codex;
SELECT * FROM utilisateurs;

-- Requete 04
USE codex;
SELECT nom, email FROM utilisateurs;

-- Requete 05
USE codex;
SELECT * FROM quiz;

-- Requete 06
USE codex;
SELECT titre_quiz FROM quiz;

-- Requete 07
USE codex;
SELECT * FROM categories;

-- Requete 08
USE codex;
SELECT * FROM utilisateurs
WHERE ROLE = 'enseignant'

-- Requete 09
USE codex;
SELECT * FROM utilisateurs
WHERE ROLE = 'etudiant'

-- Requete 10
USE codex;
SELECT * FROM quiz
WHERE duree_minutes > 30;

-- Requete 11
USE codex;
SELECT * FROM quiz
WHERE duree_minutes <= 45;

-- Requete 12
USE codex;
SELECT * FROM questions
WHERE points > 5

-- Requete 13
USE codex;
SELECT * FROM quiz
WHERE duree_minutes BETWEEN 20 AND 40;

-- Requete 14
USE codex;
SELECT * FROM resultats
WHERE score >= 60;

-- Requete 15
USE codex;
SELECT * FROM resultats
WHERE score < 50;

-- Requete 16
USE codex;
SELECT * FROM questions
WHERE points BETWEEN 5 and 10

-- Requete 17
USE codex;
SELECT * FROM quiz
WHERE id_enseignant = 1

-- Requete 18
USE codex;
SELECT * FROM quiz
ORDER BY duree_minutes

-- Requete 19
USE codex;
SELECT * FROM resultats
ORDER BY score desc

-- Requete 20
USE codex;
SELECT * FROM resultats
ORDER BY score DESC
LIMIT 5

-- Requete 21
USE codex;
SELECT * FROM questions
ORDER BY points 

-- Requete 22
USE codex;
SELECT * FROM resultats
ORDER BY date_passage DESC
LIMIT 3

-- Requete 23
USE codex;
SELECT titre_quiz, nom_categorie FROM quiz
INNER JOIN categories
ON quiz.id_categorie = categories.id_categorie





