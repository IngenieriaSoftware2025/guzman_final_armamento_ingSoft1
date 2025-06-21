<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Armamento;
use Model\TipoArmamento;
use Model\Calibre;
use Model\Almacen;

class ArmamentoController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('ARMAMENTO', 'LECTURA');
        $router->render('armamento/index', []);
    }

    public static function buscarAPI()
    {
        getHeadersApi();
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('ARMAMENTO', 'LECTURA');

        try {
            $consulta = "SELECT a.arma_id, a.arma_numero_serie, a.arma_estado, 
                               a.arma_fecha_ingreso, a.arma_observaciones, a.arma_situacion,
                               t.tipo_nombre, c.calibre_nombre, al.almacen_nombre
                        FROM guzman_armamento a
                        LEFT JOIN guzman_tipos_armamento t ON a.arma_tipo = t.tipo_id
                        LEFT JOIN guzman_calibres c ON a.arma_calibre = c.calibre_id
                        LEFT JOIN guzman_almacenes al ON a.arma_almacen = al.almacen_id
                        WHERE a.arma_situacion = 1
                        ORDER BY a.arma_numero_serie";

            $armamento = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Armamento obtenido exitosamente',
                'data' => $armamento
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener armamento',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('ARMAMENTO', 'ESCRITURA');
        getHeadersApi();

        // Validar campos obligatorios
        if (empty($_POST['arma_numero_serie'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El número de serie es obligatorio']);
            return;
        }

        if (empty($_POST['arma_tipo'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El tipo de armamento es obligatorio']);
            return;
        }

        if (empty($_POST['arma_calibre'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El calibre es obligatorio']);
            return;
        }

        if (empty($_POST['arma_almacen'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El almacén es obligatorio']);
            return;
        }

        try {
            $numero_serie = trim(htmlspecialchars($_POST['arma_numero_serie']));
            $tipo = intval($_POST['arma_tipo']);
            $calibre = intval($_POST['arma_calibre']);
            $estado = $_POST['arma_estado'] ?? 'BUEN_ESTADO';
            $almacen = intval($_POST['arma_almacen']);
            $observaciones = trim(htmlspecialchars($_POST['arma_observaciones'] ?? ''));
            $situacion = intval($_POST['arma_situacion'] ?? 1);

            // Verificar que no exista el número de serie
            $consultaSerie = "SELECT COUNT(*) as total FROM guzman_armamento WHERE arma_numero_serie = '" . $numero_serie . "'";
            $resultadoSerie = self::fetchFirst($consultaSerie);

            if ($resultadoSerie && $resultadoSerie['total'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe un armamento con este número de serie']);
                return;
            }

            // Crear instancia usando constructor
            $_POST['arma_numero_serie'] = $numero_serie;
            $_POST['arma_tipo'] = $tipo;
            $_POST['arma_calibre'] = $calibre;
            $_POST['arma_estado'] = $estado;
            $_POST['arma_almacen'] = $almacen;
            $_POST['arma_observaciones'] = $observaciones;
            $_POST['arma_situacion'] = $situacion;

            $armamento = new Armamento($_POST);
            $resultado = $armamento->crear();

            if ($resultado['resultado']) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Armamento registrado exitosamente',
                    'id' => $resultado['id']
                ]);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al registrar el armamento']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el armamento',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('ARMAMENTO', 'ESCRITURA');
        getHeadersApi();

        $id = filter_var($_POST['arma_id'], FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de armamento inválido']);
            return;
        }

        // Validar campos obligatorios
        if (empty($_POST['arma_numero_serie'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El número de serie es obligatorio']);
            return;
        }

        try {
            // Verificar que existe el armamento
            $consultaArmamento = "SELECT * FROM guzman_armamento WHERE arma_id = $id";
            $datosArmamento = self::fetchFirst($consultaArmamento);

            if (!$datosArmamento) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Armamento no encontrado']);
                return;
            }

            $numero_serie = trim(htmlspecialchars($_POST['arma_numero_serie']));
            $tipo = intval($_POST['arma_tipo']);
            $calibre = intval($_POST['arma_calibre']);
            $estado = $_POST['arma_estado'];
            $almacen = intval($_POST['arma_almacen']);
            $observaciones = trim(htmlspecialchars($_POST['arma_observaciones'] ?? ''));
            $situacion = intval($_POST['arma_situacion'] ?? 1);

            // Verificar duplicado de número de serie excluyendo el registro actual
            $consultaSerie = "SELECT COUNT(*) as total FROM guzman_armamento 
                             WHERE arma_numero_serie = " . self::$db->quote($numero_serie) . " AND arma_id != $id";
            $resultadoSerie = self::fetchFirst($consultaSerie);

            if ($resultadoSerie['total'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otro armamento con este número de serie']);
                return;
            }

            $queryUpdate = "UPDATE guzman_armamento SET 
                           arma_numero_serie = " . self::$db->quote($numero_serie) . ",
                           arma_tipo = $tipo,
                           arma_calibre = $calibre,
                           arma_estado = " . self::$db->quote($estado) . ",
                           arma_almacen = $almacen,
                           arma_observaciones = " . self::$db->quote($observaciones) . ",
                           arma_situacion = $situacion
                           WHERE arma_id = $id";

            $resultado = self::SQL($queryUpdate);

            if ($resultado !== false) {
                http_response_code(200);
                echo json_encode(['codigo' => 1, 'mensaje' => 'Armamento actualizado exitosamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al actualizar el armamento']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el armamento',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('ARMAMENTO', 'TOTAL');
        getHeadersApi();

        try {
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);

            if (!$id) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'ID de armamento inválido']);
                return;
            }

            // Verificar si el armamento existe
            $consultaArmamento = "SELECT arma_id, arma_situacion FROM guzman_armamento WHERE arma_id = $id";
            $datosArmamento = self::fetchFirst($consultaArmamento);

            if (!$datosArmamento || $datosArmamento['arma_situacion'] == 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Armamento no encontrado o ya eliminado']);
                return;
            }

            // Actualizar situación del armamento
            $queryEliminar = "UPDATE guzman_armamento SET arma_situacion = 0 WHERE arma_id = $id";
            $resultado = self::SQL($queryEliminar);

            http_response_code(200);
            echo json_encode(['codigo' => 1, 'mensaje' => 'El armamento ha sido eliminado correctamente']);
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el armamento',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerTiposAPI()
    {
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('ARMAMENTO', 'LECTURA');
        getHeadersApi();

        try {
            $consulta = "SELECT tipo_id, tipo_nombre FROM guzman_tipos_armamento WHERE tipo_situacion = 1 ORDER BY tipo_nombre";
            $tipos = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Tipos obtenidos exitosamente',
                'data' => $tipos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener tipos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerCalibresAPI()
    {
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('ARMAMENTO', 'LECTURA');
        getHeadersApi();

        try {
            $consulta = "SELECT calibre_id, calibre_nombre FROM guzman_calibres WHERE calibre_situacion = 1 ORDER BY calibre_nombre";
            $calibres = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Calibres obtenidos exitosamente',
                'data' => $calibres
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener calibres',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerAlmacenesAPI()
    {
        require_once __DIR__ . '/../includes/middleware.php';
        verificarPermisoAplicacion('ARMAMENTO', 'LECTURA');
        getHeadersApi();

        try {
            $consulta = "SELECT almacen_id, almacen_nombre FROM guzman_almacenes WHERE almacen_situacion = 1 ORDER BY almacen_nombre";
            $almacenes = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Almacenes obtenidos exitosamente',
                'data' => $almacenes
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener almacenes',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
