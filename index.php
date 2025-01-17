<?php
include 'db.php';  // Databaseverbinding

// Ophalen van alle quizzes
$sql = "SELECT * FROM quizzen";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quizbeheer</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }
        h1 {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 20px;
        }
        a {
            text-decoration: none;
            color: #fff;
            background-color: #28a745;
            padding: 10px 20px;
            border-radius: 5px;
            margin: 10px;
        }
        a:hover {
            background-color: #218838;
        }
        .container {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .actions a {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <h1>Welkom bij het Quizbeheer</h1>

    <div class="container">
        <!-- Knoppen voor CRUD-acties -->
        <p><a href="create_quiz.php">Voeg een nieuwe quiz toe</a></p><br>
        <p><a href="quizzen.php">Speel de gemaakte quizzen!</a></p>


        <h2>Huidige Quizzen</h2>
        <table>
            <tr>
                <th>Quiz Naam</th>
                <th>Beschrijving</th>
                <th>Acties</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['naam']); ?></td>
                    <td><?= htmlspecialchars($row['beschrijving']); ?></td>
                    <td class="actions">
                        <a href="update_quiz.php?id=<?= $row['id']; ?>">Bewerken</a>
                        <a href="delete_quiz.php?id=<?= $row['id']; ?>" onclick="return confirm('Weet je zeker dat je deze quiz wilt verwijderen?')">Verwijderen</a>
                        <a href="add_question.php?quiz_id=<?= $row['id']; ?>">Voeg vraag toe</a> <!-- Nieuwe knop voor het toevoegen van vragen -->
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>

</body>
</html>
