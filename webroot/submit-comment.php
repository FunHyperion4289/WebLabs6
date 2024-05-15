<?php
require_once("funcs/functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleId = (int)($_POST['article_id'] ?? 0);
    $author = trim((string)($_POST['name'] ?? null));
    $rate = (int)($_POST['rate'] ?? null);
    $content = trim((string)($_POST['content'] ?? null));

    $errors = [];

    // Validate fields
    if (empty($author)) {
        $errors['name'] = 'Name field is required.';
    } elseif (mb_strlen($author) > 50) {
        $errors['name'] = 'Name length can not be more than 50 characters.';
    }

    if ($rate < 1 || $rate > 5) {
        $errors['rate'] = 'Invalid rate.';
    }

    if (empty($content)) {
        $errors['content'] = 'Content field is required.';
    } elseif (mb_strlen($content) > 200) {
        $errors['content'] = 'Content length can not be more than 200 characters.';
    }

    // If no errors, proceed with insertion
    if (count($errors) === 0) {
        $db = getDbConnection();
        $createdAt = date('Y-m-d H:i:s');
        $query = "INSERT INTO `comments` 
            (`id`, `article_id`, `author`, `rate`, `content`, `created`) VALUES 
            (NULL, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$articleId, $author, $rate, $content, $createdAt]);

        if ($result) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                // AJAX request - Return new comment HTML
                echo '
                    
                      <div class="comment">
                    <b class="comment-author" title="Comment author">' . htmlspecialchars($author) . '</b>
                    <time class="comment-date" title="Comment time" datetime="' . $createdAt . '">' . $createdAt . '</time>
                    <span class="comment-rate" title="Rating">' . str_repeat('✨', $rate) . '</span>
                    <p class="comment-content">' . nl2br(htmlspecialchars($content), false) . '</p>
                    </div>
                    
                ';
            } else {
                // Traditional form submission
                header("Location: " . $_SERVER["PHP_SELF"] . "?id={$articleId}");
                exit;

            }
        } else {
            // Handle database error
            $errors['db'] = "Error saving comment.";
        }
    } else {
        // If there are validation errors, return them as JSON for AJAX

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            echo json_encode(['error' => 'The message was not sent, some of the fields are empty.']);
        }
    }
}
?>