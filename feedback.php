<?php
session_start();
require 'php/db_connect.php';



$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'Guest';

// Handle feedback submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $rating = (int)$_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);

    if ($rating >= 1 && $rating <= 5 && !empty($comment)) {
        $insert = "INSERT INTO feedback (user_id, rating, comment) VALUES ($user_id, $rating, '$comment')";
        mysqli_query($conn, $insert);
    }
}

// Fetch all feedbacks
$feedbacks = [];
$sql = "SELECT f.*, u.fullname FROM feedback f
        JOIN users u ON f.user_id = u.id
        ORDER BY f.created_at DESC";
$result = mysqli_query($conn, $sql);
if ($result) {
    $feedbacks = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Feedback - CRM-ERP Restaurant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .star {
            font-size: 24px;
            color: #ccc;
            cursor: pointer;
        }
        .star.checked {
            color: gold;
        }
    </style>
    <script>
        function setStars(value) {
            let stars = document.querySelectorAll('.star');
            stars.forEach((star, index) => {
                star.classList.toggle('checked', index < value);
            });
            document.getElementById('rating').value = value;
        }
    </script>
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-3">
        <a class="navbar-brand" href="#">🍽️ CRM-ERP Restaurant</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="homepage.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="view_menu.php">View Menu</a></li>
                <li class="nav-item"><a class="nav-link" href="view_orders.php">View Orders</a></li>
                <li class="nav-item"><a class="nav-link active" href="#">Add Feedback</a></li>
                <li class="nav-item"><a class="nav-link text-danger" href="logout.php">Logout 🔒</a></li>
            </ul>
        </div>
    </nav>

    <div class="container py-4">
        <h2 class="mb-4 text-center">Rate Our Restaurant</h2>
        <form method="POST" class="bg-white p-4 rounded shadow-sm">
            <div class="mb-3">
                <label class="form-label">Your Rating:</label><br>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <span class="star" onclick="setStars(<?= $i ?>)">&#9733;</span>
                <?php endfor; ?>
                <input type="hidden" name="rating" id="rating" value="0" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Your Feedback:</label>
                <textarea name="comment" rows="4" class="form-control" placeholder="Tell us what you think..." required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Submit Feedback</button>
        </form>

        <hr class="my-5">

        <h3 class="mb-3">What Others Are Saying:</h3>
        <?php if (count($feedbacks) > 0): ?>
            <?php foreach ($feedbacks as $fb): ?>
                <div class="border rounded p-3 mb-3 bg-white shadow-sm">
                    <strong><?= htmlspecialchars($fb['fullname']) ?></strong>
                    <div>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <span class="star <?= $i <= $fb['rating'] ? 'checked' : '' ?>">&#9733;</span>
                        <?php endfor; ?>
                    </div>
                    <p class="mt-2"><?= htmlspecialchars($fb['comment']) ?></p>
                    <small class="text-muted"><?= $fb['created_at'] ?></small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No feedback yet. Be the first!</p>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
