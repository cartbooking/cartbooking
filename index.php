<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app.php';
$userId = $_COOKIE['login'];
if (!$userId) {
    return (new \Symfony\Component\HttpFoundation\RedirectResponse('/login.php'))->send();
}
$app->run();
