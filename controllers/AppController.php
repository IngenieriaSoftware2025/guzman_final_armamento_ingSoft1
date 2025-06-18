<?php

namespace Controllers;

use Exception;
use Model\ActiveRecord;
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

            $usario = filter_var($_POST['usu_codigo'], FILTER_SANITIZE_NUMBER_INT);
            $constrasena = htmlspecialchars($_POST['usu_password']);

            $queyExisteUser = "SELECT USU_NOMBRE, USU_PASSWORD FROM USUARIO_LOGIN2025 WHERE USU_CODIGO = $usario AND USU_SITUACION = 1";

            $ExisteUsuario = ActiveRecord::fetchArray($queyExisteUser)[0];

            if ($ExisteUsuario) {

                $passDB = $ExisteUsuario['usu_password'];

                if (password_verify($constrasena, $passDB)) {

                    session_start();

                    $nombreUser = $ExisteUsuario['usu_nombre'];

                    $_SESSION['user'] = $nombreUser;

                    $sqlpermisos = "SELECT PERMISO_ROL, ROL_NOMBRE_CT FROM PERMISO_LOGIN2025
                                INNER JOIN ROL_LOGIN2025 ON ROL_ID = PERMISO_ROL
                                INNER JOIN USUARIO_LOGIN2025 ON USU_ID = PERMISO_USUARIO
                                WHERE USU_CODIGO = $usario";

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
