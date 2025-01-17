<?php
include 'db.php'; // Verbind met de database

// Haal alle quizzen op
$sql = "SELECT * FROM quizzen";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizzen</title>
</head>
<body>
    <h1>Beschikbare Quizzen</h1>
    <ul>
        <?php while ($quiz = $result->fetch_assoc()): ?>
            <li>
                <a href="play_quiz.php?quiz_id=<?= $quiz['id']; ?>"><?= htmlspecialchars($quiz['naam']); ?></a>
                <?php if (!empty($quiz['afbeelding'])): ?>
                    <img src="<?= htmlspecialchars($quiz['afbeelding']); ?>" alt="Quiz afbeelding" width="50">
                <?php endif; ?>
            </li>
        <?php endwhile; ?>
    </ul>
    <a href="index.php">Terug naar Quizbeheer</a>
</body>
</html>
