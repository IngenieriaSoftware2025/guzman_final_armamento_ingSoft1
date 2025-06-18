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
            $consulta = "SELECT usuario_id, usuario_nombre, usuario_apellido, usuario_dpi, 
                               usuario_correo, usuario_fecha_creacion, usuario_fotografia, usuario_situacion 
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

            // Procesar fotografía si se subió
            $rutaFotografia = null;
            if (isset($_FILES['usuario_fotografia']) && $_FILES['usuario_fotografia']['error'] === 0) {
                $resultadoFoto = self::procesarFotografia($_FILES['usuario_fotografia']);
                if ($resultadoFoto['success']) {
                    $rutaFotografia = $resultadoFoto['ruta'];
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => $resultadoFoto['mensaje']
                    ]);
                    return;
                }
            }

            $usuario = new Usuario([
                'usuario_nombre' => $nombre,
                'usuario_apellido' => $apellido,
                'usuario_dpi' => $dpi,
                'usuario_correo' => $correo,
                'usuario_contra' => $password,
                'usuario_fotografia' => $rutaFotografia,
                'usuario_situacion' => $situacion
            ]);

            $resultado = $usuario->crear();

            if ($resultado['resultado']) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuario creado exitosamente',
                    'id' => $resultado['id']
                ]);
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
            // Buscar usuario existente con consulta SQL directa
            $consultaUsuario = "SELECT * FROM guzman_usuarios WHERE usuario_id = $id";
            $datosUsuario = self::fetchFirst($consultaUsuario);
            
            if (!$datosUsuario) {
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

            // Procesar nueva fotografía si se subió
            $rutaFotografia = $datosUsuario['usuario_fotografia'] ?? null;
            if (isset($_FILES['usuario_fotografia']) && $_FILES['usuario_fotografia']['error'] === 0) {
                // Eliminar fotografía anterior si existe
                if ($rutaFotografia && file_exists(__DIR__ . '/../public/' . $rutaFotografia)) {
                    unlink(__DIR__ . '/../public/' . $rutaFotografia);
                }
                
                $resultadoFoto = self::procesarFotografia($_FILES['usuario_fotografia']);
                if ($resultadoFoto['success']) {
                    $rutaFotografia = $resultadoFoto['ruta'];
                } else {
                    http_response_code(400);
                    echo json_encode(['codigo' => 0, 'mensaje' => $resultadoFoto['mensaje']]);
                    return;
                }
            }

            // Construir query de actualización
            $queryUpdate = "UPDATE guzman_usuarios SET 
                           usuario_nombre = " . self::$db->quote($nombre) . ",
                           usuario_apellido = " . self::$db->quote($apellido) . ",
                           usuario_dpi = " . self::$db->quote($dpi) . ",
                           usuario_correo = " . self::$db->quote($correo) . ",
                           usuario_fotografia = " . ($rutaFotografia ? self::$db->quote($rutaFotografia) : "NULL") . ",
                           usuario_situacion = $situacion";

            // Solo actualizar contraseña si se proporciona
            if (!empty($_POST['usuario_contra'])) {
                $passwordHash = password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT);
                $queryUpdate .= ", usuario_contra = " . self::$db->quote($passwordHash);
            }

            $queryUpdate .= " WHERE usuario_id = $id";

            // Ejecutar actualización
            $resultado = self::SQL($queryUpdate);

            if ($resultado !== false) {
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
        // hasPermissionApi(['OFICIAL']);
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

            // Verificar si el usuario existe y obtener datos
            $consultaUsuario = "SELECT usuario_id, usuario_situacion, usuario_fotografia FROM guzman_usuarios WHERE usuario_id = $id";
            $datosUsuario = self::fetchFirst($consultaUsuario);
            
            if (!$datosUsuario || $datosUsuario['usuario_situacion'] == 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Usuario no encontrado o ya eliminado'
                ]);
                return;
            }

            // Eliminar fotografía si existe
            if ($datosUsuario['usuario_fotografia'] && file_exists(__DIR__ . '/../public/' . $datosUsuario['usuario_fotografia'])) {
                unlink(__DIR__ . '/../public/' . $datosUsuario['usuario_fotografia']);
            }

            // Actualizar situación del usuario
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

    public static function obtenerUsuarioAPI()
    {
        // hasPermissionApi(['OFICIAL']);
        getHeadersApi();

        $id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['codigo' => 0, 'mensaje' => 'ID de usuario inválido']);
            return;
        }

        try {
            $consulta = "SELECT usuario_id, usuario_nombre, usuario_apellido, usuario_dpi, 
                               usuario_correo, usuario_fecha_creacion, usuario_fotografia, usuario_situacion
                        FROM guzman_usuarios 
                        WHERE usuario_id = $id AND usuario_situacion != 0";
            
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

    private static function procesarFotografia($archivo)
    {
        $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $tamaño_maximo = 5 * 1024 * 1024; // 5MB

        $nombre_archivo = $archivo['name'];
        $tamaño_archivo = $archivo['size'];
        $archivo_temporal = $archivo['tmp_name'];

        // Validar extensión
        $extension = strtolower(pathinfo($nombre_archivo, PATHINFO_EXTENSION));
        if (!in_array($extension, $extensiones_permitidas)) {
            return [
                'success' => false,
                'mensaje' => 'Solo se permiten archivos JPG, JPEG, PNG y GIF'
            ];
        }

        // Validar tamaño
        if ($tamaño_archivo > $tamaño_maximo) {
            return [
                'success' => false,
                'mensaje' => 'El archivo es demasiado grande. Máximo 5MB'
            ];
        }

        // Generar nombre único
        $nombre_unico = uniqid() . '_' . time() . '.' . $extension;
        $directorio = __DIR__ . '/../public/uploads/usuarios/';
        
        // Crear directorio si no existe
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        $ruta_completa = $directorio . $nombre_unico;
        $ruta_relativa = 'uploads/usuarios/' . $nombre_unico;

        // Mover archivo
        if (move_uploaded_file($archivo_temporal, $ruta_completa)) {
            return [
                'success' => true,
                'ruta' => $ruta_relativa
            ];
        } else {
            return [
                'success' => false,
                'mensaje' => 'Error al subir el archivo'
            ];
        }
    }
}