<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\UsuariosController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class,'index']);

// GET para mostrar la página
$router->get('/usuarios', [UsuariosController::class, 'renderizarPagina']);
$router->get('/usuarios/buscarAPI', [UsuariosController::class, 'buscarAPI']);
$router->post('/usuarios/guardarAPI', [UsuariosController::class, 'guardarAPI']);
$router->post('/usuarios/modificarAPI', [UsuariosController::class, 'modificarAPI']);
$router->get('/usuarios/eliminarAPI', [UsuariosController::class, 'eliminarAPI']);
$router->get('/usuarios/obtenerRolesAPI', [UsuariosController::class, 'obtenerRolesAPI']);
$router->get('/usuarios/obtenerUsuarioAPI', [UsuariosController::class, 'obtenerUsuarioAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
