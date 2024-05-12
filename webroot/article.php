<?php
    require_once("funcs/functions.php");

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

