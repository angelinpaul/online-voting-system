<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied: Admins only.");
}

include('connection.php'); // Correct file name for database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO candidates (name, description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);

    if ($stmt->execute()) {
        echo "Candidate added successfully!";
        header("Location: admin.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Candidate</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <h1>Add Candidate</h1>
    <form method="POST" action="add_candidate.php">
        <label for="name">Candidate Name:</label><br>
        <input type="text" id="name" name="name" required><br><br>

        <label for="description">Candidate Description:</label><br>
        <textarea id="description" name="description"></textarea><br><br>

        <input type="submit" value="Add Candidate">
    </form>
    <a href="admin.php">Back to Admin Panel</a>
</body>
</html>
