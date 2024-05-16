
<?php
require_once("../includes/functions.php");

$articleId = $_GET['id'] ?? null;

//Параметр в адресі відсутній
if (null === $articleId) {
    http_response_code(400);
    exit();
}

$articleId = (int)$articleId;

//Пошук публікації
$article = getArticleData($articleId);

//Публікацію не знайдено
if (null === $article) {
    http_response_code(404);
    exit();
}


$page_title = "{$article['title']}";
$curPage = "articlePage";
$comments = getArticleComments($articleId);

// Detect AJAX request
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

$errors = [];
if ('new-comment' === ($_POST['action'] ?? null)) {
    $author = trim((string)($_POST['name'] ?? null));
    if ('' === $author) {
        $errors['name'] = 'This field is can not be empty';
    } elseif (mb_strlen($author) > 50) {
        $errors['name'] = 'Length can not be more than 50 characters';
    }

    $rate = (int)($_POST['rate'] ?? null);
    if ($rate < 1 || $rate > 5) {
        $errors['rate'] = 'Invalid rate';
    }

    $content = trim((string)($_POST['content'] ?? null));
    if ('' === $content) {
        $errors['content'] = 'This field is can not be empty';
    } elseif (mb_strlen($content) > 200) {
        $errors['content'] = 'Length can not be more than 200 characters';
    }

    if (0 === count($errors)) {
        $db = getDbConnection();
        $createdAt = date('Y-m-d H:i:s');
        $query = "INSERT INTO `comments` 
                (`id`, `article_id`, `author`, `rate`, `content`, `created`) VALUES 
                (NULL, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($query);
        $result = $stmt->execute([$articleId, $author, $rate, $content, $createdAt]);
    }

    if ($isAjax) {
        // If AJAX request and there are validation errors, return them as JSON
        include("../includes/comments.php");
        exit;
    }
    else if (0 === count($errors)) {
        header("Location: " . $_SERVER["PHP_SELF"] . "?id={$articleId}");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php require("../includes/head.php"); ?>
</head>

<body>
<?php include("../includes/header.php") ?>

<main class="container">
    <article class="blog-post">
        <header class="blog-post-header">
            <p class="blog-category"><?= $article['category']; ?></p>
            <h1 class="blog-post-heading"><?= $article['title']; ?></h1>
            <div class="blog-meta">
                <?= $article['author']; ?>
                <time class="meta-info" datetime="<?= $article['created']; ?>"><?= $article['created']; ?></time>
                <span class="meta-info">🗨️<?= $article['numbersComments']; ?></span>
                <span class="meta-info">✨<?= number_format($article['avgRate'], 2);?></span>
            </div>
            <img src="src/img/<?= $article['image']; ?>" loading="lazy" alt="<?= $article['title']; ?> Picture">
        </header>
        <div class="blog-post-description">
            <?= $article['content']; ?>
        </div>
    </article>

    <?php include("../includes/comments.php") ?>
</main>

<?php include("../includes/footer.php") ?>


</body>

</html>


