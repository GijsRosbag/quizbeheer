<?php
include 'db.php';  // Databaseverbinding

// Als het formulier is ingediend
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Haal de gegevens van de vraag en de bijbehorende quiz op
    $quiz_id = $_POST['quiz_id'];
    $vraagtekst = $_POST['vraagtekst'];
    $optie_a = $_POST['optie_a'];
    $optie_b = $_POST['optie_b'];
    $optie_c = $_POST['optie_c'];
    $optie_d = $_POST['optie_d'];
    $correct_antwoord = $_POST['correct_antwoord'];

    // Voeg de vraag toe aan de database
    $stmt = $conn->prepare("INSERT INTO vragen (quiz_id, vraagtekst, optie_a, optie_b, optie_c, optie_d, correct_antwoord) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssss", $quiz_id, $vraagtekst, $optie_a, $optie_b, $optie_c, $optie_d, $correct_antwoord);

    if ($stmt->execute()) {
        $succesbericht = "Vraag is succesvol toegevoegd!";
    } else {
        $foutbericht = "Er is iets mis gegaan bij het toevoegen van de vraag. Probeer het opnieuw.";
    }

    error_reporting(0);  // Zet foutmeldingen uit
ini_set('display_errors', 0);
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voeg een vraag toe</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Voeg een vraag toe aan de quiz</h1>

    <div>
        <?php if (isset($succesbericht)): ?>
            <p style="color: green;"><?= $succesbericht; ?></p>
        <?php elseif (isset($foutbericht)): ?>
            <p style="color: red;"><?= $foutbericht; ?></p>
        <?php endif; ?>
    </div>

    <form action="add_question.php" method="POST">
        <input type="hidden" name="quiz_id" value="<?= $_GET['quiz_id']; ?>">

        <label for="vraagtekst">Vraag:</label>
        <input type="text" id="vraagtekst" name="vraagtekst" required><br><br>

        <label for="optie_a">Optie A:</label>
        <input type="text" id="optie_a" name="optie_a" required><br><br>

        <label for="optie_b">Optie B:</label>
        <input type="text" id="optie_b" name="optie_b" required><br><br>

        <label for="optie_c">Optie C:</label>
        <input type="text" id="optie_c" name="optie_c" required><br><br>

        <label for="optie_d">Optie D:</label>
        <input type="text" id="optie_d" name="optie_d" required><br><br>

        <label for="correct_antwoord">Correct antwoord:</label>
        <select id="correct_antwoord" name="correct_antwoord" required>
            <option value="A">A</option>
            <option value="B">B</option>
            <option value="C">C</option>
            <option value="D">D</option>
        </select><br><br>

        <button type="submit">Vraag toevoegen</button>
    </form>

    <a href='index.php'><button>Terug naar Quizbeheer</button></a>
</body>
</html>
