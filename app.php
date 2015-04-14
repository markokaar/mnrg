<?php
date_default_timezone_set('Europe/Tallinn');

require_once 'vendor/autoload.php';

define('DEBUG', true);

ORM::configure('mysql:host=localhost;dbname=mnrg');
ORM::configure('username', 'root');
ORM::configure('password', '');

session_start();

//ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));

$app = new \Slim\Slim(array(
    'debug' => DEBUG,
    'view' => new \Slim\Views\Twig(),
));

$view = $app->view();
$view->setTemplatesDirectory(dirname(__FILE__) . '/templates');

$view->parserOptions = array(
    'charset' => 'UTF-8',
    'debug' => DEBUG,
    'cache' => dirname(__FILE__) . '/cache'
);

$view->parserExtensions = array(
    new \Slim\Views\TwigExtension(),
);



$app->response->headers->set('Content-Type', 'text/html; charset=utf-8');
