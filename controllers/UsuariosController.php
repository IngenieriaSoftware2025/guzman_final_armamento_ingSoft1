<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Usuario;

class UsuariosController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        // isAuth();
        // hasPermission(['ADMINISTRADOR']);
        $router->render('usuarios/index', []);
    }

    public static function buscarAPI()
    {
        // hasPermissionApi(['ADMINISTRADOR']);
        getHeadersApi();

        try {
            $consulta = "SELECT usuario_id, usuario_nombre, usuario_apellido, usuario_dpi, usuario_correo, usuario_fecha_creacion, usuario_situacion 
                        FROM guzman_usuarios 
                        WHERE usuario_situacion IN (1,2,3) 
                        ORDER BY usuario_nombre";
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

    public static function guardarAPI()
    {
        // hasPermissionApi(['ADMINISTRADOR']);
        getHeadersApi();

        if (empty($_POST['usuario_correo'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo del usuario es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['usuario_nombre'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre del usuario es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['usuario_apellido'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El apellido del usuario es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['usuario_dpi'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI del usuario es obligatorio'
            ]);
            return;
        }

        if (empty($_POST['usuario_contra'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña es obligatoria'
            ]);
            return;
        }

        try {
            $nombre = trim(htmlspecialchars($_POST['usuario_nombre']));
            $apellido = trim(htmlspecialchars($_POST['usuario_apellido']));
            $dpi = trim(htmlspecialchars($_POST['usuario_dpi']));
            $correo = trim(htmlspecialchars($_POST['usuario_correo']));
            $password = password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT);
            $rolId = intval($_POST['usuario_rol']);
            $situacion = intval($_POST['usuario_situacion'] ?? 1);

            if (!preg_match('/^\d{13}$/', $dpi)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El DPI debe tener exactamente 13 dígitos'
                ]);
                return;
            }

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El formato del correo electrónico no es válido'
                ]);
                return;
            }

            $consultaDpi = "SELECT COUNT(*) as total FROM guzman_usuarios WHERE usuario_dpi = " . self::$db->quote($dpi);
            $resultadoDpi = self::fetchFirst($consultaDpi);

            if ($resultadoDpi['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un usuario con este DPI'
                ]);
                return;
            }

            $consultaCorreo = "SELECT COUNT(*) as total FROM guzman_usuarios WHERE usuario_correo = " . self::$db->quote($correo);
            $resultadoCorreo = self::fetchFirst($consultaCorreo);

            if ($resultadoCorreo['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Ya existe un usuario con este correo electrónico'
                ]);
                return;
            }

            $usuario = new Usuario([
                'usuario_nombre' => $nombre,
                'usuario_apellido' => $apellido,
                'usuario_dpi' => $dpi,
                'usuario_correo' => $correo,
                'usuario_contra' => $password,
                'usuario_situacion' => $situacion
            ]);

            $resultado = $usuario->crear();

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al guardar el usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        // hasPermissionApi(['ADMINISTRADOR']);
        getHeadersApi();

        $id = filter_var($_POST['usuario_id'], FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de usuario inválido']);
            return;
        }

        // Validar campos obligatorios
        if (empty($_POST['usuario_nombre'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El nombre de usuario es obligatorio']);
            return;
        }

        if (empty($_POST['usuario_apellido'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El apellido de usuario es obligatorio']);
            return;
        }

        if (empty($_POST['usuario_dpi'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El DPI de usuario es obligatorio']);
            return;
        }

        if (empty($_POST['usuario_correo'])) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'El correo de usuario es obligatorio']);
            return;
        }

        try {
            // Buscar usuario existente
            $usuario = Usuario::find($id);
            if (!$usuario) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Usuario no encontrado']);
                return;
            }

            // Sanitizar datos
            $nombre = trim(htmlspecialchars($_POST['usuario_nombre']));
            $apellido = trim(htmlspecialchars($_POST['usuario_apellido']));
            $dpi = trim(htmlspecialchars($_POST['usuario_dpi']));
            $correo = trim(htmlspecialchars($_POST['usuario_correo']));
            $situacion = intval($_POST['usuario_situacion'] ?? 1);

            // Validar formato de DPI
            if (!preg_match('/^\d{13}$/', $dpi)) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'El DPI debe tener exactamente 13 dígitos']);
                return;
            }

            // Validar formato de correo
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'El formato del correo electrónico no es válido']);
                return;
            }

            // Verificar duplicado de DPI excluyendo el registro actual
            $consultaDpi = "SELECT COUNT(*) as total FROM guzman_usuarios 
                          WHERE usuario_dpi = " . self::$db->quote($dpi) . " AND usuario_id != $id";
            $resultadoDpi = self::fetchFirst($consultaDpi);

            if ($resultadoDpi['total'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otro usuario con este DPI']);
                return;
            }

            // Verificar duplicado de correo excluyendo el registro actual
            $consultaCorreo = "SELECT COUNT(*) as total FROM guzman_usuarios 
                             WHERE usuario_correo = " . self::$db->quote($correo) . " AND usuario_id != $id";
            $resultadoCorreo = self::fetchFirst($consultaCorreo);

            if ($resultadoCorreo['total'] > 0) {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Ya existe otro usuario con este correo']);
                return;
            }

            // Sincronizar datos del usuario usando el método del ActiveRecord
            $usuario->sincronizar([
                'usuario_nombre' => $nombre,
                'usuario_apellido' => $apellido,
                'usuario_dpi' => $dpi,
                'usuario_correo' => $correo,
                'usuario_situacion' => $situacion
            ]);

            // Solo actualizar contraseña si se proporciona
            if (!empty($_POST['usuario_contra'])) {
                $usuario->sincronizar([
                    'usuario_contra' => password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT)
                ]);
            }

            // Guardar cambios
            $resultado = $usuario->guardar();

            if ($resultado['resultado']) {
                // Actualizar rol si se proporciona
                if (!empty($_POST['usuario_rol'])) {
                    $rolId = intval($_POST['usuario_rol']);
                    
                    // Verificar que el rol existe
                    $consultaRol = "SELECT COUNT(*) as total FROM guzman_roles WHERE rol_id = $rolId AND rol_situacion = 1";
                    $resultadoRol = self::fetchFirst($consultaRol);

                    if ($resultadoRol['total'] > 0) {
                        // Actualizar rol del usuario
                        $queryActualizarRol = "UPDATE guzman_permisos_roles SET permiso_rol = $rolId 
                                              WHERE permiso_usuario = $id AND permiso_situacion = 1";
                        self::SQL($queryActualizarRol);
                    }
                }

                http_response_code(200);
                echo json_encode(['codigo' => 1, 'mensaje' => 'Usuario actualizado exitosamente']);
            } else {
                http_response_code(400);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Error al actualizar el usuario']);
            }
        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar el usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        // hasPermissionApi(['ADMINISTRADOR']);
        getHeadersApi();

        try {
            $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
            
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'ID de usuario inválido'
                ]);
                return;
            }

            // Verificar si el usuario existe y está activo usando SQL directo
            $consultaUsuario = "SELECT usuario_id, usuario_situacion FROM guzman_usuarios WHERE usuario_id = $id";
            $datosUsuario = self::fetchFirst($consultaUsuario);
            
            if (!$datosUsuario || $datosUsuario['usuario_situacion'] == 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Usuario no encontrado o ya eliminado'
                ]);
                return;
            }

            // Actualizar situación del usuario usando SQL directo
            $queryEliminar = "UPDATE guzman_usuarios SET usuario_situacion = 0 WHERE usuario_id = $id";
            $resultadoUsuario = self::SQL($queryEliminar);

            // También deshabilitar permisos del usuario
            $queryPermisos = "UPDATE guzman_permisos_roles SET permiso_situacion = 0 WHERE permiso_usuario = $id";
            self::SQL($queryPermisos);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'El usuario ha sido eliminado correctamente'
            ]);

        } catch (Exception $e) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar el usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

}