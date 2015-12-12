<?php
require __DIR__. '/.content/app.php';
$handler = new \Panthera\Components\Router\RouteHandler();
$handler->handleRequest();