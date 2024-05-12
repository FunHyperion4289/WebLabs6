<?php
/**
 * @var string $curPage
 */
?>

<header class="site-header">
    <a class="site-logo" href="index.php">
        <img src="src/img/azalea-logo.png" alt="site_logo">
    </a>

    <nav class="navbar">
        <ul class="nav-links">
            <li class="nav-link <? if ($curPage == "home") { echo "active"; } ?> "><a href="index.php">Homepage</a></li>
            <li class="nav-link <? if ($curPage == "about") { echo "active"; } ?> "><a href="about.php">About us</a></li>
        </ul>
    </nav>
</header>