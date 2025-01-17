<?php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $naam = $_POST['naam'];
    $beschrijving = $_POST['beschrijving'];

    // Verwerk afbeelding als deze is geüpload
    $afbeelding_pad = '';
    if (isset($_FILES['afbeelding']) && $_FILES['afbeelding']['error'] == 0) {
        $afbeelding = $_FILES['afbeelding'];
        $doelmap = 'uploads/';  // Map waarin je afbeeldingen wilt opslaan
        $doelbestand = $doelmap . basename($afbeelding['name']);

        // Controleer of het bestand een geldig type is (bijv. jpg, png)
        $geldige_extensies = ['jpg', 'jpeg', 'png'];
        $extensie = strtolower(pathinfo($doelbestand, PATHINFO_EXTENSION));

        if (in_array($extensie, $geldige_extensies)) {
            if (move_uploaded_file($afbeelding['tmp_name'], $doelbestand)) {
                $afbeelding_pad = $doelbestand; // Sla het pad naar de afbeelding op
            } else {
                echo "Fout bij het uploaden van de afbeelding.";
            }
        } else {
            echo "Ongeldig bestandstype voor quiz afbeelding. Alleen .jpg, .jpeg, .png toegestaan.";
        }
    }

    // Voeg nieuwe quiz toe met of zonder afbeelding
    $stmt = $conn->prepare("INSERT INTO quizzen (naam, beschrijving, afbeelding) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $naam, $beschrijving, $afbeelding_pad);
    $stmt->execute();

    echo "<p>Quiz succesvol toegevoegd!</p>";
    echo "<a href='index.php'>Terug naar de quizlijst</a>";
} else {
?>
    <h1>Voeg een Nieuwe Quiz Toe</h1>
    <form action="create_quiz.php" method="POST" enctype="multipart/form-data">
        <label for="naam">Quiz Naam:</label>
        <input type="text" id="naam" name="naam" required><br><br>

        <label for="beschrijving">Beschrijving:</label>
        <textarea id="beschrijving" name="beschrijving" required></textarea><br><br>

        <!-- Afbeelding uploaden voor de quiz -->
        <label for="afbeelding">Quiz Afbeelding:</label>
        <input type="file" id="afbeelding" name="afbeelding" accept="image/*"><br><br>

        <button type="submit">Quiz Aanmaken</button>
        
        <a href="index.php">Terug naar Quizbeheer</a>
    </form>
<?php
}
?>
