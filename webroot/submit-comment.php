<?php
require_once("funcs/functions.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $articleId = (int)($_POST['article_id'] ?? 0);
    $author = trim((string)($_POST['name'] ?? null));
    $rate = (int)($_POST['rate'] ?? null);
    $content = trim((string)($_POST['content'] ?? null));

    $errors = [];

    if (0 === count($errors)) {
        $db = getDbConnection();
        $createdAt = date('Y-m-d H:i:s');
        $query = "INSERT INTO `comments` 
            (`id`, `article_id`, `author`, `rate`, `content`, `created`) VALUES 
            (NULL, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$articleId, $author, $rate, $content, $createdAt]);

        if ($result) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                // AJAX request
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
            $errors['db'] = "Error saving comment";
        }
    }
}
?>