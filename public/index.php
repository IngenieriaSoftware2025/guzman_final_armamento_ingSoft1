<?php
require_once __DIR__ . '/../includes/app.php';


use MVC\Router;
use Controllers\AppController;
use Controllers\UsuariosController;
use Controllers\ArmamentoController;
use Controllers\PersonalController;
use Controllers\AsignacionController;
use Controllers\MapasController;
use Controllers\EstadisticasController;
use Controllers\PermisosController;
use Controllers\HistorialController;

$router = new Router();
$router->setBaseURL('/' . $_ENV['APP_NAME']);

$router->get('/', [AppController::class, 'index']);

// Rutas principales
$router->get('/', [AppController::class, 'index']);
$router->get('/login', [AppController::class, 'renderLogin']);
$router->post('/login', [AppController::class, 'login']);
$router->get('/dashboard', [AppController::class, 'dashboard']);
$router->get('/logout', [AppController::class, 'logout']);

// Rutas para Usuarios
$router->get('/usuarios', [UsuariosController::class, 'renderizarPagina']);
$router->post('/usuarios/guardarAPI', [UsuariosController::class, 'guardarAPI']);
$router->get('/usuarios/buscarAPI', [UsuariosController::class, 'buscarAPI']);
$router->post('/usuarios/modificarAPI', [UsuariosController::class, 'modificarAPI']);
$router->get('/usuarios/eliminarAPI', [UsuariosController::class, 'eliminarAPI']);

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
$router->get('/estadisticas/distribucionUsuariosRolAPI', [EstadisticasController::class, 'distribucionUsuariosRolAPI']);

// Rutas para Estadísticas
$router->get('/estadisticas', [EstadisticasController::class, 'renderizarPagina']);
$router->get('/estadisticas/armamentoPorTipoAPI', [EstadisticasController::class, 'armamentoPorTipoAPI']);
$router->get('/estadisticas/armamentoPorEstadoAPI', [EstadisticasController::class, 'armamentoPorEstadoAPI']);
$router->get('/estadisticas/asignacionesPorMesAPI', [EstadisticasController::class, 'asignacionesPorMesAPI']);
$router->get('/estadisticas/personalPorUnidadAPI', [EstadisticasController::class, 'personalPorUnidadAPI']);

// Rutas para Permisos
$router->get('/permisos', [PermisosController::class, 'renderizarPagina']);
$router->get('/permisos/obtenerUsuariosAPI', [PermisosController::class, 'obtenerUsuariosAPI']);
$router->get('/permisos/obtenerAplicacionesAPI', [PermisosController::class, 'obtenerAplicacionesAPI']);
$router->get('/permisos/obtenerPermisosUsuarioAPI', [PermisosController::class, 'obtenerPermisosUsuarioAPI']);
$router->post('/permisos/asignarPermisoAPI', [PermisosController::class, 'asignarPermisoAPI']);
$router->get('/permisos/revocarPermisoAPI', [PermisosController::class, 'revocarPermisoAPI']);

// Rutas para Historial
$router->get('/historial', [HistorialController::class, 'renderizarPagina']);
$router->get('/historial/obtenerActividadesAPI', [HistorialController::class, 'obtenerActividadesAPI']);
//url's de mapas
$router->get('/mapas', [MapasController::class, 'renderizarPagina']);


$router->comprobarRutas();
