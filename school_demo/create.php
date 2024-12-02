<!-- NEW CODE -->

<?php
// Include database connection
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collect form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $address = $_POST['address'];
    $class_id = $_POST['class_id'];

    // Handle image upload
    $image = $_FILES['image'];
    $image_path = '';

    if (!empty($image['name'])) {
        $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
        $valid_extensions = ['jpg', 'jpeg', 'png'];

        if (in_array($ext, $valid_extensions)) {
            $image_path = uniqid() . '.' . $ext;
            move_uploaded_file($image['tmp_name'], "uploads/$image_path");
        } else {
            die("Invalid image format. Only JPG and PNG are allowed.");
        }
    }

    // Insert student data into the database
    if (!empty($name)) {
        $stmt = $conn->prepare("INSERT INTO student (name, email, address, class_id, image) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssds", $name, $email, $address, $class_id, $image_path);
        $stmt->execute();
        $stmt->close();

        // Redirect to the home page
        header("Location: home.php");
        exit;
    } else {
        die("Name field is required.");
    }
}

// Fetch classes for dropdown
$classes = $conn->query("SELECT * FROM classes");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 style="text-align: center;">Add Student Details</h1>
        <form action="create.php" method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control">
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Address</label>
                <textarea name="address" id="address" class="form-control"></textarea>
            </div>
            <div class="mb-3">
                <label for="class_id" class="form-label">Class</label>
                <select name="class_id" id="class_id" class="form-control">
                    <?php while ($class = $classes->fetch_assoc()): ?>
                        <option value="<?= $class['class_id'] ?>"><?= $class['name'] ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" name="image" id="image" class="form-control" accept="image/png, image/jpg, image/jpeg">
            </div>
            <button type="submit" class="btn btn-primary">Create</button>
            <br><br>
            <a href="home.php" class="btn btn-secondary">Back to Home</a>
        </form>
    </div>
</body>

</html>