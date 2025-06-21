<?php

function verificarPermisoAplicacion($aplicacion, $nivelRequerido = 'LECTURA') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['usuario'])) {
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            http_response_code(401);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Sesión no válida']);
            exit;
        } else {
            header('Location: /guzman_final_armamento_ingSoft1/login');
            exit;
        }
    }

    $usuarioId = $_SESSION['usuario']['id'];
    if ($usuarioId == 1) {
    return true;
}
    
    try {
        global $db;
        if (!$db) {
            include_once __DIR__ . '/database.php';
        }
        
        if (!$db) {
            error_log("Error: Conexión de base de datos no disponible");
            header('Location: /guzman_final_armamento_ingSoft1/dashboard?error=conexion');
            exit;
        }
        
        $query = "SELECT pa.permiso_nivel, a.app_nombre 
                 FROM guzman_permisos_aplicaciones pa
                 INNER JOIN guzman_aplicaciones a ON pa.permiso_aplicacion = a.app_id
                 WHERE pa.permiso_usuario = $usuarioId 
                 AND a.app_nombre = '$aplicacion' 
                 AND pa.permiso_situacion = 1";
        
        $resultado = $db->query($query);
        $permiso = $resultado->fetch(PDO::FETCH_ASSOC);
        
        if (!$permiso) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                http_response_code(403);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Sin permisos para esta aplicación']);
                exit;
            } else {
                header('Location: /guzman_final_armamento_ingSoft1/dashboard?error=sin_permisos');
                exit;
            }
        }

        $nivelesPermiso = ['LECTURA' => 1, 'ESCRITURA' => 2, 'TOTAL' => 3];
        $nivelUsuario = $nivelesPermiso[$permiso['permiso_nivel']] ?? 0;
        $nivelNecesario = $nivelesPermiso[$nivelRequerido] ?? 1;

        if ($nivelUsuario < $nivelNecesario) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                http_response_code(403);
                echo json_encode(['codigo' => 0, 'mensaje' => 'Nivel de permisos insuficiente']);
                exit;
            } else {
                header('Location: /guzman_final_armamento_ingSoft1/dashboard?error=permisos_insuficientes');
                exit;
            }
        }

        return true;

    } catch (Exception $e) {
        error_log("Error verificando permisos: " . $e->getMessage());
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            http_response_code(500);
            echo json_encode(['codigo' => 0, 'mensaje' => 'Error del sistema']);
            exit;
        } else {
            header('Location: /guzman_final_armamento_ingSoft1/dashboard?error=sistema');
            exit;
        }
    }
}

function obtenerPermisosUsuario($usuarioId) {
    try {
        global $db;
        if (!$db) {
            include_once __DIR__ . '/database.php';
        }
        
        $query = "SELECT a.app_nombre, pa.permiso_nivel
                 FROM guzman_permisos_aplicaciones pa
                 INNER JOIN guzman_aplicaciones a ON pa.permiso_aplicacion = a.app_id
                 WHERE pa.permiso_usuario = $usuarioId 
                 AND pa.permiso_situacion = 1";
        
        $resultado = $db->query($query);
        $permisos = $resultado->fetchAll(PDO::FETCH_ASSOC);
        
        $permisosArray = [];
        foreach ($permisos as $permiso) {
            $permisosArray[$permiso['app_nombre']] = $permiso['permiso_nivel'];
        }
        
        return $permisosArray;

    } catch (Exception $e) {
        error_log("Error obteniendo permisos: " . $e->getMessage());
        return [];
    }
}
?>