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

<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f9f9f9;
            color: #333;
        }

        /* Navbar */
        .navbar {
            background: #d8a7d8;
            color: #fff;
            display: flex;
            justify-content: space-between;
            padding: 1rem 2rem;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar .navbar-nav .nav-link {
            color: white !important; /* Enforcing the white color */
            font-weight: bold;
            transition: 0.3s;
        }

        .navbar .navbar-nav .nav-link:hover {
            text-decoration: underline !important; /* Enforcing the underline on hover */
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 600;
        }

        /* Section styling */
        .section {
            padding: 4rem 2rem;
            min-height: 100vh;
        }

        /* Card */
        .card {
            background: #fff;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease forwards;
            opacity: 0;
        }

        /* Button */
        .btn {
            background: #00B1FD;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 30px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-top: 1.5rem;
            transition: background 0.3s ease;
        }

        .btn:hover {
            background: #e64a19;
        }

        /* Animation */
        @keyframes fadeInUp {
            0% {
                opacity: 0;
                transform: translateY(30px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive nav */
        @media (max-width: 768px) {
            .nav-links {
                flex-direction: column;
                gap: 10px;
            }

            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>


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
        .btn{
            background: #00B1FD;
        }
        .btn:hover {
            background: #e64a19;
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
    <nav class="navbar navbar-expand-lg  px-3">
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
            <button type="submit" class="btn">Submit Feedback</button>
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
