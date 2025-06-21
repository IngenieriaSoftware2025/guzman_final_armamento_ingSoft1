<?php

namespace Controllers;

use Exception;
use MVC\Router;
use Model\ActiveRecord;
use Model\Usuarios;

class UsuariosController extends ActiveRecord
{
    public static function renderizarPagina(Router $router)
    {
        $router->render('usuarios/index', []);
    }

    public static function guardarAPI()
    {
        getHeadersApi();
        $_POST['usuario_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nombre']))));
        $cantidad_nombre = strlen($_POST['usuario_nombre']);

        if ($cantidad_nombre < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre debe tener más de 1 caracter'
            ]);
            exit;
        }

        $_POST['usuario_apellido'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_apellido']))));
        $cantidad_apellido = strlen($_POST['usuario_apellido']);

        if ($cantidad_apellido < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El apellido debe tener más de 1 caracter'
            ]);
            exit;
        }

        $_POST['usuario_dpi'] = filter_var($_POST['usuario_dpi'], FILTER_VALIDATE_INT);
        if (strlen($_POST['usuario_dpi']) != 13) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI debe tener exactamente 13 dígitos'
            ]);
            exit;
        }

        $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($_POST['usuario_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico no es válido'
            ]);
            exit;
        }

        $usuarioExistente = self::fetchFirst("SELECT usuario_id FROM guzman_usuarios WHERE usuario_correo = '{$_POST['usuario_correo']}'");
        if ($usuarioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico ya está registrado'
            ]);
            exit;
        }

        $dpiExistente = self::fetchFirst("SELECT usuario_id FROM guzman_usuarios WHERE usuario_dpi = '{$_POST['usuario_dpi']}'");
        if ($dpiExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI ya está registrado'
            ]);
            exit;
        }

        if (strlen($_POST['usuario_contra']) < 6) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La contraseña debe tener al menos 6 caracteres'
            ]);
            exit;
        }

        $dpi = $_POST['usuario_dpi'];
        $file = $_FILES['usuario_fotografia'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileError = $file['error'];

        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($fileExtension, $allowed)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Solo puede cargar archivos JPG, JPEG, PNG o GIF'
            ]);
            exit;
        }

        if ($fileSize >= 5000000) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'La imagen debe pesar menos de 5MB'
            ]);
            exit;
        }

        if ($fileError === 0) {
            $directorioFotos = __DIR__ . "storage/fotosUsuarios/";
            if (!is_dir($directorioFotos)) {
                mkdir($directorioFotos, 0755, true);
            }

            $ruta = "storage/fotosUsuarios/$dpi.$fileExtension";
            $subido = move_uploaded_file($file['tmp_name'], __DIR__ . "/../../" . $ruta);

            if ($subido) {
                $_POST['usuario_contra'] = password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT);

                $usuario = new Usuarios($_POST);
                $usuario->usuario_fotografia = $ruta;
                $resultado = $usuario->crear();

                if ($resultado['resultado'] == 1) {
                    http_response_code(200);
                    echo json_encode([
                        'codigo' => 1,
                        'mensaje' => 'Usuario registrado correctamente'
                    ]);
                    exit;
                } else {
                    http_response_code(500);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'Error al registrar el usuario'
                    ]);
                    exit;
                }
            }
        } else {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error en la carga de fotografía'
            ]);
            exit;
        }
    }

    public static function buscarAPI()
    {
        getHeadersApi();

        try {
            $sql = "SELECT * FROM guzman_usuarios WHERE usuario_situacion = 1";
            $data = self::fetchArray($sql);

            if (count($data) > 0) {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Usuarios obtenidos correctamente',
                    'data' => $data
                ]);
            } else {
                http_response_code(200);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'No hay usuarios registrados',
                    'data' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error en el servidor',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function modificarAPI()
    {
        getHeadersApi();

        $id = $_POST['usuario_id'];
        $_POST['usuario_nombre'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_nombre']))));
        if (strlen($_POST['usuario_nombre']) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El nombre debe tener más de 1 caracter'
            ]);
            return;
        }

        $_POST['usuario_apellido'] = ucwords(strtolower(trim(htmlspecialchars($_POST['usuario_apellido']))));
        if (strlen($_POST['usuario_apellido']) < 2) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El apellido debe tener más de 1 caracter'
            ]);
            return;
        }

        $_POST['usuario_dpi'] = filter_var($_POST['usuario_dpi'], FILTER_SANITIZE_NUMBER_INT);
        if (strlen($_POST['usuario_dpi']) != 13) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI debe tener exactamente 13 dígitos'
            ]);
            return;
        }

        $_POST['usuario_correo'] = filter_var($_POST['usuario_correo'], FILTER_SANITIZE_EMAIL);
        if (!filter_var($_POST['usuario_correo'], FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico no es válido'
            ]);
            return;
        }

        $usuarioExistente = self::fetchFirst("SELECT usuario_id FROM guzman_usuarios WHERE usuario_correo = '{$_POST['usuario_correo']}' AND usuario_id != $id");
        if ($usuarioExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El correo electrónico ya está registrado por otro usuario'
            ]);
            return;
        }

        $dpiExistente = self::fetchFirst("SELECT usuario_id FROM guzman_usuarios WHERE usuario_dpi = '{$_POST['usuario_dpi']}' AND usuario_id != $id");
        if ($dpiExistente) {
            http_response_code(400);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'El DPI ya está registrado por otro usuario'
            ]);
            return;
        }

        try {
            $usuario = Usuarios::find($id);

            if (!$usuario) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Usuario no encontrado'
                ]);
                return;
            }

            $usuario->sincronizar([
                'usuario_nombre' => $_POST['usuario_nombre'],
                'usuario_apellido' => $_POST['usuario_apellido'],
                'usuario_dpi' => $_POST['usuario_dpi'],
                'usuario_correo' => $_POST['usuario_correo'],
                'usuario_situacion' => $_POST['usuario_situacion'] ?? 1
            ]);

            if (!empty($_POST['usuario_contra'])) {
                if (strlen($_POST['usuario_contra']) < 6) {
                    http_response_code(400);
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'La contraseña debe tener al menos 6 caracteres'
                    ]);
                    return;
                }
                $usuario->usuario_contra = password_hash($_POST['usuario_contra'], PASSWORD_DEFAULT);
            }

            $usuario->actualizar();

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuario modificado exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al modificar usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarAPI()
    {
        getHeadersApi();

        try {
            $id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

            if (!$id || $id <= 0) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'ID de usuario inválido'
                ]);
                return;
            }

            $consulta = "UPDATE guzman_usuarios SET usuario_situacion = 0 WHERE usuario_id = $id";
            self::SQL($consulta);

            http_response_code(200);
            echo json_encode([
                'codigo' => 1,
                'mensaje' => 'Usuario eliminado exitosamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al eliminar usuario',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
