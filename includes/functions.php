<?php

// Конекшн до бази
function getDbConnection():PDO {
    static $connection;

    if(null===$connection) {
        try {
            $connection = new PDO('mysql:host=localhost;dbname=weblabs',
            'root', '1234');
            return $connection;
        } catch (PDOException $e) {
            http_response_code(500);
            echo "Error!: {$e->getMessage()}";
            die;
        }   
    }

    return $connection;
}


// Функція для отримання списку статей
function getArticles(): array {
    $db = getDbConnection();

    $query = $db->query('SELECT articles.*, COUNT(comments.id) numbersComments, AVG(comments.rate) avgRate FROM articles 
    LEFT JOIN comments ON comments.article_id = articles.id
    GROUP BY articles.id
    ');
    $articles = $query->fetchAll(PDO::FETCH_ASSOC);
    return  $articles;
}

// Функція для отримання статті
function getArticleData(int $articleId):? array {
    $db = getDbConnection();

    $query = $db->query("SELECT articles.*, COUNT(comments.id) numbersComments, AVG(comments.rate) avgRate 
    FROM articles 
    LEFT JOIN comments ON comments.article_id = articles.id 
    WHERE articles.id = {$articleId} 
    GROUP BY articles.id;
    ");
    $article = $query->fetch(PDO::FETCH_ASSOC);
    return $article ? $article : null;
}

// Функція для отримання коментарів
function getArticleComments(int $articleId): array {
    $db = getDbConnection();

    $query = $db->query("SELECT * FROM comments WHERE article_id = {$articleId}");
    $comments = $query->fetchAll(PDO::FETCH_ASSOC);
    return $comments;
}