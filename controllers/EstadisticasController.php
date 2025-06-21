<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;

class EstadisticasController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('estadisticas/index', []);
    }

    public static function armamentoPorTipoAPI()
    {
        getHeadersApi();

        try {
            $consulta = "SELECT ta.tipo_nombre, COUNT(a.arma_id) as cantidad
                        FROM guzman_tipos_armamento ta
                        LEFT JOIN guzman_armamento a ON ta.tipo_id = a.arma_tipo AND a.arma_situacion = 1
                        WHERE ta.tipo_situacion = 1
                        GROUP BY ta.tipo_id, ta.tipo_nombre
                        ORDER BY cantidad DESC";

            $datos = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Estadísticas de armamento por tipo obtenidas',
                'data' => $datos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener estadísticas de armamento por tipo',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function armamentoPorEstadoAPI()
    {
        getHeadersApi();

        try {
            $consulta = "SELECT 
                            CASE 
                                WHEN a.arma_estado = 'BUEN_ESTADO' THEN 'Buen Estado'
                                WHEN a.arma_estado = 'MAL_ESTADO_REPARABLE' THEN 'Mal Estado Reparable'
                                WHEN a.arma_estado = 'MAL_ESTADO_IRREPARABLE' THEN 'Mal Estado Irreparable'
                                ELSE a.arma_estado
                            END as estado_nombre,
                            COUNT(a.arma_id) as cantidad
                        FROM guzman_armamento a
                        WHERE a.arma_situacion = 1
                        GROUP BY a.arma_estado
                        ORDER BY cantidad DESC";

            $datos = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Estadísticas de armamento por estado obtenidas',
                'data' => $datos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener estadísticas de armamento por estado',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function distribucionUsuariosRolAPI()
    {
        getHeadersApi();

        try {
            $consulta = "SELECT 
            r.rol_nombre as categoria,
            COUNT(pr.permiso_usuario) as cantidad
        FROM guzman_roles r
        LEFT JOIN guzman_permisos_roles pr ON r.rol_id = pr.permiso_rol 
            AND pr.permiso_situacion = 1
        WHERE r.rol_situacion = 1
        GROUP BY r.rol_id, r.rol_nombre
        ORDER BY cantidad DESC";

            $datos = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Estadísticas de usuarios por rol obtenidas',
                'data' => $datos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener estadísticas de usuarios por rol',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function personalPorUnidadAPI()
    {
        getHeadersApi();

        try {
            $consulta = "SELECT 
            CASE 
            WHEN LENGTH(p.personal_unidad) > 25 THEN p.personal_unidad[1,25] || '...'
            ELSE p.personal_unidad
            END as unidad_nombre,
            COUNT(p.personal_id) as cantidad
            FROM guzman_personal p
            WHERE p.personal_situacion = 1
            GROUP BY p.personal_unidad
            ORDER BY cantidad DESC";

            $datos = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Estadísticas de personal por unidad obtenidas',
                'data' => $datos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener estadísticas de personal por unidad',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
