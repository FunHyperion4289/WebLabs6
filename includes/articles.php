<?php
/**
 * @var double $averageRate
 * @var integer $numberOfComments
 * @var integer $article
 *
 */
?>

<?php
$url = "article.php?id={$article['id']}";
if ($averageRate === null) {
    $averageRate = 0;
}
else {
    $averageRate = number_format($averageRate, 1);
}
?>

<article class="blog-card">
    <img class="blog-card-image" src="src/img/<?= $article['image']; ?>" loading="lazy" alt="<?= $article['title']; ?> Picture">
    <div class="blog-card-body">
        <header class="blog-card-header">
            <p class="blog-category"><?= $article['category']; ?></p>
            <h2 class="blog-card-heading"><a href="<?= $url; ?>"><?= $article['title']; ?></a></h2>
            <div class="blog-meta">
                <?= $article['author']; ?>
                <time class="meta-info" datetime="<?= $article['created']; ?>"><?= $article['created']; ?></time>
                <span class="meta-info">🗨️<?= $numberOfComments ?></span>
                <span class="meta-info">✨<?= $averageRate ?></span>
            </div>
        </header>
        <div class="blog-card-description">
            <?= $article['content']; ?>
        </div>
    </div>
</article>