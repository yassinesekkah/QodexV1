<?php
require '../includes/header.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'enseignant') {
    header('Location: ../auth/login.php');
    exit;
}
?>

<h1>Dashboard Enseignant</h1>