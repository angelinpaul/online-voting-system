<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('connection.php');

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$query = "SELECT candidates.name, COUNT(votes.id) as vote_count 
          FROM candidates 
          LEFT JOIN votes ON candidates.id = votes.candidate_id 
          GROUP BY candidates.id";
$results = $conn->query($query);

if (!$results) {
    die("Error fetching results: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f9;
            color: #333;
        }
        h1, h2 {
            color: #007acc;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007acc;
            color: white;
        }
        a {
            color: #007acc;
            text-decoration: none;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Admin Panel</h1>
    
    <h2>Manage Candidates</h2>
    <a href="add_candidate.php">Add Candidate</a>

    <h2>Results</h2>
    <a href="view_result.php">View Election Results</a>

    <h2>Candidate Votes</h2>
    <?php if ($results->num_rows > 0): ?>
        <table>
            <tr>
                <th>Candidate Name</th>
                <th>Vote Count</th>
            </tr>
            <?php while ($row = $results->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo $row['vote_count']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No candidates available or no votes recorded yet.</p>
    <?php endif; ?>
</body>
</html>
