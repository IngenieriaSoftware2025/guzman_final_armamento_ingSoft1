document.addEventListener('DOMContentLoaded', function() {
    
    // Variables globales
    const modal = document.getElementById('modal-usuario');
    const btnNuevoUsuario = document.getElementById('btn-nuevo-usuario');
    const cerrarModal = document.getElementById('cerrar-modal');
    const formularioUsuario = document.getElementById('formulario-usuario');
    const btnCancelar = document.getElementById('btn-cancelar');
    const modalTitulo = document.getElementById('modal-titulo');
    const btnGuardar = document.getElementById('btn-guardar');
    const erroresContenedor = document.getElementById('errores-contenedor');
    const loading = document.getElementById('loading');
    
    // Elementos del formulario
    const usuarioId = document.getElementById('usuario_id');
    const usuarioNombre = document.getElementById('usuario_nombre');
    const usuarioApellido = document.getElementById('usuario_apellido');
    const usuarioDpi = document.getElementById('usuario_dpi');
    const usuarioCorreo = document.getElementById('usuario_correo');
    const usuarioContra = document.getElementById('usuario_contra');
    const confirmarContra = document.getElementById('confirmar_contra');
    const usuarioFotografia = document.getElementById('usuario_fotografia');
    const previewImagen = document.getElementById('preview-imagen');
    const imagenPreview = document.getElementById('imagen-preview');
    const fotoActualContenedor = document.getElementById('foto-actual-contenedor');
    const fotoActual = document.getElementById('foto-actual');
    const ayudaPassword = document.getElementById('ayuda-password');

    // Inicialización
    inicializar();

    function inicializar() {
        configurarEventListeners();
        configurarValidaciones();
        inicializarDataTable();
    }

    function configurarEventListeners() {
        btnNuevoUsuario.addEventListener('click', abrirModalNuevoUsuario);

        cerrarModal.addEventListener('click', cerrarModalUsuario);
        btnCancelar.addEventListener('click', cerrarModalUsuario);

        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                cerrarModalUsuario();
            }
        });

        formularioUsuario.addEventListener('submit', manejarSubmitFormulario);
        usuarioFotografia.addEventListener('change', previsualizarImagen);
        document.addEventListener('click', function(e) {
            if (e.target.closest('.btn-editar')) {
                const id = e.target.closest('.btn-editar').dataset.id;
                editarUsuario(id);
            }
            
            if (e.target.closest('.btn-eliminar')) {
                const id = e.target.closest('.btn-eliminar').dataset.id;
                const nombre = e.target.closest('.btn-eliminar').dataset.nombre;
                eliminarUsuario(id, nombre);
            }
        });
    }

    function configurarValidaciones() {
        usuarioDpi.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        confirmarContra.addEventListener('input', validarConfirmacionPassword);
        usuarioContra.addEventListener('input', validarConfirmacionPassword);
    }

    function validarConfirmacionPassword() {
        if (usuarioContra.value && confirmarContra.value) {
            if (usuarioContra.value !== confirmarContra.value) {
                confirmarContra.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirmarContra.setCustomValidity('');
            }
        }
    }

    function inicializarDataTable() {
        if (typeof DataTable !== 'undefined') {
            new DataTable('#tabla-usuarios', {
                language: {
                    url: '/build/js/datatable-spanish.json'
                },
                responsive: true,
                pageLength: 25,
                order: [[0, 'desc']]
            });
        }
    }

    function abrirModalNuevoUsuario() {
        modalTitulo.textContent = 'Nuevo Usuario';
        btnGuardar.textContent = 'Crear Usuario';
        limpiarFormulario();
        usuarioContra.required = true;
        confirmarContra.required = true;
        ayudaPassword.textContent = 'Mínimo 6 caracteres';
        fotoActualContenedor.style.display = 'none';
        modal.style.display = 'block';
        usuarioNombre.focus();
    }

    function abrirModalEditarUsuario(usuario) {
        modalTitulo.textContent = 'Editar Usuario';
        btnGuardar.textContent = 'Actualizar Usuario';

        usuarioId.value = usuario.usuario_id;
        usuarioNombre.value = usuario.usuario_nombre;
        usuarioApellido.value = usuario.usuario_apellido;
        usuarioDpi.value = usuario.usuario_dpi;
        usuarioCorreo.value = usuario.usuario_correo;
        usuarioContra.required = false;
        confirmarContra.required = false;
        usuarioContra.value = '';
        confirmarContra.value = '';
        ayudaPassword.textContent = 'Deje en blanco para mantener la actual';
        
        if (usuario.usuario_fotografia) {
            fotoActual.src = `/imagenes/usuarios/${usuario.usuario_fotografia}`;
            fotoActualContenedor.style.display = 'block';
        } else {
            fotoActualContenedor.style.display = 'none';
        }
        
        modal.style.display = 'block';
        usuarioNombre.focus();
    }

    function cerrarModalUsuario() {
        modal.style.display = 'none';
        limpiarFormulario();
        limpiarErrores();
    }

    function limpiarFormulario() {
        formularioUsuario.reset();
        usuarioId.value = '';
        previewImagen.style.display = 'none';
        usuarioContra.setCustomValidity('');
        confirmarContra.setCustomValidity('');
    }

    function limpiarErrores() {
        erroresContenedor.innerHTML = '';
    }

    function mostrarErrores(errores) {
        erroresContenedor.innerHTML = '';
        
        if (Array.isArray(errores)) {
            errores.forEach(error => {
                const div = document.createElement('div');
                div.className = 'alerta alerta-error';
                div.textContent = error;
                erroresContenedor.appendChild(div);
            });
        } else {
            const div = document.createElement('div');
            div.className = 'alerta alerta-error';
            div.textContent = errores;
            erroresContenedor.appendChild(div);
        }
    }

    function mostrarExito(mensaje) {
        const div = document.createElement('div');
        div.className = 'alerta alerta-exito';
        div.textContent = mensaje;
        erroresContenedor.appendChild(div);
        
        setTimeout(() => {
            div.remove();
        }, 3000);
    }

    function previsualizarImagen() {
        const archivo = usuarioFotografia.files[0];
        
        if (archivo) {
            // Validar tipo de archivo
            const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!tiposPermitidos.includes(archivo.type)) {
                mostrarErrores('El archivo debe ser una imagen válida (JPG, PNG, GIF)');
                usuarioFotografia.value = '';
                previewImagen.style.display = 'none';
                return;
            }
            
            // Validar tamaño (5MB)
            if (archivo.size > 5 * 1024 * 1024) {
                mostrarErrores('La imagen no puede pesar más de 5MB');
                usuarioFotografia.value = '';
                previewImagen.style.display = 'none';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                imagenPreview.src = e.target.result;
                previewImagen.style.display = 'block';
            };
            reader.readAsDataURL(archivo);
        } else {
            previewImagen.style.display = 'none';
        }
    }

    function manejarSubmitFormulario(e) {
        e.preventDefault();
        
        limpiarErrores();
        
        // Validar formulario
        if (!formularioUsuario.checkValidity()) {
            formularioUsuario.reportValidity();
            return;
        }
        
        const formData = new FormData(formularioUsuario);
        const esEdicion = usuarioId.value !== '';
        const url = esEdicion ? '/api/usuarios/actualizar' : '/api/usuarios/crear';
        
        mostrarLoading(true);
        btnGuardar.disabled = true;
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarExito(data.message || 'Usuario guardado correctamente');
                cerrarModalUsuario();
                recargarTablaUsuarios();
            } else {
                mostrarErrores(data.errors || data.message || 'Error al guardar usuario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarErrores('Error de conexión. Intente nuevamente.');
        })
        .finally(() => {
            mostrarLoading(false);
            btnGuardar.disabled = false;
        });
    }

    function editarUsuario(id) {
        mostrarLoading(true);
        
        fetch(`/api/usuarios/obtener/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                abrirModalEditarUsuario(data.usuario);
            } else {
                mostrarErrores(data.message || 'Error al cargar usuario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarErrores('Error de conexión. Intente nuevamente.');
        })
        .finally(() => {
            mostrarLoading(false);
        });
    }

    function eliminarUsuario(id, nombre) {
        if (!confirm(`¿Confirma eliminar al usuario ${nombre}?`)) {
            return;
        }
        
        mostrarLoading(true);
        
        fetch('/api/usuarios/eliminar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: id })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarExito(data.message || 'Usuario eliminado correctamente');
                recargarTablaUsuarios();
            } else {
                mostrarErrores(data.message || 'Error al eliminar usuario');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarErrores('Error de conexión. Intente nuevamente.');
        })
        .finally(() => {
            mostrarLoading(false);
        });
    }

    function recargarTablaUsuarios() {
        const tbody = document.getElementById('usuarios-tbody');
        mostrarLoading(true);
        
        fetch('/api/usuarios/listar')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                actualizarTablaUsuarios(data.usuarios);
            } else {
                console.error('Error al recargar usuarios:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            mostrarLoading(false);
        });
    }

    function actualizarTablaUsuarios(usuarios) {
        const tbody = document.getElementById('usuarios-tbody');
        tbody.innerHTML = '';
        
        if (usuarios.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" class="no-usuarios">No hay usuarios registrados</td></tr>';
            return;
        }
        
        usuarios.forEach(usuario => {
            const fila = crearFilaUsuario(usuario);
            tbody.appendChild(fila);
        });
    }

    function crearFilaUsuario(usuario) {
        const tr = document.createElement('tr');
        tr.dataset.id = usuario.usuario_id;
        
        const fotografia = usuario.usuario_fotografia 
            ? `<img src="/imagenes/usuarios/${usuario.usuario_fotografia}" alt="Foto de ${usuario.usuario_nombre}" class="foto-usuario-tabla">`
            : '<div class="sin-foto"><i class="fas fa-user"></i></div>';
        
        const estadoClase = usuario.usuario_situacion === '1' ? 'estado-activo' : 'estado-inactivo';
        const estadoTexto = usuario.usuario_situacion === '1' ? 'Activo' : 'Inactivo';
        
        tr.innerHTML = `
            <td>${usuario.usuario_id}</td>
            <td>${fotografia}</td>
            <td>${usuario.usuario_nombre}</td>
            <td>${usuario.usuario_apellido}</td>
            <td>${usuario.usuario_dpi}</td>
            <td>${usuario.usuario_correo}</td>
            <td>${usuario.usuario_fecha_creacion}</td>
            <td><span class="estado ${estadoClase}">${estadoTexto}</span></td>
            <td class="acciones">
                <button type="button" class="boton boton-azul boton-small btn-editar" data-id="${usuario.usuario_id}">
                    <i class="fas fa-edit"></i>
                </button>
                <button type="button" class="boton boton-rojo boton-small btn-eliminar" 
                        data-id="${usuario.usuario_id}" 
                        data-nombre="${usuario.usuario_nombre} ${usuario.usuario_apellido}">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        
        return tr;
    }

    function mostrarLoading(mostrar) {
        if (mostrar) {
            loading.style.display = 'block';
        } else {
            loading.style.display = 'none';
        }
    }

});