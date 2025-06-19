<?php

function debuguear($variable) {
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

// Escapa / Sanitizar el HTML
function s($html) {
    $s = htmlspecialchars($html);
    return $s;
}

/**
 * Función que revisa que el usuario este autenticado
 */
function isAuth() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['usuario'])) {
        header('Location: /guzman_final_armamento_ingSoft1/login');
        exit;
    }
    
    return true;
}

/**
 * Verificar autenticación para APIs
 */
function isAuthApi() {
    getHeadersApi();
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401);
        echo json_encode([    
            "mensaje" => "No está autenticado",
            "codigo" => 4,
        ]);
        exit;
    }
    
    return true;
}

/**
 * Verificar que el usuario NO esté autenticado (para páginas de login)
 */
function isNotAuth() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (isset($_SESSION['usuario'])) {
        header('Location: /guzman_final_armamento_ingSoft1/dashboard');
        exit;
    }
}

/**
 * Verificar permisos del usuario
 */
function hasPermission(array $permisos = []) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['usuario'])) {
        header('Location: /guzman_final_armamento_ingSoft1/login');
        exit;
    }
    
    // Si no se especifican permisos, solo verificar autenticación
    if (empty($permisos)) {
        return true;
    }
    
    $usuario_rol = $_SESSION['usuario']['rol'] ?? '';
    
    if (in_array($usuario_rol, $permisos)) {
        return true;
    } else {
        header('Location: /guzman_final_armamento_ingSoft1/');
        exit;
    }
}

/**
 * Verificar permisos para APIs
 */
function hasPermissionApi(array $permisos = []) {
    getHeadersApi();
    
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['usuario'])) {
        http_response_code(401);
        echo json_encode([     
            "mensaje" => "No está autenticado",
            "codigo" => 4,
        ]);
        exit;
    }
    
    // Si no se especifican permisos, solo verificar autenticación
    if (empty($permisos)) {
        return true;
    }
    
    $usuario_rol = $_SESSION['usuario']['rol'] ?? '';
    
    if (!in_array($usuario_rol, $permisos)) {
        http_response_code(403);
        echo json_encode([     
            "mensaje" => "No tiene permisos suficientes",
            "codigo" => 4,
        ]);
        exit;
    }
    
    return true;
}

/**
 * Obtener información del usuario autenticado
 */
function getUsuarioActual() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    return $_SESSION['usuario'] ?? null;
}

/**
 * Verificar si el usuario tiene un rol específico
 */
function tieneRol($rol) {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $usuario_rol = $_SESSION['usuario']['rol'] ?? '';
    return $usuario_rol === $rol;
}

/**
 * Headers para APIs
 */
function getHeadersApi(){
    header("Content-type: application/json; charset=utf-8");
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
}

/**
 * Función para generar URLs de assets
 */
function asset($ruta){
    return "/". $_ENV['APP_NAME']."/public/" . $ruta;
}