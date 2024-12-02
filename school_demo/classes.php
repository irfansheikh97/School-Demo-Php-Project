<?php
// Include database connection
include 'db_connection.php';

// Handle form submission for adding a new class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_class'])) {
    $class_name = $_POST['class_name'];
    if (!empty($class_name)) {
        $stmt = $conn->prepare("INSERT INTO classes (name, created_at) VALUES (?, NOW())");
        $stmt->bind_param("s", $class_name);
        $stmt->execute();
        $stmt->close();
        header("Location: classes.php");
        exit;
    }
}

// Handle form submission for editing a class
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_class'])) {
    $class_id = $_POST['class_id'];
    $class_name = $_POST['class_name'];
    if (!empty($class_name) && !empty($class_id)) {
        $stmt = $conn->prepare("UPDATE classes SET name = ? WHERE class_id = ?");
        $stmt->bind_param("si", $class_name, $class_id);
        $stmt->execute();
        $stmt->close();
        header("Location: classes.php");
        exit;
    }
}

// Handle delete functionality
if (isset($_GET['delete_id'])) {
    $class_id = $_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $stmt->close();
    header("Location: classes.php");
    exit;
}

// Fetch all classes
$classes = $conn->query("SELECT * FROM classes");

// Fetch class details for editing if edit_id is provided
$edit_class = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $conn->prepare("SELECT * FROM classes WHERE class_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $edit_class = $result->fetch_assoc();
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Classes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h1 style="text-align: center;">Manage Classes</h1>

        <!-- Add or Edit Form -->
        <form action="classes.php" method="POST" class="mb-4">
            <?php if ($edit_class): ?>
                <h2>Edit Class</h2>
                <input type="hidden" name="class_id" value="<?= $edit_class['class_id'] ?>">
            <?php else: ?>
                <h4>Add New Class</h4>
            <?php endif; ?>
            <div class="mb-3">
                <label for="class_name" class="form-label">Class Name</label>
                <input type="text" name="class_name" id="class_name" class="form-control"
                    value="<?= $edit_class ? htmlspecialchars($edit_class['name']) : '' ?>" required>
            </div>
            <button type="submit" name="<?= $edit_class ? 'edit_class' : 'add_class' ?>" class="btn btn-primary">
                <?= $edit_class ? 'Update Class' : 'Add Class' ?>
            </button>
            <?php if ($edit_class): ?>
                <a href="classes.php" class="btn btn-secondary">Cancel</a>
            <?php endif; ?>
        </form>

        <!-- Class List -->
        <h2>Class List</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($class = $classes->fetch_assoc()): ?>
                    <tr>
                        <td><?= $class['class_id'] ?></td>
                        <td><?= htmlspecialchars($class['name']) ?></td>
                        <td>
                            <a href="classes.php?edit_id=<?= $class['class_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                            <a href="classes.php?delete_id=<?= $class['class_id'] ?>" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this class?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>

</html>