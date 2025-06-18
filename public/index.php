<?php 
require_once __DIR__ . '/../includes/app.php';

use MVC\Router;
use Controllers\AppController;
use Controllers\UsuariosController;

$router = new Router();
$router->setBaseURL('/guzman_final_armamento_ingSoft1');

// RUTAS DE AUTENTICACIÓN
$router->get('/', [AppController::class, 'index']);
$router->post('/API/login', [AppController::class, 'login']);
$router->get('/inicio', [AppController::class, 'renderInicio']);
$router->post('/logout', [AppController::class, 'logout']);

// Rutas para Usuarios
$router->get('/usuarios', [UsuariosController::class, 'renderizarPagina']);
$router->post('/usuarios/guardarAPI', [UsuariosController::class, 'guardarAPI']);
$router->get('/usuarios/buscarAPI', [UsuariosController::class, 'buscarAPI']);
$router->post('/usuarios/modificarAPI', [UsuariosController::class, 'modificarAPI']);
$router->get('/usuarios/eliminarAPI', [UsuariosController::class, 'eliminarAPI']);

// Comprueba y valida las rutas
$router->comprobarRutas();