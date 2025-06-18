import { Toast, validarFormulario } from "../funciones";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import Swal from "sweetalert2";

// Elementos del DOM
const FormUsuarios = document.getElementById("formUsuario");
const BtnGuardar = document.getElementById("BtnGuardar");
const BtnModificar = document.getElementById("BtnModificar");
const BtnLimpiar = document.getElementById("BtnLimpiar");
const BtnBuscarUsuarios = document.getElementById("BtnBuscarUsuarios");
const SeccionTabla = document.getElementById("seccionTabla");
const TableUsuarios = document.getElementById("TableUsuarios");
const togglePassword = document.getElementById("togglePassword");
const inputFotografia = document.getElementById("usuario_fotografia");
const previewFotografia = document.getElementById("previewFotografia");

let TablaUsuarios;
let usuarioEnEdicion = false;

// Event Listeners
document.addEventListener('DOMContentLoaded', function() {
    inicializarEventos();
});

function inicializarEventos() {
    // Eventos del formulario
    FormUsuarios.addEventListener('submit', guardarUsuario);
    BtnModificar.addEventListener('click', modificarUsuario);
    BtnLimpiar.addEventListener('click', limpiarFormulario);
    BtnBuscarUsuarios.addEventListener('click', buscarUsuarios);
    
    // Toggle contraseña
    if (togglePassword) {
        togglePassword.addEventListener('click', togglePasswordVisibility);
    }
    
    // Preview de fotografía
    if (inputFotografia) {
        inputFotografia.addEventListener('change', previewFoto);
    }
    
    // Validación de DPI en tiempo real
    const inputDPI = document.getElementById('usuario_dpi');
    if (inputDPI) {
        inputDPI.addEventListener('input', validarDPI);
        inputDPI.addEventListener('keypress', soloNumeros);
    }
}

// FUNCIÓN PARA GUARDAR USUARIO
const guardarUsuario = async (e) => {
    e.preventDefault();

    if (!validarFormulario(FormUsuarios, ['usuario_id'])) {
        Toast.fire({
            icon: "error",
            title: "Complete todos los campos requeridos",
        });
        return;
    }

    // Validar DPI
    const dpi = document.getElementById('usuario_dpi').value;
    if (dpi.length !== 13) {
        Toast.fire({
            icon: "error",
            title: "El DPI debe tener exactamente 13 dígitos",
        });
        return;
    }

    BtnGuardar.disabled = true;
    BtnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Guardando...';

    try {
        const formData = new FormData(FormUsuarios);
        const url = "/guzman_final_armamento_ingSof1/usuarios/guardarAPI";

        const config = {
            method: "POST",
            body: formData,
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Toast.fire({
                icon: "success",
                title: datos.mensaje,
            });

            limpiarFormulario();
            if (TablaUsuarios) {
                buscarUsuarios();
            }
        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje,
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error de conexión",
        });
    } finally {
        BtnGuardar.disabled = false;
        BtnGuardar.innerHTML = '<i class="bi bi-save me-2"></i>Guardar';
    }
};

// FUNCIÓN PARA BUSCAR USUARIOS
const buscarUsuarios = async () => {
    BtnBuscarUsuarios.disabled = true;
    BtnBuscarUsuarios.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Buscando...';

    try {
        const url = "/guzman_final_armamento_ingSof1/usuarios/buscarAPI";
        const config = {
            method: "GET",
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            SeccionTabla.style.display = "block";
            llenarTablaUsuarios(datos.data);
            
            Toast.fire({
                icon: "success",
                title: `${datos.data.length} usuarios encontrados`,
            });
        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje,
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error al buscar usuarios",
        });
    } finally {
        BtnBuscarUsuarios.disabled = false;
        BtnBuscarUsuarios.innerHTML = '<i class="bi bi-search me-2"></i>Buscar Usuarios';
    }
};

// FUNCIÓN PARA LLENAR TABLA DE USUARIOS
const llenarTablaUsuarios = (usuarios) => {
    if (TablaUsuarios) {
        TablaUsuarios.destroy();
    }

    TablaUsuarios = new DataTable("#TableUsuarios", {
        data: usuarios,
        language: lenguaje,
        pageLength: 10,
        order: [[2, "asc"]], // Ordenar por nombre
        columns: [
            {
                title: "No.",
                data: null,
                width: "5%",
                render: (data, type, row, meta) => meta.row + 1
            },
            {
                title: "Fotografía",
                data: "usuario_fotografia",
                width: "10%",
                orderable: false,
                render: (data, type, row) => {
                    if (data) {
                        return `<div class="avatar-usuario">
                                   <img src="/${data}" 
                                        alt="Foto de ${row.usuario_nombre}" 
                                        class="img-thumbnail rounded-circle" 
                                        style="width: 50px; height: 50px; object-fit: cover;"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline-flex';">
                                   <div class="bg-secondary rounded-circle d-none align-items-center justify-content-center"
                                        style="width: 50px; height: 50px;">
                                       <i class="bi bi-person-fill text-white"></i>
                                   </div>
                               </div>`;
                    } else {
                        return `<div class="bg-secondary rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 50px; height: 50px;">
                                    <i class="bi bi-person-fill text-white"></i>
                                </div>`;
                    }
                }
            },
            {
                title: "Nombres",
                data: "usuario_nombre",
                width: "15%",
                render: (data) => `<strong>${data}</strong>`
            },
            {
                title: "Apellidos",
                data: "usuario_apellido",
                width: "15%"
            },
            {
                title: "DPI",
                data: "usuario_dpi",
                width: "12%",
                render: (data) => `<code class="bg-light p-1 rounded">${data}</code>`
            },
            {
                title: "Correo",
                data: "usuario_correo",
                width: "18%",
                render: (data) => `<a href="mailto:${data}" class="text-decoration-none">${data}</a>`
            },
            {
                title: "Fecha Registro",
                data: "usuario_fecha_creacion",
                width: "12%",
                render: (data) => {
                    if (data) {
                        const fecha = new Date(data);
                        return fecha.toLocaleDateString('es-GT');
                    }
                    return 'N/A';
                }
            },
            {
                title: "Situación",
                data: "usuario_situacion",
                width: "8%",
                render: (data) => {
                    if (data == 1) {
                        return '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activo</span>';
                    } else {
                        return '<span class="badge bg-secondary"><i class="bi bi-x-circle me-1"></i>Inactivo</span>';
                    }
                }
            },
            {
                title: "Acciones",
                data: "usuario_id",
                width: "15%",
                orderable: false,
                render: (data, type, row) => {
                    return `
                        <div class="btn-group" role="group">
                            <button class="btn btn-info btn-sm ver-usuario" 
                                    data-id="${data}" 
                                    title="Ver detalles">
                                <i class="bi bi-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm modificar-usuario" 
                                    data-id="${data}"
                                    data-nombre="${row.usuario_nombre}"
                                    data-apellido="${row.usuario_apellido}"
                                    data-dpi="${row.usuario_dpi}"
                                    data-correo="${row.usuario_correo}"
                                    data-situacion="${row.usuario_situacion}"
                                    data-fotografia="${row.usuario_fotografia || ''}"
                                    title="Editar">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-sm eliminar-usuario" 
                                    data-id="${data}"
                                    data-nombre="${row.usuario_nombre} ${row.usuario_apellido}"
                                    title="Eliminar">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    // Event listeners para botones de la tabla
    setTimeout(() => {
        document.querySelectorAll(".ver-usuario").forEach((btn) => {
            btn.addEventListener("click", verUsuario);
        });

        document.querySelectorAll(".modificar-usuario").forEach((btn) => {
            btn.addEventListener("click", llenarFormularioModificar);
        });

        document.querySelectorAll(".eliminar-usuario").forEach((btn) => {
            btn.addEventListener("click", confirmarEliminar);
        });
    }, 100);
};

// FUNCIÓN PARA VER DETALLES DEL USUARIO
const verUsuario = async (e) => {
    const id = e.currentTarget.dataset.id;
    
    try {
        const respuesta = await fetch(`/guzman_final_armamento_ingSof1/usuarios/obtenerUsuarioAPI?id=${id}`);
        const datos = await respuesta.json();
        
        if (datos.codigo === 1) {
            const usuario = datos.data;
            const contenido = `
                <div class="row">
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            ${usuario.usuario_fotografia ? 
                                `<img src="/${usuario.usuario_fotografia}" 
                                     alt="Foto de ${usuario.usuario_nombre}" 
                                     class="img-thumbnail rounded-circle" 
                                     style="width: 150px; height: 150px; object-fit: cover;">` :
                                `<div class="bg-light border rounded-circle d-inline-flex align-items-center justify-content-center"
                                     style="width: 150px; height: 150px;">
                                    <i class="bi bi-person-fill text-muted" style="font-size: 4rem;"></i>
                                </div>`
                            }
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h5><i class="bi bi-person-circle me-2"></i>${usuario.usuario_nombre} ${usuario.usuario_apellido}</h5>
                        <hr>
                        <p><strong><i class="bi bi-card-text me-2"></i>DPI:</strong> ${usuario.usuario_dpi}</p>
                        <p><strong><i class="bi bi-envelope me-2"></i>Email:</strong> 
                           <a href="mailto:${usuario.usuario_correo}">${usuario.usuario_correo}</a>
                        </p>
                        <p><strong><i class="bi bi-toggle-on me-2"></i>Estado:</strong> 
                            <span class="badge ${usuario.usuario_situacion == 1 ? 'bg-success' : 'bg-secondary'}">
                                ${usuario.usuario_situacion == 1 ? 'Activo' : 'Inactivo'}
                            </span>
                        </p>
                        <p><strong><i class="bi bi-calendar me-2"></i>Fecha de registro:</strong> 
                           ${new Date(usuario.usuario_fecha_creacion).toLocaleDateString('es-GT')}
                        </p>
                    </div>
                </div>
            `;
            
            document.getElementById('contenidoModalUsuario').innerHTML = contenido;
            const modal = new bootstrap.Modal(document.getElementById('modalVerUsuario'));
            modal.show();
        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje,
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error al obtener datos del usuario",
        });
    }
};

// FUNCIÓN PARA LLENAR FORMULARIO PARA MODIFICAR
const llenarFormularioModificar = (e) => {
    const datos = e.currentTarget.dataset;

    document.getElementById("usuario_id").value = datos.id;
    document.getElementById("usuario_nombre").value = datos.nombre;
    document.getElementById("usuario_apellido").value = datos.apellido;
    document.getElementById("usuario_dpi").value = datos.dpi;
    document.getElementById("usuario_correo").value = datos.correo;
    document.getElementById("usuario_situacion").value = datos.situacion;

    // Mostrar foto actual si existe
    if (datos.fotografia) {
        previewFotografia.innerHTML = `
            <img src="/${datos.fotografia}" 
                 alt="Foto actual" 
                 class="rounded-circle border"
                 style="width: 120px; height: 120px; object-fit: cover;">
        `;
    }

    // Limpiar campo de contraseña y hacerlo opcional
    document.getElementById("usuario_contra").value = "";
    document.getElementById("usuario_contra").required = false;

    // Cambiar botones
    BtnGuardar.classList.add("d-none");
    BtnModificar.classList.remove("d-none");
    
    usuarioEnEdicion = true;

    // Scroll al formulario
    window.scrollTo({ top: 0, behavior: "smooth" });
    
    Toast.fire({
        icon: "info",
        title: "Modo edición activado",
    });
};

// FUNCIÓN PARA MODIFICAR USUARIO
const modificarUsuario = async (e) => {
    e.preventDefault();

    if (!validarFormulario(FormUsuarios, ['usuario_contra'])) {
        Toast.fire({
            icon: "error",
            title: "Complete todos los campos requeridos",
        });
        return;
    }

    BtnModificar.disabled = true;
    BtnModificar.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Modificando...';

    try {
        const formData = new FormData(FormUsuarios);
        const url = "/guzman_final_armamento_ingSof1/usuarios/modificarAPI";

        const config = {
            method: "POST",
            body: formData,
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Toast.fire({
                icon: "success",
                title: datos.mensaje,
            });

            limpiarFormulario();
            buscarUsuarios();
        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje,
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error de conexión",
        });
    } finally {
        BtnModificar.disabled = false;
        BtnModificar.innerHTML = '<i class="bi bi-pencil-square me-2"></i>Modificar';
    }
};

// FUNCIÓN PARA CONFIRMAR ELIMINACIÓN
const confirmarEliminar = (e) => {
    const id = e.currentTarget.dataset.id;
    const nombre = e.currentTarget.dataset.nombre;

    document.getElementById('nombreUsuarioEliminar').textContent = nombre;
    
    const modal = new bootstrap.Modal(document.getElementById('modalEliminarUsuario'));
    modal.show();
    
    // Configurar evento del botón de confirmación
    document.getElementById('confirmarEliminarUsuario').onclick = () => eliminarUsuario(id, modal);
};

// FUNCIÓN PARA ELIMINAR USUARIO
const eliminarUsuario = async (id, modal) => {
    try {
        const url = `/guzman_final_armamento_ingSof1/usuarios/eliminarAPI?id=${id}`;
        const config = {
            method: "GET",
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Toast.fire({
                icon: "success",
                title: datos.mensaje,
            });

            modal.hide();
            buscarUsuarios();
        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje,
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error de conexión",
        });
    }
};

// FUNCIÓN PARA LIMPIAR FORMULARIO
const limpiarFormulario = () => {
    FormUsuarios.reset();
    document.getElementById("usuario_id").value = "";
    document.getElementById("usuario_contra").required = true;

    // Restaurar preview de fotografía
    previewFotografia.innerHTML = `
        <div class="bg-light border rounded-circle d-inline-flex align-items-center justify-content-center"
             style="width: 120px; height: 120px;">
            <i class="bi bi-person-fill text-muted" style="font-size: 3rem;"></i>
        </div>
    `;

    // Cambiar botones
    BtnGuardar.classList.remove("d-none");
    BtnModificar.classList.add("d-none");
    
    usuarioEnEdicion = false;
};

// FUNCIÓN PARA PREVIEW DE FOTOGRAFÍA
const previewFoto = (event) => {
    const file = event.target.files[0];
    
    if (file) {
        // Validar tipo de archivo
        const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!tiposPermitidos.includes(file.type)) {
            Toast.fire({
                icon: 'error',
                title: 'Solo se permiten archivos JPG, JPEG, PNG y GIF'
            });
            event.target.value = '';
            return;
        }

        // Validar tamaño (5MB máximo)
        const tamañoMaximo = 5 * 1024 * 1024;
        if (file.size > tamañoMaximo) {
            Toast.fire({
                icon: 'error',
                title: 'El archivo es demasiado grande. Máximo 5MB'
            });
            event.target.value = '';
            return;
        }

        // Mostrar preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewFotografia.innerHTML = `
                <img src="${e.target.result}" 
                     alt="Preview" 
                     class="rounded-circle border"
                     style="width: 120px; height: 120px; object-fit: cover;">
            `;
        };
        reader.readAsDataURL(file);
    }
};

// FUNCIÓN PARA TOGGLE DE CONTRASEÑA
const togglePasswordVisibility = () => {
    const input = document.getElementById('usuario_contra');
    const icon = togglePassword.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
};

// FUNCIÓN PARA VALIDAR DPI
const validarDPI = (event) => {
    const dpi = event.target.value;
    const input = event.target;
    
    if (dpi.length === 0) {
        input.classList.remove('is-valid', 'is-invalid');
        return;
    }
    
    if (dpi.length !== 13) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
    } else {
        input.classList.add('is-valid');
        input.classList.remove('is-invalid');
    }
};

// FUNCIÓN PARA SOLO NÚMEROS
const soloNumeros = (event) => {
    const char = String.fromCharCode(event.which);
    if (!/[0-9]/.test(char)) {
        event.preventDefault();
    }
};

console.log("Módulo de usuarios con fotografía inicializado correctamente");