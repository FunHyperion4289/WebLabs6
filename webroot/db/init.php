<?php
require_once("../funcs/functions.php");

$db = getDbConnection();

$query = 'CREATE TABLE IF NOT EXISTS `articles` (
    `id` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `title` TEXT NOT NULL,
    `category` TEXT NOT NULL,
    `content` TEXT NOT NULL,
    `author` TEXT NOT NULL,
    `created` datetime NOT NULL,
    `image` TEXT NOT NULL
    );';
$db->exec($query);

$query = 'CREATE TABLE IF NOT EXISTS `comments` (
    `id` BIGINT UNSIGNED NOT NULL PRIMARY KEY AUTO_INCREMENT,
    `article_id` BIGINT UNSIGNED NOT NULL,
    `author` TEXT NOT NULL,
    `rate` TEXT NOT NULL,
    `content` TEXT NOT NULL,
    `created` DATETIME NOT NULL,
    FOREIGN KEY (`article_id`) REFERENCES `articles`(`id`)
    );';

$db->exec($query);

