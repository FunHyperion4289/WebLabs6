<?php /** * @var array $errors * @var array $comments * @var integer $articleId */?>

<?php
if (!isset($errors)) {
    $errors = [];
}

// Check if the form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'new-comment') {
    // Process the form data (same as in submit-comment.php)
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
            // Instead of echoing the HTML here, redirect back to the article page
            header("Location: article.php?id=$articleId");
            exit(); // Stop further execution
        } else {
            $errors['submit'] = "Error saving comment";
        }
    }
}
?>

<section class="article-comments">
    <h2>Comments (<?=count($comments) ?>)</h2>

    <!-- Form now submits to the same page (comments.php) -->
    <form id="new-comment-form" method="post" class="form-container">
        <input type="hidden" name="action" value="new-comment">
        <input type="hidden" name="article_id" value="<?= $articleId ?>">

        <div class="form-input-group">
            <input class="form-input-field" value="<?= htmlspecialchars($_POST['name']??'');?>" type="text" name="name" id="nameField" max="50" placeholder="Your name" require>
            <?php if (array_key_exists('name', $errors)) { ?>
                <div class="input-error"><?= $errors['name']; ?></div>
            <?php } ?>
        </div>

        <div class="form-input-group">
            <select class="form-input-field" name="rate" id="rateField" require>
                <option selected disabled>Rate this post</option>
                <?php
                $ratings = [5, 4, 3, 2, 1];
                foreach ($ratings as $rating) {
                    $selected = (int)($_POST['rate']?? '') === $rating ? 'selected' : '';
                    echo "<option value='$rating' $selected>" . str_repeat('★', $rating) . "</option>";
                }
                ?>
            </select>
            <?php if (array_key_exists('rate', $errors)) { ?>
                <div class="input-error"><?= $errors['rate']; ?></div>
            <?php } ?>
        </div>

        <div class="form-input-group">
            <textarea class="form-input-field" name="content" id="contentField" cols="30" rows="10" max="200" placeholder="Write your comment here" require><?= htmlspecialchars($_POST['content']??''); ?></textarea>
            <?php if (array_key_exists('content', $errors)) { ?>
                <div class="input-error"><?= $errors['content']; ?></div>
            <?php } ?>
        </div>

        <!-- Display general submit error (if any) -->
        <?php if (array_key_exists('submit', $errors)) { ?>
            <div class="input-error"><?= $errors['submit']; ?></div>
        <?php } ?>

        <button type="submit" class='btn'>Leave comment</button>
    </form>

    <div class="comments">
        <?php foreach ($comments as $comment) { ?>
            <div class="comment">
                <b class='comment-author' title='Comment author' ><?= htmlspecialchars($comment['author']); ?></b>
                <time class='comment-date' title='Comment time' datetime="<?= $comment['created']; ?>"><?= $comment['created']; ?></time>
                <span class='comment-rate' title='Rating'><?= str_repeat('✨', $comment['rate']); ?></span>
                <p class='comment-content'><?= nl2br(htmlspecialchars($comment['content']), false); ?></p>
            </div>
        <?php } ?>
    </div>
</section>

<script>
    $(document).ready(function() {
        $("#new-comment-form").submit(function(event) {
            event.preventDefault();
            var formData = $(this).serialize();
            $.ajax({
                url: 'submit-comment.php',
                type: 'POST',
                data: formData,
                success: function(response) {
                    $(".comments").append(response);
                    $("#new-comment-form")[0].reset();
                },
                error: function(xhr, status, error) {
                    console.error("AJAX request failed: ", status, error);
                    alert("Error submitting comment. Please try again later.");
                }
            });
        });
    });
</script>