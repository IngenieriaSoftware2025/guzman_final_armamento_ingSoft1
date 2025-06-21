<?php
namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;

class PermisosController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('CONFIGURACION', 'TOTAL');
        
        $router->render('permisos/index', []);
    }

    public static function obtenerUsuariosAPI()
    {
        getHeadersApi();
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('CONFIGURACION', 'LECTURA');

        try {
            $consulta = "SELECT u.usuario_id, u.usuario_nombre, u.usuario_apellido, u.usuario_correo
                        FROM guzman_usuarios u 
                        WHERE u.usuario_situacion = 1 
                        ORDER BY u.usuario_nombre";
            
            $usuarios = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuarios obtenidos exitosamente',
                'data' => $usuarios
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener usuarios',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerAplicacionesAPI()
    {
        getHeadersApi();
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('CONFIGURACION', 'LECTURA');

        try {
            $consulta = "SELECT app_id, app_nombre, app_descripcion 
                        FROM guzman_aplicaciones 
                        WHERE app_situacion = 1 
                        ORDER BY app_nombre";
            
            $aplicaciones = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Aplicaciones obtenidas exitosamente',
                'data' => $aplicaciones
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener aplicaciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerPermisosUsuarioAPI()
    {
        getHeadersApi();
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('CONFIGURACION', 'LECTURA');

        $usuarioId = filter_var($_GET['usuario_id'], FILTER_VALIDATE_INT);
        if (!$usuarioId) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de usuario inválido']);
            return;
        }

        try {
            $consulta = "SELECT pa.permiso_app_id, pa.permiso_usuario, pa.permiso_aplicacion, 
                               pa.permiso_nivel, pa.permiso_fecha_asignacion,
                               a.app_nombre, a.app_descripcion,
                               u.usuario_nombre, u.usuario_apellido
                        FROM guzman_permisos_aplicaciones pa
                        INNER JOIN guzman_aplicaciones a ON pa.permiso_aplicacion = a.app_id
                        INNER JOIN guzman_usuarios u ON pa.permiso_usuario = u.usuario_id
                        WHERE pa.permiso_usuario = $usuarioId 
                        AND pa.permiso_situacion = 1
                        ORDER BY a.app_nombre";
            
            $permisos = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Permisos obtenidos exitosamente',
                'data' => $permisos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener permisos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function asignarPermisoAPI()
    {
        getHeadersApi();
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('CONFIGURACION', 'ESCRITURA');

        // Validaciones
        if (empty($_POST['permiso_usuario'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe seleccionar un usuario']);
            return;
        }

        if (empty($_POST['permiso_aplicacion'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe seleccionar una aplicación']);
            return;
        }

        if (empty($_POST['permiso_nivel'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe seleccionar un nivel de permiso']);
            return;
        }

        try {
            $usuario = intval($_POST['permiso_usuario']);
            $aplicacion = intval($_POST['permiso_aplicacion']);
            $nivel = $_POST['permiso_nivel'];

            // Verificar si ya existe el permiso
            $consultaExiste = "SELECT COUNT(*) as total 
                              FROM guzman_permisos_aplicaciones 
                              WHERE permiso_usuario = $usuario 
                              AND permiso_aplicacion = $aplicacion 
                              AND permiso_situacion = 1";
            
            $resultadoExiste = self::fetchFirst($consultaExiste);

            if ($resultadoExiste['total'] > 0) {
                // Actualizar permiso existente
                $queryUpdate = "UPDATE guzman_permisos_aplicaciones 
                               SET permiso_nivel = " . self::$db->quote($nivel) . "
                               WHERE permiso_usuario = $usuario 
                               AND permiso_aplicacion = $aplicacion 
                               AND permiso_situacion = 1";
                
                $resultado = self::SQL($queryUpdate);
                $mensaje = 'Permiso actualizado exitosamente';
            } else {
                // Crear nuevo permiso
                $queryInsert = "INSERT INTO guzman_permisos_aplicaciones 
                               (permiso_usuario, permiso_aplicacion, permiso_nivel, permiso_fecha_asignacion, permiso_situacion) 
                               VALUES ($usuario, $aplicacion, " . self::$db->quote($nivel) . ", TODAY, 1)";
                
                $resultado = self::SQL($queryInsert);
                $mensaje = 'Permiso asignado exitosamente';
            }

            if ($resultado !== false) {
                http_response_code(200);
                echo json_encode(['codigo' => 1, 'mensaje' => $mensaje]);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al procesar el permiso']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al asignar permiso',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function revocarPermisoAPI()
    {
        getHeadersApi();
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('CONFIGURACION', 'TOTAL');

        $permisoId = filter_var($_GET['permiso_id'], FILTER_VALIDATE_INT);
        if (!$permisoId) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de permiso inválido']);
            return;
        }

        try {
            $queryUpdate = "UPDATE guzman_permisos_aplicaciones 
                           SET permiso_situacion = 0 
                           WHERE permiso_app_id = $permisoId";
            
            $resultado = self::SQL($queryUpdate);

            if ($resultado !== false) {
                http_response_code(200);
                echo json_encode(['codigo' => 1, 'mensaje' => 'Permiso revocado exitosamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al revocar permiso']);
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al revocar permiso',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}