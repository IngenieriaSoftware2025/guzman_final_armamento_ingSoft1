create table guzman_usuarios(
usuario_id SERIAL PRIMARY KEY,
usuario_nombre VARCHAR (50) NOT NULL,
usuario_apellido VARCHAR (50) NOT NULL,
usuario_dpi VARCHAR (13) NOT NULL,
usuario_correo VARCHAR (100) NOT NULL,
usuario_contra LVARCHAR (1056) NOT NULL,
usuario_fecha_creacion DATE DEFAULT TODAY,
usuario_fotografia LVARCHAR (2056),
usuario_situacion SMALLINT DEFAULT 1
);

INSERT INTO guzman_usuarios (usuario_nombre, usuario_apellido, usuario_dpi, usuario_correo, usuario_contra) 
VALUES ('Herberth', 'Guzman', '3002458740101', 'herberthguzman0@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
--contraseña: password

CREATE TABLE guzman_roles (
rol_id SERIAL PRIMARY KEY,
rol_nombre VARCHAR(50) NOT NULL UNIQUE,
rol_descripcion VARCHAR(200),
rol_situacion SMALLINT DEFAULT 1
);

INSERT INTO guzman_roles (rol_nombre, rol_descripcion) VALUES('OFICIAL', 'Acceso total al sistema');
INSERT INTO guzman_roles (rol_nombre, rol_descripcion) VALUES('SARGENTO', 'Acceso de lectura y escritura limitada');
INSERT INTO guzman_roles (rol_nombre, rol_descripcion) VALUES('SOLDADO', 'Acceso de solo lectura');

CREATE TABLE guzman_permisos_roles (
permiso_id SERIAL PRIMARY KEY,
permiso_usuario INTEGER NOT NULL,
permiso_rol INTEGER NOT NULL,
permiso_situacion SMALLINT DEFAULT 1,
FOREIGN KEY (permiso_usuario) REFERENCES guzman_usuarios(usuario_id),
FOREIGN KEY (permiso_rol) REFERENCES guzman_roles(rol_id)
);

INSERT INTO guzman_permisos_roles (permiso_usuario, permiso_rol) VALUES (1, 1);



