<?php
session_start();
include('connection.php');
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied: Admins only.");
}
$query = "
    SELECT candidates.id, candidates.name, COUNT(votes.candidate_id) AS vote_count
    FROM candidates
    LEFT JOIN votes ON candidates.id = votes.candidate_id
    GROUP BY candidates.id
    ORDER BY vote_count DESC;
";

$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        header {
            text-align: center;
            margin-bottom: 20px;
        }
        header h1 {
            color: #007acc;
        }
        main {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: left;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
        th {
            background-color: #007acc;
            color: white;
        }
        footer {
            margin-top: 20px;
            text-align: center;
        }
        footer p {
            color: #aaa;
        }
    </style>
</head>
<body>
    <header>
        <h1>Election Results</h1>
    </header>
    <main>
        <table>
            <tr>
                <th>Candidate</th>
                <th>Votes</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['vote_count']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <br>
        <a href="admin.php" class="logout-button">Back to Admin Panel</a>
    </main>
   
</body>
</html>
