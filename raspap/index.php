<?php
$parsed = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

if (in_array(pathinfo(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), PATHINFO_EXTENSION), [
    'jpeg', 'jpg', 'gif', 'css', 'js', 'png', 'ico', 'woff', 'ttf'
]))
{
    return false;
}
elseif (in_array(strtolower(pathinfo($parsed, PATHINFO_BASENAME)), [
    '.gitignore', 'composer.json', 'LICENSE', 'README.md',
]))
{
    header('HTTP/1.1 403 Forbidden');
    exit;
}

require './vendor/pantheraframework/panthera/lib/init.php';
$handler = new \Panthera\Components\Router\RouteHandler();
$handler->handleRequest();