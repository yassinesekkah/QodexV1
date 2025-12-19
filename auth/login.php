<?php
session_start();
require_once __DIR__ . '/../config/database.php';

// CSRF token
if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // CSRF check
    if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
        die('CSRF invalide');
    }

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = "عمر جميع الخانات";
    } else {

        $stmt = $pdo->prepare(
            "SELECT id_utilisateur, nom, email, motdepasse, role
             FROM utilisateurs
             WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = "Email ou mot de passe incorrect";
        } else {

            if (!password_verify($password, $user['motdepasse'])) {
                $error = "Email ou mot de passe incorrect";
            } else {

                // ✅ Login success
                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id_utilisateur'];
                $_SESSION['user_nom'] = $user['nom'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect selon role
                if ($user['role'] === 'enseignant') {
                    header('Location: ../enseignant/dashboard.php');
                    exit;
                } else {
                    header('Location: ../etudiant/dashboard.php');
                    exit;
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-500 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Connexion</h2>

        <?php
        if (!empty($error)) {
            echo "<p class='text-red-500 text-sm mb-4'>$error</p>";
        }
        ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="token" value="<?= $_SESSION['token']; ?>">

            <div>
                <label class="block text-gray-700 font-medium mb-1">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <div>
                <label class="block text-gray-700 font-medium mb-1">Mot de passe</label>
                <input type="password" name="password"
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white font-bold py-2 rounded-md hover:bg-blue-700 transition duration-200">
                Login
            </button>
        </form>

        <p class="text-center text-gray-600 mt-4 text-sm">
            Pas encore inscrit ? <a href="register.php" class="text-blue-600 hover:underline">Créer un compte</a>
        </p>
    </div>

</body>

</html>