<?php
// Include database connection
include 'db_connection.php';

// Get the student ID from the URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid student ID.");
}

// Fetch student details
$query = "SELECT student.*, classes.name AS class_name 
          FROM student 
          LEFT JOIN classes ON student.class_id = classes.class_id 
          WHERE student.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found.");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 style="text-align: center;">Student Details</h1>
        <p><strong>Name: </strong> <?= htmlspecialchars($student['name']) ?></p>
        <p><strong>Email: </strong> <?= htmlspecialchars($student['email']) ?></p>
        <p><strong>Address: </strong> <?= htmlspecialchars($student['address']) ?></p>
        <p><strong>Class: </strong> <?= htmlspecialchars($student['class_name']) ?></p>
        <p><strong>Created At: </strong> <?= htmlspecialchars($student['created_at']) ?></p>
        <img src="uploads/<?= htmlspecialchars($student['image']) ?>" alt="Student Image" class="img-thumbnail" width="200">
        <br><br>
        <a href="home.php" class="btn btn-secondary">Back to Home</a>
    </div>
</body>

</html>