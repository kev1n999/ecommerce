<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');
$routes->post('/auth/signup', 'Authentication::signup');
$routes->post('/auth/signin', 'Authentication::signin');