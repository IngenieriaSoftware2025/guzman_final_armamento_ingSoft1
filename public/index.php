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

// APIs para CRUD
$router->post('/api/usuarios/buscar', [UsuariosController::class, 'buscarAPI']);
$router->post('/api/usuarios/guardar', [UsuariosController::class, 'guardarAPI']);
$router->post('/api/usuarios/modificar', [UsuariosController::class, 'modificarAPI']);
$router->get('/api/usuarios/eliminar', [UsuariosController::class, 'eliminarAPI']);
$router->get('/api/usuarios/roles', [UsuariosController::class, 'obtenerRolesAPI']);
$router->get('/api/usuarios/obtener', [UsuariosController::class, 'obtenerUsuarioAPI']);

// Comprueba y valida las rutas, que existan y les asigna las funciones del Controlador
$router->comprobarRutas();
