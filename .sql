CREATE TABLE guzman_usuarios (
    usuario_id SERIAL PRIMARY KEY,
    usuario_nombre VARCHAR(50) NOT NULL,
    usuario_apellido VARCHAR(50) NOT NULL,
    usuario_dpi VARCHAR(15) NOT NULL UNIQUE,
    usuario_correo VARCHAR(100) NOT NULL UNIQUE,
    usuario_contra LVARCHAR(1056) NOT NULL,
    usuario_fotografia LVARCHAR(2056),
    usuario_situacion SMALLINT DEFAULT 1
);

CREATE TABLE guzman_roles (
    rol_id SERIAL PRIMARY KEY,
    rol_nombre VARCHAR(50),
    rol_descripcion VARCHAR(200),
    rol_situacion SMALLINT DEFAULT 1
);

INSERT INTO guzman_roles (rol_nombre, rol_descripcion) VALUES ('ADMINISTRADOR', 'Control total del sistema de armamento');
INSERT INTO guzman_roles (rol_nombre, rol_descripcion) VALUES ('SUPERVISOR', 'Supervisión y asignación de armamento');
INSERT INTO guzman_roles (rol_nombre, rol_descripcion) VALUES ('OPERADOR', 'Registro y consulta de armamento');

CREATE TABLE guzman_permisos_roles (
    permiso_id SERIAL PRIMARY KEY,
    permiso_usuario INTEGER NOT NULL,
    permiso_rol INTEGER NOT NULL,
    permiso_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (permiso_usuario) REFERENCES guzman_usuarios(usuario_id),
    FOREIGN KEY (permiso_rol) REFERENCES guzman_roles(rol_id)
);

CREATE TABLE guzman_aplicaciones (
    app_id SERIAL PRIMARY KEY,
    app_nombre VARCHAR(100) NOT NULL,
    app_descripcion VARCHAR(200),
    app_situacion SMALLINT DEFAULT 1
);

INSERT INTO guzman_aplicaciones (app_nombre, app_descripcion) VALUES ('ARMAMENTO', 'Gestión de armamento y equipo');
INSERT INTO guzman_aplicaciones (app_nombre, app_descripcion) VALUES ('PERSONAL', 'Control de personal militar');
INSERT INTO guzman_aplicaciones (app_nombre, app_descripcion) VALUES ('REPORTES', 'Reportes y estadísticas');
INSERT INTO guzman_aplicaciones (app_nombre, app_descripcion) VALUES ('CONFIGURACION', 'Configuración del sistema');

CREATE TABLE guzman_tipos_armamento (
    tipo_id SERIAL PRIMARY KEY,
    tipo_nombre VARCHAR(100) NOT NULL UNIQUE,
    tipo_descripcion VARCHAR(250),
    tipo_categoria VARCHAR(50), 
    tipo_situacion SMALLINT DEFAULT 1
);

INSERT INTO guzman_tipos_armamento (tipo_nombre, tipo_descripcion, tipo_categoria) VALUES ('PISTOLA', 'Arma de fuego corta', 'ARMA_CORTA');
INSERT INTO guzman_tipos_armamento (tipo_nombre, tipo_descripcion, tipo_categoria) VALUES ('FUSIL', 'Arma de fuego larga', 'ARMA_LARGA');
INSERT INTO guzman_tipos_armamento (tipo_nombre, tipo_descripcion, tipo_categoria) VALUES ('AMETRALLADORA', 'Arma automática', 'ARMA_PESADA');
INSERT INTO guzman_tipos_armamento (tipo_nombre, tipo_descripcion, tipo_categoria) VALUES ('GRANADA', 'Explosivo de mano', 'EXPLOSIVO');

CREATE TABLE guzman_calibres (
    calibre_id SERIAL PRIMARY KEY,
    calibre_nombre VARCHAR(50) NOT NULL UNIQUE,
    calibre_descripcion VARCHAR(100),
    calibre_situacion SMALLINT DEFAULT 1
);

INSERT INTO guzman_calibres (calibre_nombre, calibre_descripcion) VALUES ('9mm', 'Calibre 9 milímetros');
INSERT INTO guzman_calibres (calibre_nombre, calibre_descripcion) VALUES('5.56mm', 'Calibre 5.56 milímetros ');
INSERT INTO guzman_calibres (calibre_nombre, calibre_descripcion) VALUES('7.62mm', 'Calibre 7.62 milímetros');
INSERT INTO guzman_calibres (calibre_nombre, calibre_descripcion) VALUES('.45 ACP', 'Calibre .45');

CREATE TABLE guzman_personal (
    personal_id SERIAL PRIMARY KEY,
    personal_nombres VARCHAR(100) NOT NULL,
    personal_apellidos VARCHAR(100) NOT NULL,
    personal_grado VARCHAR(50),
    personal_unidad VARCHAR(100),
    personal_dpi VARCHAR(13) UNIQUE,
    personal_situacion SMALLINT DEFAULT 1
);

CREATE TABLE guzman_almacenes (
    almacen_id SERIAL PRIMARY KEY,
    almacen_nombre VARCHAR(100) NOT NULL,
    almacen_ubicacion VARCHAR(200),
    almacen_responsable INTEGER,
    almacen_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (almacen_responsable) REFERENCES guzman_personal(personal_id)
);

CREATE TABLE guzman_armamento (
    arma_id SERIAL PRIMARY KEY,
    arma_numero_serie VARCHAR(100) NOT NULL UNIQUE,
    arma_tipo INTEGER NOT NULL,
    arma_calibre INTEGER NOT NULL,
    arma_estado VARCHAR(30) DEFAULT 'BUEN_ESTADO', 
    arma_fecha_ingreso DATE DEFAULT TODAY,
    arma_almacen INTEGER NOT NULL,
    arma_observaciones LVARCHAR(500),
    arma_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (arma_tipo) REFERENCES guzman_tipos_armamento(tipo_id),
    FOREIGN KEY (arma_calibre) REFERENCES guzman_calibres(calibre_id),
    FOREIGN KEY (arma_almacen) REFERENCES guzman_almacenes(almacen_id)
);


CREATE TABLE guzman_asignaciones_armamento (
    asignacion_id SERIAL PRIMARY KEY,
    asignacion_arma INTEGER NOT NULL,
    asignacion_personal INTEGER NOT NULL,
    asignacion_fecha_asignacion DATE DEFAULT TODAY,
    asignacion_fecha_devolucion DATE,
    asignacion_motivo VARCHAR(200),
    asignacion_estado VARCHAR(20) DEFAULT 'ASIGNADO',
    asignacion_usuario INTEGER NOT NULL,
    asignacion_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (asignacion_arma) REFERENCES guzman_armamento(arma_id),
    FOREIGN KEY (asignacion_personal) REFERENCES guzman_personal(personal_id),
    FOREIGN KEY (asignacion_usuario) REFERENCES guzman_usuarios(usuario_id)
);


CREATE TABLE guzman_historial_actividades (
    historial_id SERIAL PRIMARY KEY,
    historial_usuario_id INTEGER NOT NULL,
    historial_tabla_afectada VARCHAR(50),
    historial_registro_id INTEGER,
    historial_descripcion LVARCHAR(500),
    historial_fecha DATETIME YEAR TO MINUTE,
    historial_situacion SMALLINT DEFAULT 1,
    FOREIGN KEY (historial_usuario_id) REFERENCES guzman_usuarios(usuario_id)
);

CREATE TABLE guzman_sesiones_usuario (
    sesion_id SERIAL PRIMARY KEY,
    sesion_usuario_id INTEGER NOT NULL,
    sesion_fecha_inicio DATETIME YEAR TO MINUTE,
    sesion_fecha_expiracion DATETIME YEAR TO MINUTE,
    sesion_activa SMALLINT DEFAULT 1,
    FOREIGN KEY (sesion_usuario_id) REFERENCES guzman_usuarios(usuario_id)
);

INSERT INTO guzman_usuarios (usuario_nombre, usuario_apellido, usuario_dpi, usuario_correo, usuario_contra) VALUES ('Herberth', 'Guzman', '3002458740101', 'herberth@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT INTO guzman_usuarios (usuario_nombre, usuario_apellido, usuario_dpi, usuario_correo, usuario_contra) VALUES ('Andrea', 'Masella', '3002458740102', 'herberth1@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
INSERT INTO guzman_usuarios (usuario_nombre, usuario_apellido, usuario_dpi, usuario_correo, usuario_contra) VALUES ('Javier', 'Jimenez', '3002458740103', 'herberth2@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Contraseña: password

INSERT INTO guzman_permisos_roles (permiso_usuario, permiso_rol) VALUES (1, 1);
INSERT INTO guzman_permisos_roles (permiso_usuario, permiso_rol) VALUES (2, 2);
INSERT INTO guzman_permisos_roles (permiso_usuario, permiso_rol) VALUES (3, 3);

INSERT INTO guzman_almacenes (almacen_nombre, almacen_ubicacion) VALUES ('Almacén General', 'Mariscal Zavala, Guatemala');

INSERT INTO personal (personal_nombres, personal_apellidos, personal_grado, personal_unidad, personal_dpi) VALUES 
('Juan Carlos', 'Pérez García', 'Teniente', 'Comando de Informática', '1234567890102'),
('María Elena', 'López Morales', 'Subteniente', 'Comando de Informática', '1234567890103'),
('Roberto', 'Martínez Cruz', 'Sargento', 'Comando de Informática', '1234567890104');
