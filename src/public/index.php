<?php
require_once __DIR__ . '/../config/database.php';

// Path folder upload (Fisik)
$uploadDir = __DIR__ . '/uploads/';

// Buat folder jika belum ada dan beri izin akses
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$error = "";

// ===== CREATE =====
if (isset($_POST['create'])) {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $imageName = null;

    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = time() . '_' . uniqid() . '.' . $ext;

        if (!move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $imageName)) {
            $error = "Gagal mengunggah gambar. Cek permissions folder uploads.";
        }
    }

    if (!$error) {
        $stmt = $pdo->prepare("INSERT INTO articles (title, content, image) VALUES (?, ?, ?)");
        $stmt->execute([$title, $content, $imageName]);
        header("Location: index.php");
        exit;
    }
}

// ===== DELETE =====
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM articles WHERE id=?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();

    if ($img && file_exists($uploadDir . $img)) {
        unlink($uploadDir . $img);
    }

    $stmt = $pdo->prepare("DELETE FROM articles WHERE id=?");
    $stmt->execute([$id]);
    header("Location: index.php");
    exit;
}

// ===== READ =====
$articles = $pdo->query("SELECT * FROM articles ORDER BY id DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Poetic Blog</title>
    <style>
        /* CSS tetap sama seperti milikmu */
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            background: #f4f4f9;
        }

        .container {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #fff;
            padding: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.05);
        }

        .sidebar form input,
        .sidebar form textarea,
        .sidebar form button {
            margin-bottom: 10px;
            width: 100%;
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }

        .sidebar form button {
            background: #6c63ff;
            color: white;
            border: none;
            cursor: pointer;
        }

        .main {
            flex: 1;
            padding: 20px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            background: #eee;
        }

        .card-content {
            padding: 15px;
            flex: 1;
        }

        .card-actions {
            text-align: right;
            padding: 10px;
            border-top: 1px solid #eee;
        }

        .card-actions a {
            margin-left: 10px;
            color: #6c63ff;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="sidebar">
            <h2>Create Article</h2>
            <?php if ($error): ?>
                <p style="color: red; font-size: 12px;"><?= $error ?></p>
            <?php endif; ?>
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="title" placeholder="Title" required>
                <textarea name="content" placeholder="Your content..." rows="5" required></textarea>
                <input type="file" name="image" accept="image/*">
                <button type="submit" name="create">Publish</button>
            </form>
        </div>

        <div class="main">
            <div class="grid">
                <?php foreach ($articles as $a): ?>
                    <div class="card">
                        <?php
                        // Cek file secara fisik di server
                        $imagePath = 'uploads/' . $a['image'];
                        if ($a['image'] && file_exists($uploadDir . $a['image'])):
                        ?>
                            <img src="<?= $imagePath ?>" alt="Image">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/400x180?text=No+Image" alt="No Image">
                        <?php endif; ?>

                        <div class="card-content">
                            <h3><?= htmlspecialchars($a['title']) ?></h3>
                            <p><?= nl2br(htmlspecialchars($a['content'])) ?></p>
                        </div>
                        <div class="card-actions">
                            <a href="edit.php?id=<?= $a['id'] ?>">Edit</a>
                            <a href="?delete=<?= $a['id'] ?>" onclick="return confirm('Hapus?')">Delete</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</body>

</html>