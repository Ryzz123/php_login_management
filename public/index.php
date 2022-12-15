<?php

require_once __DIR__ . '/../vendor/autoload.php';

use FebriAnandaLubis\Belajar\PHP\MVC\App\Router;
use FebriAnandaLubis\Belajar\PHP\MVC\Controller\HomeController;
use FebriAnandaLubis\Belajar\PHP\MVC\Controller\UserController;
use FebriAnandaLubis\Belajar\PHP\MVC\Config\Database;

// berfungsi untuk merubah database dari test menjadi production
Database::getConnection('prod');

// Home controller
Router::add('GET', '/', HomeController::class, 'index', []);

// User Controller register
Router::add('GET', '/users/register', UserController::class, 'register', []);
Router::add('POST', '/users/register', UserController::class, 'postRegister', []);

// User Controller login
Router::add('GET', '/users/login', UserController::class, 'login', []);
Router::add('POST', '/users/login', UserController::class, 'postLogin', []);

Router::run();