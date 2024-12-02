<?php
// Include database connection
include 'db_connection.php';

// Get the student ID from the URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid student ID.");
}

// Fetch the student to delete
$stmt = $conn->prepare("SELECT image FROM student WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found.");
}

// Delete the image file
if (file_exists("uploads/" . $student['image'])) {
    unlink("uploads/" . $student['image']);
}

// Delete the student from the database
$stmt = $conn->prepare("DELETE FROM student WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();

// Redirect to home page
header("Location: home.php");
exit;
