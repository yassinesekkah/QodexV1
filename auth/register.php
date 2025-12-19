<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_POST['token'] !== $_SESSION['token']) {
        die('CSRF invalide');
    }

    $nom = htmlspecialchars(trim($_POST['nom']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = $_POST['password'];

    // Validation simple
    if (empty($nom) || empty($email) || empty($password)) {
        $error = "Veuillez remplir tous les champs";
    } else {
        // Hash du mot de passe
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert prepared
        try {
            $sql = "INSERT INTO utilisateurs (nom, email, motdepasse, role) VALUES (?, ?, ?, 'enseignant')";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nom, $email, $hashedPassword]);

            // ✅ redirect directement ل login بعد التسجيل
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            // مثال: email duplicate
            if ($e->getCode() == 23000) {
                $error = "Cet email est déjà utilisé";
            } else {
                $error = "Erreur: " . $e->getMessage();
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Register Enseignant</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-500 min-h-screen flex items-center justify-center">

    <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-md">
        <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Inscription Enseignant</h2>

        <?php
        if (!empty($error)) {
            echo "<p class='text-red-500 text-sm mb-4'>$error</p>";
        }
        if (!empty($success)) {
            echo "<p class='text-green-500 text-sm mb-4'>$success</p>";
        }
        ?>

        <form method="POST" class="space-y-4">
            <input type="hidden" name="token" value="<?= $_SESSION['token']; ?>">

            <div>
                <label class="block text-gray-700 font-medium mb-1">Nom</label>
                <input type="text" name="nom" value="<?= htmlspecialchars($nom ?? '') ?>"
                    class="w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400">
            </div>

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
                Register
            </button>
        </form>

        <!-- Link Login -->
        <p class="text-center text-gray-600 mt-4 text-sm">
            Déjà un compte ?
            <a href="login.php" class="text-blue-600 hover:underline font-medium">Connexion ici</a>
        </p>
    </div>
</body>

</html>