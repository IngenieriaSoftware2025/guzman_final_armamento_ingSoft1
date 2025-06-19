<?php 
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\UsuariosController;
use Controllers\ArmamentoController;
use Controllers\PersonalController;
use Controllers\AsignacionController;

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

// Rutas para Armamento
$router->get('/armamento', [ArmamentoController::class, 'renderizarPagina']);
$router->post('/armamento/guardarAPI', [ArmamentoController::class, 'guardarAPI']);
$router->get('/armamento/buscarAPI', [ArmamentoController::class, 'buscarAPI']);
$router->post('/armamento/modificarAPI', [ArmamentoController::class, 'modificarAPI']);
$router->get('/armamento/eliminarAPI', [ArmamentoController::class, 'eliminarAPI']);
$router->get('/armamento/obtenerTiposAPI', [ArmamentoController::class, 'obtenerTiposAPI']);
$router->get('/armamento/obtenerCalibresAPI', [ArmamentoController::class, 'obtenerCalibresAPI']);
$router->get('/armamento/obtenerAlmacenesAPI', [ArmamentoController::class, 'obtenerAlmacenesAPI']);

// Rutas para Personal
$router->get('/personal', [PersonalController::class, 'renderizarPagina']);
$router->post('/personal/guardarAPI', [PersonalController::class, 'guardarAPI']);
$router->get('/personal/buscarAPI', [PersonalController::class, 'buscarAPI']);
$router->post('/personal/modificarAPI', [PersonalController::class, 'modificarAPI']);
$router->get('/personal/eliminarAPI', [PersonalController::class, 'eliminarAPI']);
$router->get('/personal/obtenerPersonalAPI', [PersonalController::class, 'obtenerPersonalAPI']);

// Rutas para Asignaciones
$router->get('/asignaciones', [AsignacionController::class, 'renderizarPagina']);
$router->post('/asignaciones/guardarAPI', [AsignacionController::class, 'guardarAPI']);
$router->get('/asignaciones/buscarAPI', [AsignacionController::class, 'buscarAPI']);
$router->post('/asignaciones/devolverAPI', [AsignacionController::class, 'devolverAPI']);
$router->get('/asignaciones/eliminarAPI', [AsignacionController::class, 'eliminarAPI']);
$router->get('/asignaciones/obtenerArmamentoDisponibleAPI', [AsignacionController::class, 'obtenerArmamentoDisponibleAPI']);
$router->get('/asignaciones/obtenerPersonalDisponibleAPI', [AsignacionController::class, 'obtenerPersonalDisponibleAPI']);
$router->get('/asignaciones/obtenerHistorialPersonalAPI', [AsignacionController::class, 'obtenerHistorialPersonalAPI']);

$router->comprobarRutas();
