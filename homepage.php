<?php
session_start();
include('connect.php');
include('user.php');
include('recipe.php');
include('ingredient.php'); 
include('review.php'); 
include('comment.php'); 


if (!isset($_SESSION['Email'])) {
    echo "Session not found. Please log in.";
    exit();
}

$email = $_SESSION['Email'];

$stmt = $conn->prepare("SELECT * FROM users WHERE Email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$userData = $stmt->get_result()->fetch_assoc();

if (!$userData) {
    echo "User not found!";
    exit();
}

$recentRecipesStmt = $conn->prepare("
    SELECT recipes.*, users.Fname, users.Lname, users.profile_pic, 
    (SELECT COUNT(*) FROM post_likes WHERE post_likes.postId = recipes.id) AS like_count,
    (SELECT COUNT(*) FROM post_likes WHERE post_likes.postId = recipes.id AND post_likes.userId = ?) AS user_liked
    FROM recipes
    JOIN users ON recipes.userId = users.id
    ORDER BY recipes.created_at DESC
");
$recentRecipesStmt->bind_param("i", $userData['id']);
$recentRecipesStmt->execute();
$recentRecipes = $recentRecipesStmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link rel="stylesheet" href="style/homepage.css"> 
</head>
<body>

    <div class="navbar">
        <div class="logo">
            <h1>FoodLovers</h1>
        </div>
        <div class="nav-links">
            <a href="homepage.php">Home</a>
            <a href="about_us.php">About Us</a>
            <a href="contact.php">Contact Us</a>
            <a href="profile.php">Profile</a> 
        </div>
    </div>

    <div class="container">
        <h1>Recent Posts</h1>
        <div class="posts-feed">
            <?php
            if ($recentRecipes && $recentRecipes->num_rows > 0) {
                while ($recipe = $recentRecipes->fetch_assoc()) {
                    echo "<div class='recipe-post'>";
                    echo "<div class='user-info'>";
                    $profilePicPath = !empty($recipe['profile_pic']) ? htmlspecialchars($recipe['profile_pic']) : 'uploads/default.jpg';
                    echo "<img src='" . $profilePicPath . "' alt='Profile Picture' class='profile-pic'>";
                    echo "<h4>" . htmlspecialchars($recipe['Fname'] . " " . $recipe['Lname']) . "</h4>";
                    echo "</div>";
                    echo "<h3>" . htmlspecialchars($recipe['title']) . "</h3>";
                    echo "<p>" . htmlspecialchars($recipe['description']) . "</p>";
                    echo "<p class='timestamp'>Posted on " . date("F j, Y, g:i a", strtotime($recipe['created_at'])) . "</p>";

                    if (!empty($recipe['image_path'])) {
                        $imagePath = htmlspecialchars($recipe['image_path']);
                        echo "<img src='" . $imagePath . "' alt='Recipe Image' style='width:300px;'>";
                    }

                    $userLiked = $recipe['user_liked'] > 0;
                    $likeText = $userLiked ? "Unlike" : "Like";
                    echo "<form action='likes_post.php' method='POST' style='display:inline-block;'>";
                    echo "<input type='hidden' name='post_id' value='" . $recipe['id'] . "'>";
                    echo "<button type='submit'>" . $likeText . "</button>";
                    echo "</form>";
                    echo "<span>" . $recipe['like_count'] . " likes</span>";

                    
                    $ingredientObj = new Ingredient($recipe['id']);
                    $ingredientsResult = $ingredientObj->getIngredients($conn);

                    echo "<h4>Ingredients:</h4>";
                    if ($ingredientsResult && $ingredientRow = mysqli_fetch_assoc($ingredientsResult)) {
                      
                        echo "<p>" . htmlspecialchars($ingredientRow['ingredients']) . "</p>";
                    } else {
                        echo "<p>No ingredients found.</p>";
                    }

                    echo "<h4>Comments:</h4>";
                    $commentObj = new Comment($conn, $recipe['id'], $userData['id'], '');
                    $comments = $commentObj->getComments();

                    foreach ($comments as $comment) {
                        echo "<p><strong>" . htmlspecialchars($comment['Fname']) . " " . htmlspecialchars($comment['Lname']) . ":</strong> " . htmlspecialchars($comment['comment']) . "</p>";
                    }

                    echo "<form action='post_comment.php' method='POST'>
                            <textarea name='comment' required></textarea>
                            <input type='hidden' name='recipe_id' value='" . $recipe['id'] . "'>
                            <button type='submit'>Post Comment</button>
                        </form>";

                    echo "</div>";
                }
            } else {
                echo "<p>No recent posts found.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>
