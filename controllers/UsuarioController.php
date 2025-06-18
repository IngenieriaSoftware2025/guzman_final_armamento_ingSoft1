<?php

namespace Controllers;

use Model\Usuario;
use Model\ActiveRecord;
use MVC\Router;
use Exception;

class UsuarioController
{
    public static function index(Router $router)
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /guzman_final_armamento_ingSoft1/');
            exit;
        }

        $usuarios = static::obtenerUsuariosActivos();
        
        $router->render('usuarios/index', [
            'usuarios' => $usuarios
        ], 'layouts/layout');
    }

    public static function crear(Router $router)
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /guzman_final_armamento_ingSoft1/');
            exit;
        }

        $usuario = new Usuario();
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            
            $errores = static::validarUsuario($usuario);
            
            if (empty($errores)) {
                $usuarioExistente = static::buscarPorCorreo($usuario->usuario_correo);
                if ($usuarioExistente) {
                    $errores[] = 'El correo electrónico ya está registrado';
                }
            }

            if (empty($errores)) {
                $dpiExistente = static::buscarPorDPI($usuario->usuario_dpi);
                if ($dpiExistente) {
                    $errores[] = 'El DPI ya está registrado';
                }
            }

            if (empty($errores) && !empty($_FILES['usuario_fotografia']['name'])) {
                $resultado = static::procesarFotografia($_FILES['usuario_fotografia']);
                if ($resultado['error']) {
                    $errores[] = $resultado['mensaje'];
                } else {
                    $usuario->usuario_fotografia = $resultado['nombre_archivo'];
                }
            }

            if (empty($errores)) {
                $usuario->usuario_contra = password_hash($usuario->usuario_contra, PASSWORD_BCRYPT);
                $usuario->usuario_fecha_creacion = date('Y-m-d');
            }

            if (empty($errores)) {
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /guzman_final_armamento_ingSoft1/usuarios');
                    exit;
                }
            }
        }

        $router->render('usuarios/crear', [
            'usuario' => $usuario,
            'errores' => $errores
        ], 'layouts/layout');
    }

    public static function actualizar(Router $router)
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            header('Location: /guzman_final_armamento_ingSoft1/');
            exit;
        }

        $id = validarORedireccionar('/guzman_final_armamento_ingSoft1/usuarios');
        $usuario = Usuario::find($id);
        $errores = [];

        if (!$usuario) {
            header('Location: /guzman_final_armamento_ingSoft1/usuarios');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $passwordAnterior = $usuario->usuario_contra;
            $usuario->sincronizar($_POST);
            
            $errores = static::validarUsuarioEdicion($usuario);
            
            if (empty($errores)) {
                $usuarioExistente = static::buscarPorCorreoExceptoId($usuario->usuario_correo, $usuario->usuario_id);
                if ($usuarioExistente) {
                    $errores[] = 'El correo electrónico ya está registrado';
                }
            }

            if (empty($errores)) {
                $dpiExistente = static::buscarPorDPIExceptoId($usuario->usuario_dpi, $usuario->usuario_id);
                if ($dpiExistente) {
                    $errores[] = 'El DPI ya está registrado';
                }
            }

            if (empty($errores) && !empty($_FILES['usuario_fotografia']['name'])) {
                $resultado = static::procesarFotografia($_FILES['usuario_fotografia']);
                if ($resultado['error']) {
                    $errores[] = $resultado['mensaje'];
                } else {
                    if ($usuario->usuario_fotografia) {
                        static::eliminarFotografia($usuario->usuario_fotografia);
                    }
                    $usuario->usuario_fotografia = $resultado['nombre_archivo'];
                }
            }

            if (empty($errores)) {
                if (!empty($_POST['usuario_contra'])) {
                    $usuario->usuario_contra = password_hash($usuario->usuario_contra, PASSWORD_BCRYPT);
                } else {
                    $usuario->usuario_contra = $passwordAnterior;
                }
            }

            if (empty($errores)) {
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /guzman_final_armamento_ingSoft1/usuarios');
                    exit;
                }
            }
        }

        $router->render('usuarios/actualizar', [
            'usuario' => $usuario,
            'errores' => $errores
        ], 'layouts/layout');
    }

    public static function eliminar()
    {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            getHeadersApi();
            echo json_encode(['codigo' => 0, 'mensaje' => 'Sesión no válida']);
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            getHeadersApi();

            try {
                $id = $_POST['id'] ?? null;
                $id = filter_var($id, FILTER_VALIDATE_INT);

                if ($id) {
                    $usuario = Usuario::find($id);
                    if ($usuario) {
                        $usuario->usuario_situacion = 0;
                        $resultado = $usuario->guardar();
                        
                        if ($resultado) {
                            echo json_encode([
                                'codigo' => 1,
                                'mensaje' => 'Usuario eliminado correctamente'
                            ]);
                        } else {
                            echo json_encode([
                                'codigo' => 0,
                                'mensaje' => 'Error al eliminar usuario'
                            ]);
                        }
                    } else {
                        echo json_encode([
                            'codigo' => 0,
                            'mensaje' => 'Usuario no encontrado'
                        ]);
                    }
                } else {
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'ID de usuario inválido'
                    ]);
                }
            } catch (Exception $e) {
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Error al eliminar usuario',
                    'detalle' => $e->getMessage()
                ]);
            }
        }
    }

    // MÉTODOS PRIVADOS DE VALIDACIÓN Y UTILIDADES

    private static function validarUsuario($usuario)
    {
        $errores = [];

        if (!$usuario->usuario_nombre) {
            $errores[] = 'El nombre es obligatorio';
        }

        if (!$usuario->usuario_apellido) {
            $errores[] = 'El apellido es obligatorio';
        }

        if (!$usuario->usuario_dpi) {
            $errores[] = 'El DPI es obligatorio';
        }

        if (strlen($usuario->usuario_dpi) !== 13) {
            $errores[] = 'El DPI debe tener exactamente 13 dígitos';
        }

        if (!filter_var($usuario->usuario_correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido';
        }

        if (!$usuario->usuario_contra) {
            $errores[] = 'La contraseña es obligatoria';
        }

        if (strlen($usuario->usuario_contra) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }

        return $errores;
    }

    private static function validarUsuarioEdicion($usuario)
    {
        $errores = [];

        if (!$usuario->usuario_nombre) {
            $errores[] = 'El nombre es obligatorio';
        }

        if (!$usuario->usuario_apellido) {
            $errores[] = 'El apellido es obligatorio';
        }

        if (!$usuario->usuario_dpi) {
            $errores[] = 'El DPI es obligatorio';
        }

        if (strlen($usuario->usuario_dpi) !== 13) {
            $errores[] = 'El DPI debe tener exactamente 13 dígitos';
        }

        if (!filter_var($usuario->usuario_correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'El correo electrónico no es válido';
        }

        if (!empty($usuario->usuario_contra) && strlen($usuario->usuario_contra) < 6) {
            $errores[] = 'La contraseña debe tener al menos 6 caracteres';
        }

        return $errores;
    }

    private static function obtenerUsuariosActivos()
    {
        $query = "SELECT * FROM guzman_usuarios WHERE usuario_situacion = 1 ORDER BY usuario_id DESC";
        return Usuario::consultarSQL($query);
    }

    private static function buscarPorCorreo($correo)
    {
        $query = "SELECT * FROM guzman_usuarios WHERE usuario_correo = '$correo' AND usuario_situacion = 1";
        $resultado = Usuario::consultarSQL($query);
        return !empty($resultado) ? array_shift($resultado) : null;
    }

    private static function buscarPorDPI($dpi)
    {
        $query = "SELECT * FROM guzman_usuarios WHERE usuario_dpi = '$dpi' AND usuario_situacion = 1";
        $resultado = Usuario::consultarSQL($query);
        return !empty($resultado) ? array_shift($resultado) : null;
    }

    private static function buscarPorCorreoExceptoId($correo, $id)
    {
        $query = "SELECT * FROM guzman_usuarios WHERE usuario_correo = '$correo' AND usuario_id != $id AND usuario_situacion = 1";
        $resultado = Usuario::consultarSQL($query);
        return !empty($resultado) ? array_shift($resultado) : null;
    }

    private static function buscarPorDPIExceptoId($dpi, $id)
    {
        $query = "SELECT * FROM guzman_usuarios WHERE usuario_dpi = '$dpi' AND usuario_id != $id AND usuario_situacion = 1";
        $resultado = Usuario::consultarSQL($query);
        return !empty($resultado) ? array_shift($resultado) : null;
    }

    private static function procesarFotografia($archivo)
    {
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        if (!in_array($archivo['type'], $tiposPermitidos)) {
            return [
                'error' => true,
                'mensaje' => 'El archivo debe ser una imagen válida (JPG, PNG, GIF)'
            ];
        }

        $tamanoMaximo = 5 * 1024 * 1024;
        if ($archivo['size'] > $tamanoMaximo) {
            return [
                'error' => true,
                'mensaje' => 'La imagen no puede pesar más de 5MB'
            ];
        }

        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = md5(uniqid(rand(), true)) . '.' . $extension;
        $rutaDestino = $_SERVER['DOCUMENT_ROOT'] . '/imagenes/usuarios/' . $nombreArchivo;

        if (!file_exists(dirname($rutaDestino))) {
            mkdir(dirname($rutaDestino), 0755, true);
        }

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return [
                'error' => false,
                'nombre_archivo' => $nombreArchivo
            ];
        } else {
            return [
                'error' => true,
                'mensaje' => 'Error al subir la imagen'
            ];
        }
    }

    private static function eliminarFotografia($nombreArchivo)
    {
        $ruta = $_SERVER['DOCUMENT_ROOT'] . '/imagenes/usuarios/' . $nombreArchivo;
        if (file_exists($ruta)) {
            unlink($ruta);
        }
    }
}