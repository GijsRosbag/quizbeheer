<?php
include 'db.php';  // Database connection

// Check if quiz ID is provided
if (isset($_GET['id'])) {
    $quiz_id = $_GET['id'];

    // Begin transaction
    $conn->begin_transaction();

    try {
        // Step 1: Delete all questions related to the quiz
        $delete_questions_sql = "DELETE FROM vragen WHERE quiz_id = ?";
        $stmt = $conn->prepare($delete_questions_sql);
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();

        // Step 2: Delete the quiz itself
        $delete_quiz_sql = "DELETE FROM quizzen WHERE id = ?";
        $stmt = $conn->prepare($delete_quiz_sql);
        $stmt->bind_param("i", $quiz_id);
        $stmt->execute();

        // Commit transaction
        $conn->commit();

        echo "Quiz and associated questions have been deleted.";
        header("Location: index.php");  // Redirect to the quiz management page

    } catch (Exception $e) {
        // If an error occurs, roll back the transaction
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "No quiz ID provided.";
}
?>
