<?php
session_start();

// ✅ إلا session موجود
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {

    // إلا أستاذ
    if ($_SESSION['user_role'] === 'enseignant') {
        header('Location: enseignant/dashboard.php');
        exit;
    }

    // إلا طالب
    if ($_SESSION['user_role'] === 'etudiant') {
        header('Location: etudiant/dashboard.php');
        exit;
    }

    // أي role آخر (مستقبلاً)
    header('Location: auth/login.php');
    exit;
}

// ✅ إلا ما كاينش session → login
header('Location: auth/login.php');
exit;
