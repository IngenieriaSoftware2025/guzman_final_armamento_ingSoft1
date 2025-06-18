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
        isAuth();
        hasPermission(['OFICIAL']);
        $router->render('usuarios/index', []);
    }

    public static function buscarAPI()
    {
        hasPermissionApi(['OFICIAL']);
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
        hasPermissionApi(['OFICIAL']);
        getHeadersApi();

        // Validar campos obligatorios
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

        if (empty($_POST['usuario_rol'])) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El rol del usuario es obligatorio'
            ]);
            return;
        }

        try {
            // Sanitizar datos de entrada
            $nombre = trim(htmlspecialchars($_POST['usuario_nombre']));
            $apellido = trim(htmlspecialchars($_POST['usuario_apellido']));
            $dpi = trim(htmlspecialchars($_POST['usuario_dpi']));
            $correo = trim(htmlspecialchars($_POST['usuario_correo']));
            $password = password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT);
            $rolId = intval($_POST['usuario_rol']);
            $situacion = intval($_POST['usuario_situacion'] ?? 1);

            // Validar formato de DPI (13 dígitos)
            if (!preg_match('/^\d{13}$/', $dpi)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El DPI debe tener exactamente 13 dígitos'
                ]);
                return;
            }

            // Validar formato de correo electrónico
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El formato del correo electrónico no es válido'
                ]);
                return;
            }

            // Verificar si el DPI ya existe
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

            // Verificar si el correo ya existe
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

            // Verificar que el rol existe
            $consultaRol = "SELECT COUNT(*) as total FROM guzman_roles WHERE rol_id = $rolId AND rol_situacion = 1";
            $resultadoRol = self::fetchFirst($consultaRol);

            if ($resultadoRol['total'] == 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El rol seleccionado no es válido'
                ]);
                return;
            }

            // Crear instancia del modelo Usuario
            $usuario = new Usuario([
                'usuario_nombre' => $nombre,
                'usuario_apellido' => $apellido,
                'usuario_dpi' => $dpi,
                'usuario_correo' => $correo,
                'usuario_contra' => $password,
                'usuario_situacion' => $situacion
            ]);

            // Guardar usuario usando el modelo
            $resultado = $usuario->crear();

            if ($resultado['resultado']) {
                // Obtener el ID del usuario recién creado
                $usuarioId = $resultado['id'];

                // Insertar permiso de rol
                $queryPermiso = "INSERT INTO guzman_permisos_roles (permiso_usuario, permiso_rol) 
                               VALUES ($usuarioId, $rolId)";
                
                $resultadoPermiso = self::SQL($queryPermiso);

                if ($resultadoPermiso) {
                    http_response_code(200);
                    echo json_encode([
                        'codigo' => 1,
                        'mensaje' => 'Usuario creado exitosamente',
                        'data' => ['id' => $usuarioId]
                    ]);
                } else {
                    // Rollback: eliminar usuario si falla la asignación de rol
                    $queryEliminar = "DELETE FROM guzman_usuarios WHERE usuario_id = $usuarioId";
                    self::SQL($queryEliminar);
                    
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Error al asignar rol al usuario'
                    ]);
                }
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al crear el usuario'
                ]);
            }
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
        hasPermissionApi(['OFICIAL']);
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
        hasPermissionApi(['OFICIAL']);
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


    public static function obtenerRolesAPI()
    {
        hasPermissionApi(['OFICIAL']);
        getHeadersApi();

        try {
            $consulta = "SELECT rol_id, rol_nombre, rol_descripcion 
                        FROM guzman_roles 
                        WHERE rol_situacion = 1 
                        ORDER BY rol_nombre";
            $roles = self::fetchArray($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Roles obtenidos exitosamente',
                'data' => $roles
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener roles',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerUsuarioAPI()
    {
        hasPermissionApi(['OFICIAL']);
        getHeadersApi();

        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de usuario inválido']);
            return;
        }

        try {
            $consulta = "SELECT u.usuario_id, u.usuario_nombre, u.usuario_apellido, u.usuario_dpi, 
                               u.usuario_correo, u.usuario_fecha_creacion, u.usuario_situacion,
                               r.rol_id, r.rol_nombre, r.rol_descripcion
                        FROM guzman_usuarios u
                        LEFT JOIN guzman_permisos_roles pr ON u.usuario_id = pr.permiso_usuario
                        LEFT JOIN guzman_roles r ON pr.permiso_rol = r.rol_id
                        WHERE u.usuario_id = $id AND u.usuario_situacion != 0";
            
            $usuario = self::fetchFirst($consulta);

            if ($usuario) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario obtenido exitosamente',
                    'data' => $usuario
                ]);
            } else {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Usuario no encontrado'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al obtener el usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}