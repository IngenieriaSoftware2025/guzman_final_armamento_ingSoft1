<div class="contenedor-app">
    <div class="contenedor-superior">
        <h1>Gestión de Usuarios</h1>
        <button type="button" class="boton boton-verde" id="btn-nuevo-usuario">
            <i class="fas fa-plus"></i> Nuevo Usuario
        </button>
    </div>

    <!-- Modal para crear/editar usuario -->
    <div id="modal-usuario" class="modal" style="display: none;">
        <div class="modal-contenido">
            <div class="modal-header">
                <h2 id="modal-titulo">Nuevo Usuario</h2>
                <span class="cerrar-modal" id="cerrar-modal">&times;</span>
            </div>
            
            <div class="modal-body">
                <div id="errores-contenedor"></div>
                
                <form id="formulario-usuario" enctype="multipart/form-data">
                    <input type="hidden" id="usuario_id" name="usuario_id">
                    
                    <fieldset>
                        <legend>Información Personal</legend>
                        
                        <div class="campo">
                            <label for="usuario_nombre">Nombre:</label>
                            <input 
                                type="text" 
                                id="usuario_nombre" 
                                name="usuario_nombre" 
                                placeholder="Ingrese el nombre"
                                required
                            >
                        </div>

                        <div class="campo">
                            <label for="usuario_apellido">Apellido:</label>
                            <input 
                                type="text" 
                                id="usuario_apellido" 
                                name="usuario_apellido" 
                                placeholder="Ingrese el apellido"
                                required
                            >
                        </div>

                        <div class="campo">
                            <label for="usuario_dpi">DPI:</label>
                            <input 
                                type="text" 
                                id="usuario_dpi" 
                                name="usuario_dpi" 
                                placeholder="Ingrese el DPI (13 dígitos)"
                                maxlength="13"
                                pattern="[0-9]{13}"
                                required
                            >
                        </div>

                        <div class="campo">
                            <label for="usuario_correo">Correo Electrónico:</label>
                            <input 
                                type="email" 
                                id="usuario_correo" 
                                name="usuario_correo" 
                                placeholder="usuario@correo.com"
                                required
                            >
                        </div>

                        <div class="campo">
                            <label for="usuario_contra">Contraseña:</label>
                            <input 
                                type="password" 
                                id="usuario_contra" 
                                name="usuario_contra" 
                                placeholder="Mínimo 6 caracteres"
                                minlength="6"
                                required
                            >
                            <small class="texto-ayuda" id="ayuda-password">
                                Mínimo 6 caracteres
                            </small>
                        </div>

                        <div class="campo">
                            <label for="confirmar_contra">Confirmar Contraseña:</label>
                            <input 
                                type="password" 
                                id="confirmar_contra" 
                                name="confirmar_contra" 
                                placeholder="Confirme la contraseña"
                                minlength="6"
                                required
                            >
                        </div>
                    </fieldset>

                    <fieldset>
                        <legend>Fotografía del Usuario</legend>
                        
                        <div class="foto-actual" id="foto-actual-contenedor" style="display: none;">
                            <label>Fotografía Actual:</label>
                            <img id="foto-actual" src="" alt="Foto actual" class="foto-usuario-actual">
                        </div>
                        
                        <div class="campo">
                            <label for="usuario_fotografia">Fotografía:</label>
                            <input 
                                type="file" 
                                id="usuario_fotografia" 
                                name="usuario_fotografia" 
                                accept="image/*"
                            >
                            <small class="texto-ayuda">
                                Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB
                            </small>
                        </div>

                        <div class="preview-imagen" id="preview-imagen" style="display: none;">
                            <img id="imagen-preview" src="" alt="Vista previa">
                        </div>
                    </fieldset>

                    <div class="acciones-formulario">
                        <button type="submit" id="btn-guardar" class="boton boton-verde">
                            Guardar Usuario
                        </button>
                        <button type="button" id="btn-cancelar" class="boton boton-gris">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de usuarios -->
    <div class="contenedor-listado">
        <div id="loading" class="loading" style="display: none;">
            <i class="fas fa-spinner fa-spin"></i> Cargando...
        </div>
        
        <div id="usuarios-contenedor">
            <table class="tabla-usuarios" id="tabla-usuarios">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fotografía</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>DPI</th>
                        <th>Correo</th>
                        <th>Fecha Creación</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="usuarios-tbody">
                    <?php if (!empty($usuarios)): ?>
                        <?php foreach ($usuarios as $usuario): ?>
                            <tr data-id="<?php echo $usuario->usuario_id; ?>">
                                <td><?php echo $usuario->usuario_id; ?></td>
                                <td>
                                    <?php if ($usuario->usuario_fotografia): ?>
                                        <img src="/imagenes/usuarios/<?php echo $usuario->usuario_fotografia; ?>" 
                                             alt="Foto de <?php echo $usuario->usuario_nombre; ?>" 
                                             class="foto-usuario-tabla">
                                    <?php else: ?>
                                        <div class="sin-foto">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo $usuario->usuario_nombre; ?></td>
                                <td><?php echo $usuario->usuario_apellido; ?></td>
                                <td><?php echo $usuario->usuario_dpi; ?></td>
                                <td><?php echo $usuario->usuario_correo; ?></td>
                                <td><?php echo $usuario->usuario_fecha_creacion; ?></td>
                                <td>
                                    <span class="estado <?php echo $usuario->usuario_situacion === '1' ? 'estado-activo' : 'estado-inactivo'; ?>">
                                        <?php echo $usuario->usuario_situacion === '1' ? 'Activo' : 'Inactivo'; ?>
                                    </span>
                                </td>
                                <td class="acciones">
                                    <button type="button" 
                                            class="boton boton-azul boton-small btn-editar" 
                                            data-id="<?php echo $usuario->usuario_id; ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <button type="button" 
                                            class="boton boton-rojo boton-small btn-eliminar" 
                                            data-id="<?php echo $usuario->usuario_id; ?>"
                                            data-nombre="<?php echo $usuario->usuario_nombre . ' ' . $usuario->usuario_apellido; ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="no-usuarios">No hay usuarios registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="/build/js/usuarios/index.js"></script>