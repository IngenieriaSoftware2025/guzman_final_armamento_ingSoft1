<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\AsignacionesArmamento;

class AsignacionController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('asignaciones/index', []);
    }

    public static function buscarAPI()
    {
        getHeadersApi();

        try {
            $consulta = "SELECT a.asignacion_id, a.asignacion_fecha_asignacion, 
                               a.asignacion_fecha_devolucion, a.asignacion_motivo, 
                               a.asignacion_estado, a.asignacion_situacion,
                               ar.arma_numero_serie, ta.tipo_nombre, c.calibre_nombre,
                               p.personal_nombres, p.personal_apellidos, p.personal_grado,
                               u.usuario_nombre, u.usuario_apellido
                        FROM guzman_asignaciones_armamento a
                        LEFT JOIN guzman_armamento ar ON a.asignacion_arma = ar.arma_id
                        LEFT JOIN guzman_tipos_armamento ta ON ar.arma_tipo = ta.tipo_id
                        LEFT JOIN guzman_calibres c ON ar.arma_calibre = c.calibre_id
                        LEFT JOIN guzman_personal p ON a.asignacion_personal = p.personal_id
                        LEFT JOIN guzman_usuarios u ON a.asignacion_usuario = u.usuario_id
                        WHERE a.asignacion_situacion = 1
                        ORDER BY a.asignacion_fecha_asignacion DESC";

            $asignaciones = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Asignaciones obtenidas exitosamente',
                'data' => $asignaciones
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener asignaciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        if (empty($_POST['asignacion_arma'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe seleccionar un armamento']);
            return;
        }

        if (empty($_POST['asignacion_personal'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Debe seleccionar el personal']);
            return;
        }

        if (empty($_POST['asignacion_motivo'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El motivo de asignación es obligatorio']);
            return;
        }

        if (empty($_POST['asignacion_usuario'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Usuario no válido']);
            return;
        }

        try {
            $arma = intval($_POST['asignacion_arma']);
            $personal = intval($_POST['asignacion_personal']);
            $motivo = trim(htmlspecialchars($_POST['asignacion_motivo']));
            $usuario = intval($_POST['asignacion_usuario']);
            $fecha_asignacion = $_POST['asignacion_fecha_asignacion'] ?? date('m/d/Y');
            $situacion = intval($_POST['asignacion_situacion'] ?? 1);

            $consultaArmamento = "SELECT COUNT(*) as total FROM guzman_asignaciones_armamento 
                                 WHERE asignacion_arma = $arma AND asignacion_estado = 'ASIGNADO' 
                                 AND asignacion_situacion = 1";
            $resultadoArmamento = self::fetchFirst($consultaArmamento);

            if ($resultadoArmamento['total'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Este armamento ya está asignado a otro personal']);
                return;
            }

            $consultaDisponible = "SELECT COUNT(*) as total FROM guzman_armamento 
                                  WHERE arma_id = $arma AND arma_situacion = 1";
            $resultadoDisponible = self::fetchFirst($consultaDisponible);

            if ($resultadoDisponible['total'] == 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'El armamento seleccionado no está disponible']);
                return;
            }

            $consultaPersonal = "SELECT COUNT(*) as total FROM guzman_personal 
                               WHERE personal_id = $personal AND personal_situacion = 1";
            $resultadoPersonal = self::fetchFirst($consultaPersonal);

            if ($resultadoPersonal['total'] == 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'El personal seleccionado no está disponible']);
                return;
            }

            $asignacion = new AsignacionesArmamento([
                'asignacion_arma' => $arma,
                'asignacion_personal' => $personal,
                'asignacion_fecha_asignacion' => $fecha_asignacion,
                'asignacion_motivo' => $motivo,
                'asignacion_estado' => 'ASIGNADO',
                'asignacion_usuario' => $usuario,
                'asignacion_situacion' => $situacion
            ]);

            $resultado = $asignacion->crear();

            if ($resultado['resultado']) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Asignación registrada exitosamente',
                    'id' => $resultado['id']
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al registrar la asignación']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar la asignación',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function devolverAPI()
    {
        getHeadersApi();

        $id = filter_var($_POST['asignacion_id'], FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de asignación inválido']);
            return;
        }

        try {
            $consultaAsignacion = "SELECT * FROM guzman_asignaciones_armamento WHERE asignacion_id = $id";
            $datosAsignacion = self::fetchFirst($consultaAsignacion);

            if (!$datosAsignacion) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Asignación no encontrada']);
                return;
            }

            if ($datosAsignacion['asignacion_estado'] !== 'ASIGNADO') {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Esta asignación ya fue devuelta']);
                return;
            }

            $fecha_devolucion = date('Y-m-d');
            $queryUpdate = "UPDATE guzman_asignaciones_armamento SET 
               asignacion_estado = 'DEVUELTO',
               asignacion_fecha_devolucion = TODAY
               WHERE asignacion_id = $id";

            $resultado = self::SQL($queryUpdate);

            if ($resultado !== false) {
                http_response_code(200);
                echo json_encode(['codigo' => 1, 'mensaje' => 'Armamento devuelto exitosamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al procesar la devolución']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al devolver el armamento',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        getHeadersApi();

        try {
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

            if (!$id) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'ID de asignación inválido']);
                return;
            }

            $consultaAsignacion = "SELECT asignacion_id, asignacion_situacion FROM guzman_asignaciones_armamento WHERE asignacion_id = $id";
            $datosAsignacion = self::fetchFirst($consultaAsignacion);

            if (!$datosAsignacion || $datosAsignacion['asignacion_situacion'] == 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Asignación no encontrada o ya eliminada']);
                return;
            }

            $queryEliminar = "UPDATE guzman_asignaciones_armamento SET asignacion_situacion = 0 WHERE asignacion_id = $id";
            $resultado = self::SQL($queryEliminar);

            http_response_code(200);
            echo json_encode(['codigo' => 1, 'mensaje' => 'La asignación ha sido eliminada correctamente']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar la asignación',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerArmamentoDisponibleAPI()
    {
        getHeadersApi();

        try {
            $consulta = "SELECT a.arma_id, a.arma_numero_serie, ta.tipo_nombre, c.calibre_nombre
                        FROM guzman_armamento a
                        LEFT JOIN guzman_tipos_armamento ta ON a.arma_tipo = ta.tipo_id
                        LEFT JOIN guzman_calibres c ON a.arma_calibre = c.calibre_id
                        WHERE a.arma_situacion = 1 
                        AND a.arma_id NOT IN (
                            SELECT asignacion_arma FROM guzman_asignaciones_armamento 
                            WHERE asignacion_estado = 'ASIGNADO' AND asignacion_situacion = 1
                        )
                        ORDER BY a.arma_numero_serie";

            $armamento = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Armamento disponible obtenido exitosamente',
                'data' => $armamento
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener armamento disponible',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerPersonalDisponibleAPI()
    {
        getHeadersApi();

        try {
            $consulta = "SELECT personal_id, personal_nombres, personal_apellidos, 
                               personal_grado, personal_unidad
                        FROM guzman_personal 
                        WHERE personal_situacion = 1 
                        ORDER BY personal_nombres";

            $personal = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Personal disponible obtenido exitosamente',
                'data' => $personal
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener personal disponible',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerHistorialPersonalAPI()
    {
        getHeadersApi();

        $personal_id = filter_var($_GET['personal_id'], FILTER_VALIDATE_INT);
        if (!$personal_id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de personal inválido']);
            return;
        }

        try {
            $consulta = "SELECT a.asignacion_id, a.asignacion_fecha_asignacion, 
                               a.asignacion_fecha_devolucion, a.asignacion_motivo, 
                               a.asignacion_estado,
                               ar.arma_numero_serie, ta.tipo_nombre, c.calibre_nombre
                        FROM guzman_asignaciones_armamento a
                        LEFT JOIN guzman_armamento ar ON a.asignacion_arma = ar.arma_id
                        LEFT JOIN guzman_tipos_armamento ta ON ar.arma_tipo = ta.tipo_id
                        LEFT JOIN guzman_calibres c ON ar.arma_calibre = c.calibre_id
                        WHERE a.asignacion_personal = $personal_id AND a.asignacion_situacion = 1
                        ORDER BY a.asignacion_fecha_asignacion DESC";

            $historial = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Historial obtenido exitosamente',
                'data' => $historial
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener historial',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
