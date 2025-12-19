<?php
session_start();
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant') {
    header('Location: ../auth/login.php');
    exit;
}

// Check if POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF check
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['token']) {
        die('CSRF invalide');
    }
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $categorie_id = $_POST['categorie_id'];
    $duree = (int) $_POST['duree_minutes'];
    $id_enseignant = $_SESSION['user_id'];

    // Add or Update
    if (empty($_POST['id_quiz'])) {
        // Add new quiz
        $stmt = $pdo->prepare("
            INSERT INTO quiz (titre_quiz, description, id_categorie, id_enseignant, duree_minutes)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$titre, $description, $categorie_id, $id_enseignant, $duree]);
        $quiz_id = $pdo->lastInsertId();
    } else {
        // Update existing quiz
        $quiz_id = (int) $_POST['id_quiz'];
        // Delete old questions
        $stmt = $pdo->prepare("DELETE FROM questions WHERE quiz_id = ?");
        $stmt->execute([$quiz_id]);

        // Update quiz
        $stmt = $pdo->prepare("
            UPDATE quiz 
            SET titre_quiz = ?, description = ?, id_categorie = ?, duree_minutes = ?
            WHERE id_quiz = ?
        ");
        $stmt->execute([$titre, $description, $categorie_id, $duree, $quiz_id]);
    }

    // Insert questions
    if (!empty($_POST['questions'])) {
        foreach ($_POST['questions'] as $q) {
            $question = htmlspecialchars(trim($q['question']));
            $option1 = strip_tags(trim($q['option1']));
            $option2 = trim($q['option2']);
            $option3 = trim($q['option3']);
            $option4 = trim($q['option4']);
            $correct  = (int)$q['correct'];

            $stmt = $pdo->prepare("
                INSERT INTO questions (id_quiz, texte_question, option1, option2, option3, option4, reponse_correcte)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$quiz_id, $question, $option1, $option2, $option3, $option4, $correct]);
        }
    }

    header('Location: add_quiz.php');
    exit;
}

// Fetch quizzes of this teacher
$stmt = $pdo->prepare("
    SELECT q.id_quiz, q.titre_quiz, q.duree_minutes, c.nom_categorie
    FROM quiz q
    JOIN categories c ON q.id_categorie = c.id_categorie
    WHERE q.id_enseignant = ?
");
$stmt->execute([$_SESSION['user_id']]);
$quizs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch categories
$stmt = $pdo->query("SELECT id_categorie, nom_categorie FROM categories ORDER BY nom_categorie ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


    <!-- Include Header -->
    <?php include '../includes/header.php'; ?>

    <main class="pt-24 px-6 lg:px-10">
        <div class="flex justify-between items-center mb-8 ">
            <h1 class="text-3xl font-bold text-gray-800">Mes Quiz</h1>

            <button onclick="openModal('createQuizModal')"
                class="bg-indigo-600 text-white px-5 py-2 rounded-lg">
                ➕ Ajouter un Quiz
            </button>
        </div>

        <!-- Grid Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php if (empty($quizs)): ?>
                <p class="text-gray-500">Aucun quiz trouvé.</p>
            <?php else: ?>
                <?php foreach ($quizs as $quiz): ?>
                    <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition">

                        <h2 class="text-xl font-bold text-gray-800 mb-2">
                            <?= htmlspecialchars($quiz['titre_quiz']) ?>
                        </h2>

                        <p class="text-sm text-gray-500 mb-1">
                            📁 Catégorie :
                            <span class="font-medium text-gray-700">
                                <?= htmlspecialchars($quiz['nom_categorie']) ?>
                            </span>
                        </p>

                        <p class="text-sm text-gray-500 mb-4">
                            ⏱ Durée :
                            <span class="font-medium text-gray-700">
                                <?= $quiz['duree_minutes'] ?> minutes
                            </span>
                        </p>

                        <div class="flex gap-3">
                            <a href="edit_quiz.php?id=<?= $quiz['id_quiz'] ?>"
                                class="flex-1 text-center bg-yellow-400 text-white py-2 rounded-lg hover:bg-yellow-500">
                                ✏️ Edit
                            </a>
                            <a href="manage_quizzes.php?delete=1&id=<?= $quiz['id_quiz'] ?>"
                                onclick="return confirm('Supprimer ce quiz ?')"
                                class="flex-1 text-center bg-red-500 text-white py-2 rounded-lg hover:bg-red-600">
                                🗑 Delete
                            </a>



                        </div>

                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </main>


    <div id="createQuizModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-2xl max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-2xl font-bold text-gray-900">Créer un Quiz</h3>
                    <button onclick="closeModal('createQuizModal')" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form method="POST" class="space-y-6">
                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['token']; ?>">

                    <!-- Basic Info -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Titre du quiz *</label>
                            <input type="text" name="titre" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Ex: Les Bases de HTML5">
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Catégorie *</label>
                            <select name="categorie_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                                <option value="">Sélectionner une catégorie</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id_categorie'] ?>"><?= htmlspecialchars($cat['nom_categorie']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-gray-700 text-sm font-bold mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Décrivez votre quiz..."></textarea>
                        </div>

                        <div>
                            <label class="block text-gray-700 text-sm font-bold mb-2">Durée (minutes) *</label>
                            <input type="number" name="duree_minutes" min="1" required class="w-full px-4 py-2 border rounded-lg">
                        </div>
                    </div>

                    <!-- Questions Section -->
                    <div class="space-y-4">
                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-bold text-gray-800">Questions</h3>
                            <button type="button" onclick="addQuestion()" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-sm">
                                ➕ Ajouter une question
                            </button>
                        </div>

                        <div id="questionsContainer">
                            <!-- Question template -->
                            <div class="bg-gray-50 rounded-lg p-4 mb-4 question-block">
                                <div class="flex justify-between items-center mb-4">
                                    <h4 class="font-bold text-gray-900">Question 1</h4>
                                    <button type="button" onclick="removeQuestion(this)" class="text-red-600 hover:text-red-700">
                                        🗑
                                    </button>
                                </div>

                                <div class="mb-3">
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Question *</label>
                                    <input type="text" name="questions[0][question]" required class="w-full px-4 py-2 border rounded-lg">
                                </div>

                                <div class="grid md:grid-cols-2 gap-3 mb-3">
                                    <div>
                                        <label class="block text-gray-700 text-sm mb-1">Option 1 *</label>
                                        <input type="text" name="questions[0][option1]" required class="w-full px-4 py-2 border rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm mb-1">Option 2 *</label>
                                        <input type="text" name="questions[0][option2]" required class="w-full px-4 py-2 border rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm mb-1">Option 3 *</label>
                                        <input type="text" name="questions[0][option3]" required class="w-full px-4 py-2 border rounded-lg">
                                    </div>
                                    <div>
                                        <label class="block text-gray-700 text-sm mb-1">Option 4 *</label>
                                        <input type="text" name="questions[0][option4]" required class="w-full px-4 py-2 border rounded-lg">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-gray-700 text-sm font-bold mb-2">Réponse correcte *</label>
                                    <select name="questions[0][correct]" required class="w-full px-4 py-2 border rounded-lg">
                                        <option value="">Sélectionner la bonne réponse</option>
                                        <option value="1">Option 1</option>
                                        <option value="2">Option 2</option>
                                        <option value="3">Option 3</option>
                                        <option value="4">Option 4</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-3 mt-4">
                        <button type="button" onclick="closeModal('createQuizModal')" class="flex-1 px-4 py-2 border rounded-lg text-gray-700 hover:bg-gray-50 transition">Annuler</button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Créer le Quiz</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let questionCount = 1;

        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('bg-opacity-50')) {
                event.target.classList.add('hidden');
                event.target.classList.remove('flex');
            }
        }

        function addQuestion() {
            questionCount++;
            const container = document.getElementById('questionsContainer');
            const questionHTML = `
        <div class="bg-gray-50 rounded-lg p-4 mb-4 question-block">
            <div class="flex justify-between items-center mb-4">
                <h5 class="font-bold text-gray-900">Question ${questionCount}</h5>
                <button type="button" onclick="removeQuestion(this)" class="text-red-600 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Question *</label>
                <input type="text" name="questions[${questionCount-1}][question]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="Posez votre question...">
            </div>-

            <div class="grid md:grid-cols-2 gap-3 mb-3">
                <div>
                    <label class="block text-gray-700 text-sm mb-2">Option 1 *</label>
                    <input type="text" name="questions[${questionCount-1}][option1]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm mb-2">Option 2 *</label>
                    <input type="text" name="questions[${questionCount-1}][option2]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm mb-2">Option 3 *</label>
                    <input type="text" name="questions[${questionCount-1}][option3]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-gray-700 text-sm mb-2">Option 4 *</label>
                    <input type="text" name="questions[${questionCount-1}][option4]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2">Réponse correcte *</label>
                <select name="questions[${questionCount-1}][correct]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">Sélectionner la bonne réponse</option>
                    <option value="1">Option 1</option>
                    <option value="2">Option 2</option>
                    <option value="3">Option 3</option>
                    <option value="4">Option 4</option>
                </select>
            </div>
        </div>
    `;
            container.insertAdjacentHTML('beforeend', questionHTML);
        }

        // Remove Question from Quiz Creation Form
        function removeQuestion(button) {
            const questionBlock = button.closest('.question-block');
            questionBlock.remove();

            // Renumber questions
            const questions = document.querySelectorAll('.question-block');
            questions.forEach((q, index) => {
                const title = q.querySelector('h5');
                title.textContent = `Question ${index + 1}`;
            });
            questionCount = questions.length;
        }
    </script>
</body>

</html>