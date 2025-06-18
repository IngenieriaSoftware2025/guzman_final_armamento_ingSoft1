<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use Model\Usuario;
use MVC\Router;

class AppController
{
    public static function index(Router $router)
    {
        $router->render('login/index', [], 'layouts/login');
    }

    public static function login()
    {
        getHeadersApi();

        try {
            $dpi = filter_var($_POST['usuario_dpi'], FILTER_SANITIZE_STRING);
            $password = htmlspecialchars($_POST['usuario_password']);

            // Buscar usuario por DPI usando nombres de campos correctos
            $query = "SELECT usuario_id, usuario_nombre, usuario_apellido, usuario_dpi, usuario_contra 
                  FROM guzman_usuarios 
                  WHERE usuario_dpi = '$dpi' AND usuario_situacion = 1";

            $usuario = ActiveRecord::fetchFirst($query);

            if ($usuario) {
                $passDB = $usuario['usuario_contra'];

                if (password_verify($password, $passDB)) {
                    session_start();

                    $_SESSION['user_id'] = $usuario['usuario_id'];
                    $_SESSION['user_nombre'] = $usuario['usuario_nombre'];
                    $_SESSION['user_apellido'] = $usuario['usuario_apellido'];
                    $_SESSION['user_dpi'] = $usuario['usuario_dpi'];

                    // Obtener roles del usuario
                    $sqlRoles = "SELECT r.rol_id, r.rol_nombre, r.rol_descripcion 
                            FROM guzman_roles r 
                            INNER JOIN guzman_permisos_roles pr ON r.rol_id = pr.permiso_rol 
                            WHERE pr.permiso_usuario = {$usuario['usuario_id']} 
                            AND pr.permiso_situacion = 1 AND r.rol_situacion = 1";

                    $roles = ActiveRecord::fetchArray($sqlRoles);

                    $_SESSION['user_roles'] = $roles;
                    $_SESSION['user_rol_principal'] = !empty($roles) ? $roles[0]['rol_nombre'] : 'USUARIO';

                    echo json_encode([
                        'codigo' => 1,
                        'mensaje' => 'Usuario logueado exitosamente',
                        'usuario' => [
                            'nombre' => $usuario['usuario_nombre'],
                            'apellido' => $usuario['usuario_apellido'],
                            'rol' => $_SESSION['user_rol_principal']
                        ]
                    ]);
                } else {
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'La contraseña que ingresó es incorrecta'
                    ]);
                }
            } else {
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El DPI que intenta usar para login NO EXISTE'
                ]);
            }
        } catch (Exception $e) {
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al intentar loguearse',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function renderInicio(Router $router)
    {
        session_start();

        // Verificar si el usuario está logueado
        if (!isset($_SESSION['user_id'])) {
            header('Location: /guzman_final_armamento_ingSoft1/');
            exit;
        }

        $router->render('pages/index', [], 'layouts/menu');
    }

    public static function logout()
    {
        session_start();
        $_SESSION = [];
        session_destroy();
        header('Location: /guzman_final_armamento_ingSoft1/');
        exit;
    }
}
