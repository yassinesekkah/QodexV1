<?php  
session_start();
require_once __DIR__ . '/../config/database.php';

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'enseignant'){
    header('location: ../auth/login.php');
    exit;
}
if(!isset($_GET['id'])){
    header('location: add_quiz.php');
    exit;
}


//njibo id mn lien b GET methode
$quiz_id = (int) $_GET['id'];
$id_enseignant = $_SESSION['user_id'];

//njibo question
$stmt = $pdo->prepare("SELECT * FROM quiz WHERE id_quiz = ? AND id_enseignant = ?");
$stmt->execute([$quiz_id, $id_enseignant]);
$questions = $stmt ->fetchAll(PDO::FETCH_ASSOC);

//njibo l categories
$stmt = $pdo->query("SELECT id_categorie, nom_categorie FROM categories ORDER BY nom_categorie ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <form method="POST" action="edit_quiz.php?id=<?= $quiz_id ?>">
    <input type="hidden" name="csrf_token" value="<?= $_SESSION['token'] ?>">

    <label>Titre:</label>
    <input type="text" name="titre" value="<?= htmlspecialchars($quiz['titre_quiz']) ?>">

    <label>Description:</label>
    <textarea name="description"><?= htmlspecialchars($quiz['description']) ?></textarea>

    <label>Catégorie:</label>
    <select name="categorie_id">
        <?php foreach($categories as $cat): ?>
            <option value="<?= $cat['id_categorie'] ?>" <?= $cat['id_categorie']==$quiz['id_categorie']?'selected':'' ?>>
                <?= htmlspecialchars($cat['nom_categorie']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Durée (minutes):</label>
    <input type="number" name="duree_minutes" value="<?= $quiz['duree_minutes'] ?>">

    <h3>Questions</h3>
    <div id="questionsContainer">
        <?php foreach($questions as $i => $q): ?>
            <div class="question-block">
                <input type="text" name="questions[<?= $i ?>][question]" value="<?= htmlspecialchars($q['question']) ?>">
                <input type="text" name="questions[<?= $i ?>][option1]" value="<?= htmlspecialchars($q['option1']) ?>">
                <input type="text" name="questions[<?= $i ?>][option2]" value="<?= htmlspecialchars($q['option2']) ?>">
                <input type="text" name="questions[<?= $i ?>][option3]" value="<?= htmlspecialchars($q['option3']) ?>">
                <input type="text" name="questions[<?= $i ?>][option4]" value="<?= htmlspecialchars($q['option4']) ?>">
                <select name="questions[<?= $i ?>][correct]">
                    <?php for($c=1;$c<=4;$c++): ?>
                        <option value="<?= $c ?>" <?= $c==$q['correct_option']?'selected':'' ?>>Option <?= $c ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="submit">Sauvegarder les modifications</button>
</form>

</body>
</html>