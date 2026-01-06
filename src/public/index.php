<?php
require_once __DIR__ . '/../config/database.php';

// CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("INSERT INTO articles (title, content) VALUES (?, ?)");
    $stmt->execute([$_POST['title'], $_POST['content']]);
}

// READ
$articles = $pdo->query("SELECT * FROM articles ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Poetic Blog</title>


<body>

    <h1>✍️ Poetic Articles</h1>

    <form method="POST">
        <input name="title" placeholder="Judul" required><br><br>
        <textarea name="content" placeholder="Tulisanmu..." required></textarea><br><br>
        <button type="submit">Publish</button>
    </form>

    <hr>

    <?php foreach ($articles as $a): ?>
        <h2><?= htmlspecialchars($a['title']) ?></h2>
        <p><?= nl2br(htmlspecialchars($a['content'])) ?></p>
        <hr>
    <?php endforeach; ?>

</body>

</html>