<?php 
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AppController;
use Controllers\UsuarioController;

$router = new Router();
$router->setBaseURL('/guzman_final_armamento_ingSoft1');

// RUTAS DE AUTENTICACIÓN
$router->get('/', [AppController::class, 'index']);
$router->post('/API/login', [AppController::class, 'login']);
$router->get('/inicio', [AppController::class, 'renderInicio']);
$router->post('/logout', [AppController::class, 'logout']);

// RUTAS DE USUARIOS
$router->get('/usuarios', [UsuarioController::class, 'index']);
$router->get('/usuarios/crear', [UsuarioController::class, 'crear']);
$router->post('/usuarios/crear', [UsuarioController::class, 'crear']);
$router->get('/usuarios/actualizar', [UsuarioController::class, 'actualizar']);
$router->post('/usuarios/actualizar', [UsuarioController::class, 'actualizar']);
$router->post('/usuarios/eliminar', [UsuarioController::class, 'eliminar']);

// Comprueba y valida las rutas
$router->comprobarRutas();