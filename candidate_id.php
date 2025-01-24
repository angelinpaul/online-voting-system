<?php
session_start();
include('connection.php');
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$user_stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$user_stmt->bind_param("s", $username);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();
$user_id = $user['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    if (isset($_POST['candidate_id']) && !empty($_POST['candidate_id'])) {
        $candidate_id = $_POST['candidate_id'];
        $vote_time = date('Y-m-d H:i:s');
        $stmt = $conn->prepare("SELECT * FROM votes WHERE user_id = ? AND candidate_id = ?");
        $stmt->bind_param("ii", $user_id, $candidate_id);
        $stmt->execute();
        $existing_vote = $stmt->get_result()->fetch_assoc();

        if (!$existing_vote) {
            $stmt = $conn->prepare("INSERT INTO votes (user_id, candidate_id, vote_time) VALUES (?, ?, ?)");
            $stmt->bind_param("iis", $user_id, $candidate_id, $vote_time);
            if ($stmt->execute()) {
                echo "<script>alert('Vote recorded successfully!'); window.location.href='results.php';</script>";
            } else {
                echo "<script>alert('Failed to record vote.');</script>";
            }
        } else {
            echo "<script>alert('You have already voted for this candidate.');</script>";
        }
    } else {
        echo "<script>alert('Please select a candidate.');</script>";
    }
}

$candidates = $conn->query("SELECT * FROM candidates");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote</title>
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
            width: 300px;
            text-align: left;
        }
        form div {
            margin-bottom: 15px;
        }
        input[type="radio"] {
            margin-right: 10px;
        }
        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007acc;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        input[type="submit"]:hover {
            background-color: #005f99;
        }
        .logout-button {
            width: 100%;
            padding: 10px;
            margin-top: 15px;
            background-color: #ff4d4d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
            display: block;
            text-decoration: none;
        }
        .logout-button:hover {
            background-color: #cc0000;
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
        <h1>Vote for a Candidate</h1>
    </header>
    <main>
        <form method="POST" action="vote.php">
            <?php while ($candidate = $candidates->fetch_assoc()): ?>
                <div>
                    <input type="radio" id="candidate_<?php echo $candidate['id']; ?>" name="candidate_id" value="<?php echo $candidate['id']; ?>" required>
                    <label for="candidate_<?php echo $candidate['id']; ?>"><?php echo htmlspecialchars($candidate['name']); ?></label>
                </div>
            <?php endwhile; ?>
            <br>
            <input type="submit" value="Submit Vote">
        </form>
        <a href="logout.php" class="logout-button">Logout</a>
    </main>
    <footer>
        <p>&copy; 2024 Club Election Voting System. All rights reserved.</p>
    </footer>
</body>
</html>
