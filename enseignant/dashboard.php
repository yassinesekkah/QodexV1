<?php
session_start();

if (!isset($_SESSION['token'])) {
    $_SESSION['token'] = bin2hex(random_bytes(32));
}

// ✅ منع cache نهائي
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// ✅ تحقق من login و role
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
    header('Location: ../auth/login.php');
    exit;
}
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Enseignant';

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token']) {
        die('CSRF invalide');
    }

    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);

    if (empty($nom)) {
        $error = "Le nom de la catégorie est obligatoire";
    } else {
        $stmt = $pdo->prepare(
            "INSERT INTO categories (nom_categorie, description) VALUES (?, ?)"
        );
        $stmt->execute([$nom, $description]);

        header("Location: dashboard.php");
        exit;
    }
}

// جلب جميع categories من DB
try {
    $stmtCategories = $pdo->query("SELECT * FROM categories ORDER BY id_categorie DESC");
    $categories = $stmtCategories->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $categories = [];
    $error = "Erreur lors du chargement des catégories: " . $e->getMessage();
}

// DELETE category
if (isset($_POST['delete_category']) && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id_categorie = ?");
    $stmt->execute([$id]);

    header("Location: dashboard.php");
    exit;
}
// UPDATE
if (isset($_POST['edit_category']) && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id_categorie = ?");
    $stmt->execute([$edit_id]);
    $editCategory = $stmt->fetch(PDO::FETCH_ASSOC);
}

if (isset($_POST['update_category']) && !empty($_POST['category_id'])) {
    $id = $_POST['category_id'];
    $nom = trim($_POST['nom']);
    $description = trim($_POST['description']);

    if (!empty($nom)) {
        $stmt = $pdo->prepare(
            "UPDATE categories SET nom_categorie = ?, description = ? WHERE id_categorie = ?"
        );
        $stmt->execute([$nom, $description, $id]);

        header("Location: dashboard.php");
        exit;
    }
}

// Total quiz
$stmt = $pdo->query("SELECT COUNT(*) FROM quiz");
$totalQuiz = $stmt->fetchColumn();

// Total categories
$stmt = $pdo->query("SELECT COUNT(*) FROM categories");
$totalCategories = $stmt->fetchColumn();

?>

<!-- Include Header -->
<?php include '../includes/header.php'; ?>

<!-- Dashboard Section -->
<div id="dashboard" class="section-content  pt-16">
    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <h1 class="text-4xl font-bold mb-4">Tableau de bord Enseignant</h1>
            <p class="text-xl text-indigo-100 mb-6">Gérez vos quiz et suivez les performances de vos étudiants</p>
            <div class="flex gap-4">
                <button onclick="openModal('createCategoryModal')"
                    class="bg-white text-indigo-600 px-6 py-3 rounded-lg font-semibold hover:bg-indigo-50 transition">
                    <i class="fas fa-folder-plus mr-2"></i>Nouvelle Catégorie
                </button>
                <a href="./add_quiz.php">
                    <button onclick="openModal('createQuizModal')" class="bg-indigo-700 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-800 transition">
                        <i href="./add_quiz.php" class="fas fa-plus-circle mr-2"></i>Créer un Quiz
                    </button>
                </a>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Total Quiz</p>
                        <p class="text-3xl font-bold text-gray-900">
                            <?= $totalQuiz ?>
                        </p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm">Catégories</p>
                        <p class="text-3xl font-bold text-gray-900">
                            <?= $totalCategories ?>
                        </p>


                    </div>
                    <div class="bg-purple-100 p-3 rounded-lg">
                        <i class="fas fa-folder text-purple-600 text-2xl"></i>
                    </div>
                </div>
            </div>
            <!-- <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                     <div>
                        <p class="text-gray-500 text-sm">Étudiants Actifs</p>
                        <p class="text-3xl font-bold text-gray-900">156</p>
                    </div> 
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="fas fa-user-graduate text-green-600 text-2xl"></i>
                    </div>
                </div>
            </div> -->
            <!-- <div class="bg-white rounded-xl shadow-md p-6">
                <div class="flex items-center justify-between">
                     <div>
                        <p class="text-gray-500 text-sm">Taux Réussite</p>
                        <p class="text-3xl font-bold text-gray-900">87%</p>
                    </div> 
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="fas fa-chart-line text-yellow-600 text-2xl"></i>
                    </div>
                </div>
            </div> -->
        </div>
    </div>
</div>
<!-- categories section -->
<h1 class="text-3xl font-bold text-gray-800 p-4">Mes Categories</h1>

<div id="createCategoryModal" class="p-4    fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Nouvelle Catégorie</h3>
                <button onclick="closeModal('createCategoryModal')" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['token']; ?>">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Nom de la catégorie *
                    </label>
                    <input type="text" name="nom" required
                        value="<?php echo $editCategory['nom_categorie'] ?? ''; ?>"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        Description
                    </label>
                    <textarea name="description" rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg"><?php
                                                                                    echo $editCategory['description'] ?? '';
                                                                                    ?></textarea>

                </div>

                <div class="flex gap-3">
                    <button type="button" onclick="closeModal('createCategoryModal')" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">
                        Annuler
                    </button>
                    <button type="submit"
                        name="<?php echo isset($editCategory) ? 'update_category' : 'add_category'; ?>"
                        class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        <i class="fas fa-check mr-2"></i>
                        <?php echo isset($editCategory) ? 'Enregistrer' : 'Créer'; ?>
                    </button>
                </div>
                <input type="hidden" name="category_id" value="<?php echo $editCategory['id_categorie'] ?? ''; ?>">
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <?php foreach ($categories as $cat): ?>
        <div class="bg-white rounded-xl shadow-md p-4 flex justify-between items-center">
            <div>
                <p class="font-bold text-gray-900"><?php echo htmlspecialchars($cat['nom_categorie']); ?></p>
                <p class="text-gray-500 text-sm"><?php echo htmlspecialchars($cat['description']); ?></p>
            </div>
            <div class="flex gap-2">
                <!-- Edit button -->
                <form method="POST" style="display:inline;">
                    <input type="hidden" name="edit_id" value="<?php echo $cat['id_categorie']; ?>">
                    <button type="submit" name="edit_category" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit"></i>
                    </button>
                </form>
                <!-- Delete button -->
                <form method="POST" style="display:inline;" onsubmit="return confirm('Voulez-vous vraiment supprimer cette catégorie ?');">
                    <input type="hidden" name="delete_id" value="<?php echo $cat['id_categorie']; ?>">
                    <button type="submit" name="delete_category" class="text-red-600 hover:text-red-800">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!--Quiz creation -->
<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
        document.getElementById(id).classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
        document.getElementById(id).classList.remove('flex');
    }

    <?php if (isset($editCategory)): ?>
        openModal('createCategoryModal');
    <?php endif; ?>
</script>
</body>

</html>