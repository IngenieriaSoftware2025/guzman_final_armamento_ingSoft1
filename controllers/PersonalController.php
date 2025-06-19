<?php
namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Personal;

class PersonalController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('personal/index', []);
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        
        try {
            $consulta = "SELECT personal_id, personal_nombres, personal_apellidos, 
                               personal_grado, personal_unidad, personal_dpi, personal_situacion
                        FROM guzman_personal 
                        WHERE personal_situacion = 1 
                        ORDER BY personal_nombres";
            
            $personal = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Personal obtenido exitosamente',
                'data' => $personal
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener personal',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        getHeadersApi();

        if (empty($_POST['personal_nombres'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los nombres son obligatorios']);
            return;
        }

        if (empty($_POST['personal_apellidos'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los apellidos son obligatorios']);
            return;
        }

        if (empty($_POST['personal_grado'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El grado es obligatorio']);
            return;
        }

        if (empty($_POST['personal_unidad'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'La unidad es obligatoria']);
            return;
        }

        if (empty($_POST['personal_dpi'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El DPI es obligatorio']);
            return;
        }

        try {
            $nombres = trim(htmlspecialchars($_POST['personal_nombres']));
            $apellidos = trim(htmlspecialchars($_POST['personal_apellidos']));
            $grado = trim(htmlspecialchars($_POST['personal_grado']));
            $unidad = trim(htmlspecialchars($_POST['personal_unidad']));
            $dpi = trim(htmlspecialchars($_POST['personal_dpi']));
            $situacion = intval($_POST['personal_situacion'] ?? 1);

            if (!preg_match('/^\d{13}$/', $dpi)) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'El DPI debe tener exactamente 13 dígitos']);
                return;
            }

            $consultaDpi = "SELECT COUNT(*) as total FROM guzman_personal WHERE personal_dpi = " . self::$db->quote($dpi);
            $resultadoDpi = self::fetchFirst($consultaDpi);

            if ($resultadoDpi['total'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe personal con este DPI']);
                return;
            }

            $personal = new Personal([
                'personal_nombres' => $nombres,
                'personal_apellidos' => $apellidos,
                'personal_grado' => $grado,
                'personal_unidad' => $unidad,
                'personal_dpi' => $dpi,
                'personal_situacion' => $situacion
            ]);

            $resultado = $personal->crear();

            if ($resultado['resultado']) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Personal registrado exitosamente',
                    'id' => $resultado['id']
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al registrar el personal']);
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el personal',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        $id = filter_var($_POST['personal_id'], FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de personal inválido']);
            return;
        }

        if (empty($_POST['personal_nombres'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los nombres son obligatorios']);
            return;
        }

        if (empty($_POST['personal_apellidos'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Los apellidos son obligatorios']);
            return;
        }

        try {
            $consultaPersonal = "SELECT * FROM guzman_personal WHERE personal_id = $id";
            $datosPersonal = self::fetchFirst($consultaPersonal);
            
            if (!$datosPersonal) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Personal no encontrado']);
                return;
            }

            $nombres = trim(htmlspecialchars($_POST['personal_nombres']));
            $apellidos = trim(htmlspecialchars($_POST['personal_apellidos']));
            $grado = trim(htmlspecialchars($_POST['personal_grado']));
            $unidad = trim(htmlspecialchars($_POST['personal_unidad']));
            $dpi = trim(htmlspecialchars($_POST['personal_dpi']));
            $situacion = intval($_POST['personal_situacion'] ?? 1);

            if (!preg_match('/^\d{13}$/', $dpi)) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'El DPI debe tener exactamente 13 dígitos']);
                return;
            }

            $consultaDpi = "SELECT COUNT(*) as total FROM guzman_personal 
                           WHERE personal_dpi = " . self::$db->quote($dpi) . " AND personal_id != $id";
            $resultadoDpi = self::fetchFirst($consultaDpi);

            if ($resultadoDpi['total'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otro personal con este DPI']);
                return;
            }

            $queryUpdate = "UPDATE guzman_personal SET 
                           personal_nombres = " . self::$db->quote($nombres) . ",
                           personal_apellidos = " . self::$db->quote($apellidos) . ",
                           personal_grado = " . self::$db->quote($grado) . ",
                           personal_unidad = " . self::$db->quote($unidad) . ",
                           personal_dpi = " . self::$db->quote($dpi) . ",
                           personal_situacion = $situacion
                           WHERE personal_id = $id";

            $resultado = self::SQL($queryUpdate);

            if ($resultado !== false) {
                http_response_code(200);
                echo json_encode(['codigo' => 1, 'mensaje' => 'Personal actualizado exitosamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al actualizar el personal']);
            }

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el personal',
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
                echo json_encode(['codigo' => 0, 'mensaje' => 'ID de personal inválido']);
                return;
            }

            $consultaPersonal = "SELECT personal_id, personal_situacion FROM guzman_personal WHERE personal_id = $id";
            $datosPersonal = self::fetchFirst($consultaPersonal);
            
            if (!$datosPersonal || $datosPersonal['personal_situacion'] == 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Personal no encontrado o ya eliminado']);
                return;
            }

            $queryEliminar = "UPDATE guzman_personal SET personal_situacion = 0 WHERE personal_id = $id";
            $resultado = self::SQL($queryEliminar);

            http_response_code(200);
            echo json_encode(['codigo' => 1, 'mensaje' => 'El personal ha sido eliminado correctamente']);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el personal',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerPersonalAPI()
    {
        getHeadersApi();

        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de personal inválido']);
            return;
        }

        try {
            $consulta = "SELECT personal_id, personal_nombres, personal_apellidos, 
                               personal_grado, personal_unidad, personal_dpi, personal_situacion
                        FROM guzman_personal 
                        WHERE personal_id = $id AND personal_situacion = 1";
            
            $personal = self::fetchFirst($consulta);

            if ($personal) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Personal obtenido exitosamente',
                    'data' => $personal
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Personal no encontrado']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el personal',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}