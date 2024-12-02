<?php
// Include database connection
include 'db_connection.php';

// Get the student ID from the URL
$id = $_GET['id'] ?? null;

if (!$id) {
    die("Invalid student ID.");
}

// Fetch student details for prefilling the form
$query = "SELECT * FROM student WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die("Student not found.");
}

// Fetch classes for dropdown
$classes = $conn->query("SELECT * FROM classes");

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $class_id = $_POST['class_id'];
    $image_path = $student['image']; // Keep the current image by default

    // Handle new image upload if provided
    if (!empty($_FILES['image']['name'])) {
        $image = $_FILES['image'];
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $valid_extensions = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $valid_extensions)) {
            $image_path = uniqid() . '.' . $ext;
            move_uploaded_file($image['tmp_name'], "uploads/$image_path");

            // Delete old image if exists
            if (file_exists("uploads/" . $student['image'])) {
                unlink("uploads/" . $student['image']);
            }
        } else {
            die("Invalid image format. Only JPG and PNG are allowed.");
        }
    }

    // Update student data
    $stmt = $conn->prepare("UPDATE student SET name = ?, email = ?, address = ?, class_id = ?, image = ? WHERE id = ?");
    $stmt->bind_param("sssisi", $name, $email, $address, $class_id, $image_path, $id);
    $stmt->execute();
    $stmt->close();

    // Redirect to home page
    header("Location: home.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1>Edit Student</h1>
        <form action="edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($student['name']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($student['email']) ?>">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control"><?= htmlspecialchars($student['address']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="class_id" class="form-label">Class</label>
                <select name="class_id" id="class_id" class="form-control">
                    <?php while ($class = $classes->fetch_assoc()): ?>
                        <option value="<?= $class['class_id'] ?>" <?= $class['class_id'] == $student['class_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($class['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/png, image/jpeg">
                <br>
                <img src="uploads/<?= htmlspecialchars($student['image']) ?>" width="100" class="img-thumbnail">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
        <br>
        <a href="home.php" class="btn btn-secondary">Back to Home</a>
    </div>
</body>

</html>