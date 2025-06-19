<?php
namespace Controllers;

use Exception;
use Model\ActiveRecord;
use MVC\Router;

class AppController extends ActiveRecord
{
    public static function index(Router $router)
    {
        $router->render('pages/index', []);
        self::renderLogin($router);
    }

    public static function renderLogin(Router $router)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        if (isset($_SESSION['usuario'])) {
            header('Location: /guzman_final_armamento_ingSoft1/dashboard');
            exit;
        }
        
        $router->render('auth/login', []);
    }

    public static function login()
    {
        getHeadersApi();

        try {
            if (empty($_POST['usuario_correo'])) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El correo electrónico es obligatorio'
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

            $correo = trim(htmlspecialchars($_POST['usuario_correo']));
            $password = $_POST['usuario_contra'];

            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                http_response_code(400);
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'El formato del correo electrónico no es válido'
                ]);
                return;
            }

            $queryExisteUser = "SELECT u.usuario_id, u.usuario_nombre, u.usuario_apellido, 
                                      u.usuario_correo, u.usuario_contra, u.usuario_situacion,
                                      r.rol_nombre
                               FROM guzman_usuarios u
                               LEFT JOIN guzman_permisos_roles pr ON u.usuario_id = pr.permiso_usuario
                               LEFT JOIN guzman_roles r ON pr.permiso_rol = r.rol_id
                               WHERE u.usuario_correo = " . self::$db->quote($correo) . "
                               AND u.usuario_situacion = 1
                               AND (pr.permiso_situacion = 1 OR pr.permiso_situacion IS NULL)";

            $existeUsuario = self::fetchFirst($queryExisteUser);

            if (!$existeUsuario) {
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Credenciales incorrectas'
                ]);
                return;
            }
            $passDB = $existeUsuario['usuario_contra'];

            if (password_verify($password, $passDB)) {
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }

                $_SESSION['usuario'] = [
                    'id' => $existeUsuario['usuario_id'],
                    'nombre' => $existeUsuario['usuario_nombre'],
                    'apellido' => $existeUsuario['usuario_apellido'],
                    'correo' => $existeUsuario['usuario_correo'],
                    'rol' => $existeUsuario['rol_nombre'] ?? 'USUARIO'
                ];

                try {
                    $querySession = "INSERT INTO guzman_sesiones_usuario 
                                   (sesion_usuario_id, sesion_fecha_inicio, sesion_fecha_expiracion, sesion_activa) 
                                   VALUES (" . $existeUsuario['usuario_id'] . ", NOW(), DATE_ADD(NOW(), INTERVAL 8 HOUR), 1)";
                    self::SQL($querySession);
                } catch (Exception $e) {
                    error_log("No se pudo registrar sesión: " . $e->getMessage());
                }

                try {
                    $queryActividad = "INSERT INTO guzman_historial_actividades 
                                     (historial_usuario_id, historial_tabla_afectada, historial_descripcion, historial_fecha) 
                                     VALUES (" . $existeUsuario['usuario_id'] . ", 'guzman_usuarios', 'Inicio de sesión exitoso', NOW())";
                    self::SQL($queryActividad);
                } catch (Exception $e) {
                    error_log("No se pudo registrar actividad: " . $e->getMessage());
                }

                echo json_encode([
                    'codigo' => 1,
                    'mensaje' => 'Inicio de sesión exitoso',
                    'usuario' => [
                        'nombre' => $existeUsuario['usuario_nombre'],
                        'apellido' => $existeUsuario['usuario_apellido'],
                        'rol' => $existeUsuario['rol_nombre'] ?? 'USUARIO'
                    ]
                ]);
            } else {
                echo json_encode([
                    'codigo' => 0,
                    'mensaje' => 'Credenciales incorrectas'
                ]);
            }

        } catch (Exception $e) {
            echo json_encode([
                'codigo' => 0,
                'mensaje' => 'Error al intentar iniciar sesión',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['usuario'])) {
            $usuarioId = $_SESSION['usuario']['id'];

            try {
                $querySession = "UPDATE guzman_sesiones_usuario 
                               SET sesion_activa = 0 
                               WHERE sesion_usuario_id = $usuarioId AND sesion_activa = 1";
                self::SQL($querySession);

                try {
                    $queryActividad = "INSERT INTO guzman_historial_actividades 
                                     (historial_usuario_id, historial_tabla_afectada, historial_descripcion, historial_fecha) 
                                     VALUES ($usuarioId, 'guzman_usuarios', 'Cierre de sesión', NOW())";
                    self::SQL($queryActividad);
                } catch (Exception $e) {
                    error_log("No se pudo registrar actividad de logout: " . $e->getMessage());
                }

            } catch (Exception $e) {
                error_log("Error al cerrar sesión: " . $e->getMessage());
            }
        }

        session_unset();
        session_destroy();

        header('Location: /guzman_final_armamento_ingSoft1/login');
        exit;
    }

    public static function dashboard(Router $router)
    {
        isAuth(); 
        
        $router->render('dashboard/index', []);
    }

    public static function renderInicio(Router $router)
    {
        isAuth();
        $router->render('pages/inicio', []);
    }
}