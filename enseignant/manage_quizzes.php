<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (isset($_GET['delete']) && isset($_GET['id'])) {
    $id_quiz = (int) $_GET['id'];
    $id_enseignant = $_SESSION['user_id'];

    
    $stmt = $pdo->prepare("SELECT id_quiz FROM quiz WHERE id_quiz = ? AND id_enseignant = ?");
    $stmt->execute([$id_quiz, $id_enseignant]);
    if (!$stmt->fetch()) {
        die('Accès interdit');
    }

    // delete questions
    $stmt = $pdo->prepare("DELETE FROM questions WHERE id_quiz = ?");
    $stmt->execute([$id_quiz]);

    // delete quiz
    $stmt = $pdo->prepare("DELETE FROM quiz WHERE id_quiz = ?");
    $stmt->execute([$id_quiz]);

    header('Location: add_quiz.php');
    exit;
}
?>