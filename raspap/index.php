<?php
if (in_array(pathinfo(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), PATHINFO_EXTENSION), [
    'jpeg', 'jpg', 'gif', 'css', 'js', 'png', 'ico', 'woff', 'ttf'
]))
{
    return false;
}

require __DIR__. '/.content/app.php';
$handler = new \Panthera\Components\Router\RouteHandler();
$handler->handleRequest();