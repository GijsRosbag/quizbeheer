<?php
require 'db.php'; // Verbind met de database

// Check of een quiz-ID is meegegeven
if (!isset($_GET['quiz_id'])) {
    echo "Geen quiz geselecteerd.";
    exit;
}

$quiz_id = intval($_GET['quiz_id']);
$current_question = isset($_GET['current_question']) ? intval($_GET['current_question']) : 1;

// Haal quizinformatie op
$quiz_query = $conn->prepare("SELECT * FROM quizzen WHERE id = ?");
$quiz_query->bind_param("i", $quiz_id);
$quiz_query->execute();
$quiz_result = $quiz_query->get_result();

if ($quiz_result->num_rows === 0) {
    echo "Quiz niet gevonden.";
    exit;
}

$quiz = $quiz_result->fetch_assoc();

// Haal de vragen van de quiz op
$questions_query = $conn->prepare("SELECT * FROM vragen WHERE quiz_id = ? ORDER BY id");
$questions_query->bind_param("i", $quiz_id);
$questions_query->execute();
$questions_result = $questions_query->get_result();
$questions = $questions_result->fetch_all(MYSQLI_ASSOC);

$total_questions = count($questions);

// Controleer of er geen vragen zijn
if ($total_questions === 0) {
    echo "<h1>Geen Vragen Beschikbaar</h1>";
    echo "<p>Er zijn geen vragen voor deze quiz. Kies een andere quiz.</p>";
    echo "<a href='quizzen.php'><button>Speel een andere quiz</button></a>";
    echo "<a href='index.php'><button>Terug naar Quizbeheer</button></a>";
    exit;
}

// Start sessie
session_start();

// Personalisatie: Begroet de gebruiker als deze is ingelogd
if (isset($_SESSION['username'])) {
    echo "<p>Welkom terug, " . htmlspecialchars($_SESSION['username']) . "!</p>";
}

if (!isset($_SESSION['answers'])) {
    $_SESSION['answers'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['answer'])) {
        $_SESSION['answers'][$current_question] = $_POST['answer'];
    }

    if (isset($_POST['next'])) {
        header("Location: play_quiz.php?quiz_id=$quiz_id&current_question=" . ($current_question + 1));
        exit;
    }

    if (isset($_POST['prev'])) {
        header("Location: play_quiz.php?quiz_id=$quiz_id&current_question=" . ($current_question - 1));
        exit;
    }
}

if ($current_question > $total_questions) {
    // Quiz is voltooid
    echo "<h1>Resultaat: " . htmlspecialchars($quiz['naam']) . "</h1>";
    echo "<table border='1' style='width: 100%; border-collapse: collapse; text-align: left;'>";
    echo "<thead>
    <tr>
        <th style='width: 50%; padding: 10px; text-align: left;'>Vraag</th>
        <th style='width: 50%; padding: 10px; text-align: left;'>Antwoorden</th>
    </tr>
  </thead>";

    echo "<tbody>";

    $score = 0;
    foreach ($questions as $index => $question) {
        $question_id = $question['id'];
        $user_answer = $_SESSION['answers'][$index + 1] ?? "Geen antwoord";
        $correct_answer = $question['correct_antwoord'];

        echo "<tr>";
        // Vraag Kolom
        echo "<td style='padding: 10px; vertical-align: top;'>" . htmlspecialchars($question['vraagtekst']) . "</td>";

        // Antwoorden Kolom
        echo "<td style='padding: 10px; vertical-align: top;'>";

        // Opties weergeven
        foreach (['a', 'b', 'c', 'd'] as $option) {
            $option_text = htmlspecialchars($question["optie_$option"]);
            if ($user_answer === strtoupper($option) && $user_answer === $correct_answer) {
                // Correct antwoord door gebruiker
                echo "<span style='color: green; font-weight: bold;'><strong>$option:</strong> $option_text</span><br>";
                $score++;
            } elseif ($user_answer === strtoupper($option)) {
                // Fout antwoord door gebruiker
                echo "<span style='color: red; font-weight: bold;'><strong>$option:</strong> $option_text</span><br>";
            } elseif ($correct_answer === strtoupper($option)) {
                // Correct antwoord dat de gebruiker gemist heeft
                echo "<span style='color: green; font-weight: bold;'><strong>$option:</strong> $option_text</span><br>";
            } else {
                // Overige opties
                echo "<span><strong>$option:</strong> $option_text</span><br>";
            }
        }

        echo "</td>";
        echo "</tr>";
    }

    echo "</tbody>";
    echo "</table>";
    echo "<p><strong>U heeft $score van de $total_questions vragen correct beantwoord!</strong></p>";
    echo "<a href='quizzen.php'><button>Speel een andere quiz</button></a>";
    echo "<a href='index.php'><button>Terug naar Quizbeheer</button></a>";
    session_destroy();
    exit;
}

// Huidige vraag ophalen
$current_question_data = $questions[$current_question - 1];
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Speel Quiz: <?= htmlspecialchars($quiz['naam']) ?></title>
    <!-- Voeg Google Fonts toe -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles/style.css">
</head>

<body>
    <h1><?= htmlspecialchars($quiz['naam']) ?></h1>
    <p>Vraag <?= $current_question ?> van <?= $total_questions ?></p>
    <form method="POST">
        <fieldset>
            <legend><?= htmlspecialchars($current_question_data['vraagtekst']) ?></legend>
            <label>
                <input type="radio" name="answer" value="A" <?= (isset($_SESSION['answers'][$current_question]) && $_SESSION['answers'][$current_question] === 'A') ? 'checked' : '' ?>>
                <?= htmlspecialchars($current_question_data['optie_a']) ?>
            </label><br>
            <label>
                <input type="radio" name="answer" value="B" <?= (isset($_SESSION['answers'][$current_question]) && $_SESSION['answers'][$current_question] === 'B') ? 'checked' : '' ?>>
                <?= htmlspecialchars($current_question_data['optie_b']) ?>
            </label><br>
            <label>
                <input type="radio" name="answer" value="C" <?= (isset($_SESSION['answers'][$current_question]) && $_SESSION['answers'][$current_question] === 'C') ? 'checked' : '' ?>>
                <?= htmlspecialchars($current_question_data['optie_c']) ?>
            </label><br>
            <label>
                <input type="radio" name="answer" value="D" <?= (isset($_SESSION['answers'][$current_question]) && $_SESSION['answers'][$current_question] === 'D') ? 'checked' : '' ?>>
                <?= htmlspecialchars($current_question_data['optie_d']) ?>
            </label><br>
            <?php if (!empty($quiz['afbeelding'])): ?>
                <img src="<?= htmlspecialchars($quiz['afbeelding']) ?>"
                    alt="Afbeelding van <?= htmlspecialchars($quiz['naam']) ?>"
                    style="width: 100%; max-width: 500px; height: auto; margin-bottom: 20px;">
            <?php endif; ?>
        </fieldset>
        <div>
            <?php if ($current_question < $total_questions): ?>
                <button type="submit" name="next">Volgende</button>
            <?php else: ?>
                <button type="submit" name="next">Afronden</button>
            <?php endif; ?>
            <?php if ($current_question > 1): ?>
                <button type="submit" name="prev">Vorige</button>
            <?php endif; ?>
        </div>
    </form>
</body>

</html>