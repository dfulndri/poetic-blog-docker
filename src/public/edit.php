<?php
require_once __DIR__ . '/../config/database.php';
$uploadDir = __DIR__ . '/uploads/';

$id = $_GET['id'] ?? null;
if (!$id) header("Location: index.php");

// Ambil data artikel
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();
if (!$article) header("Location: index.php");

// Proses Update
if (isset($_POST['update'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imageName = $article['image'];

    if (!empty($_FILES['image']['name'])) {
        // Hapus foto lama
        if ($imageName && file_exists($uploadDir . $imageName)) unlink($uploadDir . $imageName);

        $imageName = time() . '_' . $_FILES['image']['name'];
        move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName);
    }

    $stmt = $pdo->prepare("UPDATE articles SET title=?, content=?, image=? WHERE id=?");
    $stmt->execute([$title, $content, $imageName, $id]);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Article</title>
    <style>
        body {
            font-family: Arial;
            background: #f4f4f9;
            padding: 40px;
        }

        .form-edit {
            background: #fff;
            padding: 20px;
            max-width: 500px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        input,
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        button {
            background: #6c63ff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <div class="form-edit">
        <h1>Edit Article</h1>
        <form method="POST" enctype="multipart/form-data">
            <input type="text" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
            <textarea name="content" rows="10" required><?= htmlspecialchars($article['content']) ?></textarea>

            <p>Current Image:</p>
            <?php if ($article['image'] && file_exists($uploadDir . $article['image'])): ?>
                <img src="uploads/<?= $article['image'] ?>" width="100%" style="border-radius: 5px; margin-bottom: 10px;">
            <?php endif; ?>

            <input type="file" name="image" accept="image/*">
            <button type="submit" name="update">Update Article</button>
            <a href="index.php" style="margin-left: 10px; color: #666;">Cancel</a>
        </form>
    </div>
</body>

</html>