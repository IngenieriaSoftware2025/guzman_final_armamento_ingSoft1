<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\UsuariosController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

// Rutas para Usuarios
$router->get('/usuarios', [UsuariosController::class, 'renderizarPagina']);
$router->post('/usuarios/guardarAPI', [UsuariosController::class, 'guardarAPI']);
$router->get('/usuarios/buscarAPI', [UsuariosController::class, 'buscarAPI']);
$router->post('/usuarios/modificarAPI', [UsuariosController::class, 'modificarAPI']);
$router->get('/usuarios/eliminarAPI', [UsuariosController::class, 'eliminarAPI']);
$router->get('/usuarios/obtenerUsuarioAPI', [UsuariosController::class, 'obtenerUsuarioAPI']);

$router->comprobarRutas();
