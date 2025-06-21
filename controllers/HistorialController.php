<?php
namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;

class HistorialController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        isAuth();
        $router->render('historial/index', []);
    }

    public static function obtenerActividadesAPI()
    {
        getHeadersApi();
        isAuth();

        try {
            $consulta = "SELECT h.historial_id, h.historial_descripcion, 
                               h.historial_tabla_afectada, h.historial_fecha,
                               u.usuario_nombre, u.usuario_apellido, u.usuario_correo
                        FROM guzman_historial_actividades h
                        INNER JOIN guzman_usuarios u ON h.historial_usuario_id = u.usuario_id
                        WHERE h.historial_situacion = 1
                        ORDER BY h.historial_fecha DESC
                        LIMIT 500";
            
            $actividades = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Actividades obtenidas exitosamente',
                'data' => $actividades
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener actividades',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function registrarActividad($usuarioId, $tabla, $descripcion)
    {
        try {
            $query = "INSERT INTO guzman_historial_actividades 
                     (historial_usuario_id, historial_tabla_afectada, historial_descripcion, historial_fecha, historial_situacion) 
                     VALUES ($usuarioId, " . self::$db->quote($tabla) . ", " . self::$db->quote($descripcion) . ", NOW(), 1)";
            
            self::SQL($query);
            return true;
        } catch (Exception $e) {
            error_log("Error registrando actividad: " . $e->getMessage());
            return false;
        }
    }
}
?>