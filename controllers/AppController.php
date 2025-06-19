<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
use MVC\Router;

class AppController
{
    public static function index(Router $router)
    {
        $router->render('pages/index', [], 'layouts/layout');
    }


    public static function login()
    {
        getHeadersApi();

        try {

            $usario = filter_var($_POST['usuario_dpi'], FILTER_SANITIZE_NUMBER_INT);
            $constrasena = htmlspecialchars($_POST['usuario_contra']);

            $queyExisteUser = "SELECT usuario_nombre, usuario_contra FROM guzman_usuario WHERE usuario_dpi = $usario AND usuario_situacion = 1";

            $ExisteUsuario = ActiveRecord::fetchArray($queyExisteUser)[0];

            if ($ExisteUsuario) {

                $passDB = $ExisteUsuario['usuario_contra'];

                if (password_verify($constrasena, $passDB)) {

                    session_start();

                    $nombreUser = $ExisteUsuario['usuario_nombre'];

                    $_SESSION['user'] = $nombreUser;

                    $sqlpermisos = "SELECT PERMISO_ROL, ROL_NOMBRE_CT FROM PERMISO_LOGIN2025
                                INNER JOIN ROL_LOGIN2025 ON ROL_ID = PERMISO_ROL
                                INNER JOIN USUARIO_LOGIN2025 ON USU_ID = PERMISO_USUARIO
                                WHERE usuario_dpi = $usario";

                    $permiso = ActiveRecord::fetchArray($sqlpermisos)[0]['rol_nombre_ct'];

                    $_SESSION['rol'] = $permiso;

                    echo json_encode([
                        'codigo' => 1,
                        'mensaje' => 'usuario logueado existosamente',

                    ]);
                } else {
                    echo json_encode([
                        'codigo' => 0,
                        'mensaje' => 'La contraseña que ingreso es Incorrecta',

                    ]);
                }
            } else {
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El usuario que intenta loguearse NO EXISTE',

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

    public static function renderInicio(Router $router){

        $router->render('pages/index', [], 'layouts/menu');
    }
}
