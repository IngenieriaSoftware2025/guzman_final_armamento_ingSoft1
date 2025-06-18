<?php

namespace Controllers;

use Model\Usuario;
use MVC\Router;

class UsuarioController
{
    public static function index(Router $router)
    {
        $usuarios = Usuario::all();
        
        $router->render('usuarios/index', [
            'usuarios' => $usuarios
        ]);
    }

    public static function crear(Router $router)
    {
        $usuario = new Usuario();
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            
            // Validaciones
            $errores = static::validarUsuario($usuario);
            
            // Validar que no exista el correo
            if (empty($errores)) {
                $usuarioExistente = static::buscarPorCorreo($usuario->usuario_correo);
                if ($usuarioExistente) {
                    $errores[] = 'El correo electrónico ya está registrado';
                }
            }

            // Validar que no exista el DPI
            if (empty($errores)) {
                $dpiExistente = static::buscarPorDPI($usuario->usuario_dpi);
                if ($dpiExistente) {
                    $errores[] = 'El DPI ya está registrado';
                }
            }

            // Procesar fotografía si se subió
            if (empty($errores) && !empty($_FILES['usuario_fotografia']['name'])) {
                $resultado = static::procesarFotografia($_FILES['usuario_fotografia']);
                if ($resultado['error']) {
                    $errores[] = $resultado['mensaje'];
                } else {
                    $usuario->usuario_fotografia = $resultado['nombre_archivo'];
                }
            }

            // Hashear contraseña
            if (empty($errores)) {
                $usuario->usuario_contra = password_hash($usuario->usuario_contra, PASSWORD_BCRYPT);
            }

            // Guardar en base de datos
            if (empty($errores)) {
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /usuarios');
                }
            }
        }

        $router->render('usuarios/crear', [
            'usuario' => $usuario,
            'errores' => $errores
        ]);
    }

    public static function actualizar(Router $router)
    {
        $id = validarORedireccionar('/usuarios');
        $usuario = Usuario::find($id);
        $errores = [];

        if (!$usuario) {
            header('Location: /usuarios');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usuario->sincronizar($_POST);
            
            // Validaciones
            $errores = static::validarUsuario($usuario);
            
            // Validar que no exista el correo (excepto el actual)
            if (empty($errores)) {
                $usuarioExistente = static::buscarPorCorreoExceptoId($usuario->usuario_correo, $usuario->usuario_id);
                if ($usuarioExistente) {
                    $errores[] = 'El correo electrónico ya está registrado';
                }
            }

            // Validar que no exista el DPI (excepto el actual)
            if (empty($errores)) {
                $dpiExistente = static::buscarPorDPIExceptoId($usuario->usuario_dpi, $usuario->usuario_id);
                if ($dpiExistente) {
                    $errores[] = 'El DPI ya está registrado';
                }
            }

            // Procesar fotografía si se subió
            if (empty($errores) && !empty($_FILES['usuario_fotografia']['name'])) {
                $resultado = static::procesarFotografia($_FILES['usuario_fotografia']);
                if ($resultado['error']) {
                    $errores[] = $resultado['mensaje'];
                } else {
                    // Eliminar fotografía anterior si existe
                    if ($usuario->usuario_fotografia) {
                        static::eliminarFotografia($usuario->usuario_fotografia);
                    }
                    $usuario->usuario_fotografia = $resultado['nombre_archivo'];
                }
            }

            // Hashear contraseña solo si se cambió
            if (empty($errores) && !empty($_POST['usuario_contra'])) {
                $usuario->usuario_contra = password_hash($usuario->usuario_contra, PASSWORD_BCRYPT);
            }

            // Guardar en base de datos
            if (empty($errores)) {
                $resultado = $usuario->guardar();
                if ($resultado) {
                    header('Location: /usuarios');
                }
            }
        }

        $router->render('usuarios/actualizar', [
            'usuario' => $usuario,
            'errores' => $errores
        ]);
    }

    public static function eliminar()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $id = filter_var($id, FILTER_VALIDATE_INT);

            if ($id) {
                $usuario = Usuario::find($id);
                if ($usuario) {
                    // Cambiar situación a 0 (eliminación lógica)
                    $usuario->usuario_situacion = 0;
                    $resultado = $usuario->guardar();
                }
            }
        }
        header('Location: /usuarios');
    }

    public static function login(Router $router)
    {
        $errores = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $correo = $_POST['usuario_correo'] ?? '';
            $password = $_POST['usuario_contra'] ?? '';

            if (empty($correo)) {
                $errores[] = 'El correo es obligatorio';
            }

            if (empty($password)) {
                $errores[] = 'La contraseña es obligatoria';
            }

            if (empty($errores)) {
                $usuario = static::buscarPorCorreo($correo);
                
                if (!$usuario) {
                    $errores[] = 'Usuario no encontrado';
                } else {
                    if (!password_verify($password, $usuario->usuario_contra)) {
                        $errores[] = 'Contraseña incorrecta';
                    } else {
                        // Iniciar sesión
                        session_start();
                        $_SESSION['usuario_id'] = $usuario->usuario_id;
                        $_SESSION['usuario_nombre'] = $usuario->usuario_nombre;
                        $_SESSION['usuario_apellido'] = $usuario->usuario_apellido;
                        $_SESSION['login'] = true;

                        header('Location: /dashboard');
                    }
                }
            }
        }

        $router->render('auth/login', [
            'errores' => $errores
        ]);
    }

    public static function logout()
    {
        session_start();
        $_SESSION = [];
        session_destroy();
        header('Location: /');
    }

    // Métodos privados para validaciones y utilidades

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

    private static function buscarPorCorreo($correo)
    {
        $query = "SELECT * FROM guzman_usuarios WHERE usuario_correo = ? AND usuario_situacion = 1";
        $resultado = Usuario::consultarSQL($query, [$correo]);
        return $resultado ? array_shift($resultado) : null;
    }

    private static function buscarPorDPI($dpi)
    {
        $query = "SELECT * FROM guzman_usuarios WHERE usuario_dpi = ? AND usuario_situacion = 1";
        $resultado = Usuario::consultarSQL($query, [$dpi]);
        return $resultado ? array_shift($resultado) : null;
    }

    private static function buscarPorCorreoExceptoId($correo, $id)
    {
        $query = "SELECT * FROM guzman_usuarios WHERE usuario_correo = ? AND usuario_id != ? AND usuario_situacion = 1";
        $resultado = Usuario::consultarSQL($query, [$correo, $id]);
        return $resultado ? array_shift($resultado) : null;
    }

    private static function buscarPorDPIExceptoId($dpi, $id)
    {
        $query = "SELECT * FROM guzman_usuarios WHERE usuario_dpi = ? AND usuario_id != ? AND usuario_situacion = 1";
        $resultado = Usuario::consultarSQL($query, [$dpi, $id]);
        return $resultado ? array_shift($resultado) : null;
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

        $tamanoMaximo = 5 * 1024 * 1024; // 5MB
        if ($archivo['size'] > $tamanoMaximo) {
            return [
                'error' => true,
                'mensaje' => 'La imagen no puede pesar más de 5MB'
            ];
        }

        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = md5(uniqid(rand(), true)) . '.' . $extension;
        $rutaDestino = $_SERVER['DOCUMENT_ROOT'] . '/imagenes/usuarios/' . $nombreArchivo;

        // Crear directorio si no existe
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