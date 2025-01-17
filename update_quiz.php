<?php
include 'db.php'; // Databaseverbinding

// Haal de quiz-id op (deze wordt vaak via de URL meegegeven)
$quiz_id = $_GET['id'];

// Haal de quizgegevens op (naam en beschrijving)
$sql_quiz = "SELECT * FROM quizzen WHERE id = ?";
$stmt_quiz = $conn->prepare($sql_quiz);
$stmt_quiz->bind_param("i", $quiz_id);
$stmt_quiz->execute();
$result_quiz = $stmt_quiz->get_result();
$quiz = $result_quiz->fetch_assoc();

// Haal de vragen voor de quiz op
$sql_vragen = "SELECT * FROM vragen WHERE quiz_id = ?";
$stmt_vragen = $conn->prepare($sql_vragen);
$stmt_vragen->bind_param("i", $quiz_id);
$stmt_vragen->execute();
$vragen_result = $stmt_vragen->get_result();

// Verwerken van het formulier
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Quizgegevens bijwerken
    $quiz_naam = $_POST['naam'];
    $quiz_beschrijving = $_POST['beschrijving'];

    // Als een afbeelding voor de quiz is geüpload
    if (isset($_FILES['afbeelding']) && $_FILES['afbeelding']['error'] == 0) {
        $afbeelding = $_FILES['afbeelding'];
        $doelmap = 'uploads/';  // Map waarin je afbeeldingen wilt opslaan
        $doelbestand = $doelmap . basename($afbeelding['name']);

        // Controleer of het bestand een geldig type is (bijv. jpg, png)
        $geldige_extensies = ['jpg', 'jpeg', 'png'];
        $extensie = strtolower(pathinfo($doelbestand, PATHINFO_EXTENSION));

        if (in_array($extensie, $geldige_extensies)) {
            if (move_uploaded_file($afbeelding['tmp_name'], $doelbestand)) {
                // Sla het pad naar de afbeelding op in de database
                $sql_update_quiz = "UPDATE quizzen SET naam = ?, beschrijving = ?, afbeelding = ? WHERE id = ?";
                $stmt_update_quiz = $conn->prepare($sql_update_quiz);
                $stmt_update_quiz->bind_param("sssi", $quiz_naam, $quiz_beschrijving, $doelbestand, $quiz_id);
                $stmt_update_quiz->execute();
            } else {
                echo "Fout bij het uploaden van de afbeelding voor de quiz.";
            }
        } else {
            echo "Ongeldig bestandstype voor quiz afbeelding. Alleen .jpg, .jpeg, .png toegestaan.";
        }
    } else {
        // Als geen afbeelding is geüpload, update de naam en beschrijving zonder afbeelding
        $sql_update_quiz = "UPDATE quizzen SET naam = ?, beschrijving = ? WHERE id = ?";
        $stmt_update_quiz = $conn->prepare($sql_update_quiz);
        $stmt_update_quiz->bind_param("ssi", $quiz_naam, $quiz_beschrijving, $quiz_id);
        $stmt_update_quiz->execute();
    }

    // Vragen bijwerken zonder afbeelding
    if (isset($_POST['vragen'])) {
        foreach ($_POST['vragen'] as $vraag_id => $vraag_data) {
            $vraag_tekst = $vraag_data['tekst'];

            // Update de vraagtekst zonder afbeelding
            $sql_update_vraag = "UPDATE vragen SET vraagtekst = ? WHERE id = ?";
            $stmt_update_vraag = $conn->prepare($sql_update_vraag);
            $stmt_update_vraag->bind_param("si", $vraag_tekst, $vraag_id);
            $stmt_update_vraag->execute();
        }
    }


    echo "Quiz en vragen succesvol bijgewerkt!";
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Bewerken</title>
</head>

<body>
    <h1>Bewerk Quiz: <?= htmlspecialchars($quiz['naam']); ?></h1>

    <!-- Formulier voor het bewerken van de quiz -->
    <form action="update_quiz.php?id=<?= $quiz_id ?>" method="post" enctype="multipart/form-data">
        <label for="naam">Quiz Naam:</label>
        <input type="text" id="naam" name="naam" value="<?= htmlspecialchars($quiz['naam']); ?>" required><br><br>

        <label for="beschrijving">Beschrijving:</label>
        <textarea id="beschrijving" name="beschrijving" rows="4"
            required><?= htmlspecialchars($quiz['beschrijving']); ?></textarea><br><br>

        <!-- Afbeelding uploaden voor de quiz -->
        <label for="afbeelding">Quiz Afbeelding:</label>
        <input type="file" id="afbeelding" name="afbeelding" accept="image/*"><br><br>

        <?php if (!empty($quiz['afbeelding'])): ?>
            <p>Huidige afbeelding:</p>
            <img src="<?= htmlspecialchars($quiz['afbeelding']); ?>" alt="Quiz afbeelding" width="150"><br><br>
        <?php endif; ?>

        <h2>Vragen Bewerken</h2>
        <?php
        $vraag_nummer = 1;
        while ($vraag = $vragen_result->fetch_assoc()): ?>
            <div class="vraag">
                <h3>Vraag <?= $vraag_nummer; ?>: <?= htmlspecialchars($vraag['vraagtekst']); ?></h3>

                <label for="vraagtekst_<?= $vraag['id']; ?>">Vraagtekst:</label>
                <input type="text" name="vragen[<?= $vraag['id']; ?>][tekst]"
                    value="<?= htmlspecialchars($vraag['vraagtekst']); ?>" required><br><br>

                <!-- Opties en correct antwoord -->
                <label for="optie_a_<?= $vraag['id']; ?>">Optie A:</label>
                <input type="text" name="vragen[<?= $vraag['id']; ?>][optie_a]"
                    value="<?= htmlspecialchars($vraag['optie_a']); ?>" required><br><br>

                <label for="optie_b_<?= $vraag['id']; ?>">Optie B:</label>
                <input type="text" name="vragen[<?= $vraag['id']; ?>][optie_b]"
                    value="<?= htmlspecialchars($vraag['optie_b']); ?>" required><br><br>

                <label for="optie_c_<?= $vraag['id']; ?>">Optie C:</label>
                <input type="text" name="vragen[<?= $vraag['id']; ?>][optie_c]"
                    value="<?= htmlspecialchars($vraag['optie_c']); ?>" required><br><br>

                <label for="optie_d_<?= $vraag['id']; ?>">Optie D:</label>
                <input type="text" name="vragen[<?= $vraag['id']; ?>][optie_d]"
                    value="<?= htmlspecialchars($vraag['optie_d']); ?>" required><br><br>

                <label for="correct_antwoord_<?= $vraag['id']; ?>">Correct Antwoord:</label>
                <select name="vragen[<?= $vraag['id']; ?>][correct_antwoord]" required>
                    <option value="A" <?= ($vraag['correct_antwoord'] == 'A') ? 'selected' : ''; ?>>A</option>
                    <option value="B" <?= ($vraag['correct_antwoord'] == 'B') ? 'selected' : ''; ?>>B</option>
                    <option value="C" <?= ($vraag['correct_antwoord'] == 'C') ? 'selected' : ''; ?>>C</option>
                    <option value="D" <?= ($vraag['correct_antwoord'] == 'D') ? 'selected' : ''; ?>>D</option>
                </select><br><br>
            </div>
            <?php
            $vraag_nummer++;
        endwhile; ?>


        <button type="submit">Update Quiz en Vragen</button>

        <p><a href="index.php">Terug naar quizbeheer</a></p>

    </form>

</body>

</html>