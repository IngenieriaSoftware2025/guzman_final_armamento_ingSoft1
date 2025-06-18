import { Toast, validarFormulario } from "../funciones";
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import Swal from "sweetalert2";

const FormUsuarios = document.getElementById("FormUsuarios");
const BtnGuardar = document.getElementById("BtnGuardar");
const BtnModificar = document.getElementById("BtnModificar");
const BtnLimpiar = document.getElementById("BtnLimpiar");
const BtnBuscarUsuarios = document.getElementById("BtnBuscarUsuarios");
const TableUsuarios = document.getElementById("TableUsuarios");

let TablaUsuarios;

// FUNCIÓN PARA CARGAR ROLES DISPONIBLES
const cargarRoles = async () => {
    try {
        const url = "/api/usuarios/roles";
        const config = {
            method: "GET"
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            const selectRol = document.getElementById("usuario_rol");
            selectRol.innerHTML = '<option value="">Seleccione un rol...</option>';
            
            datos.data.forEach(rol => {
                const option = document.createElement("option");
                option.value = rol.rol_id;
                option.textContent = `${rol.rol_nombre} - ${rol.rol_descripcion}`;
                selectRol.appendChild(option);
            });
        }
    } catch (error) {
        console.error("Error al cargar roles:", error);
    }
};

// FUNCIÓN PARA GUARDAR USUARIO
const guardarUsuario = async (e) => {
    e.preventDefault();

    // Validar campos requeridos
    if (
        !FormUsuarios.usuario_nombre.value ||
        !FormUsuarios.usuario_apellido.value ||
        !FormUsuarios.usuario_dpi.value ||
        !FormUsuarios.usuario_correo.value ||
        !FormUsuarios.usuario_contra.value ||
        !FormUsuarios.usuario_rol.value
    ) {
        Toast.fire({
            icon: "error",
            title: "Complete todos los campos requeridos"
        });
        return;
    }

    // Validar DPI (13 dígitos)
    if (!/^\d{13}$/.test(FormUsuarios.usuario_dpi.value)) {
        Toast.fire({
            icon: "error",
            title: "El DPI debe tener exactamente 13 dígitos"
        });
        return;
    }

    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(FormUsuarios.usuario_correo.value)) {
        Toast.fire({
            icon: "error",
            title: "Ingrese un correo electrónico válido"
        });
        return;
    }

    try {
        const body = new FormData(FormUsuarios);
        const url = "/api/usuarios/guardar";

        const config = {
            method: "POST",
            body
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Toast.fire({
                icon: "success",
                title: datos.mensaje
            });

            limpiarFormulario();
            buscarUsuarios();
        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error de conexión"
        });
    }
};

// FUNCIÓN PARA BUSCAR USUARIOS
const buscarUsuarios = async () => {
    try {
        const url = "/api/usuarios/buscar";
        const config = {
            method: "GET"
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            // Mostrar sección de tabla
            document.getElementById("seccion-usuarios").classList.remove("d-none");
            document.getElementById("mensaje-sin-usuarios").classList.add("d-none");

            llenarTablaUsuarios(datos.data);
        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error al buscar usuarios"
        });
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
        order: [[1, "asc"]],
        columns: [
            {
                title: "ID",
                data: "usuario_id",
                width: "5%"
            },
            {
                title: "Nombre",
                data: "usuario_nombre",
                width: "15%"
            },
            {
                title: "Apellido",
                data: "usuario_apellido",
                width: "15%"
            },
            {
                title: "DPI",
                data: "usuario_dpi",
                width: "15%",
                render: (data) => `<code class="bg-light p-1 rounded">${data}</code>`
            },
            {
                title: "Correo",
                data: "usuario_correo",
                width: "20%"
            },
            {
                title: "Fecha Creación",
                data: "usuario_fecha_creacion",
                width: "12%",
                render: (data) => {
                    if (!data) return "N/A";
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString("es-GT");
                }
            },
            {
                title: "Estado",
                data: "usuario_situacion",
                width: "8%",
                render: (data) => {
                    switch (parseInt(data)) {
                        case 1:
                            return '<span class="badge bg-success">Activo</span>';
                        case 2:
                            return '<span class="badge bg-warning">Inactivo</span>';
                        case 3:
                            return '<span class="badge bg-info">Suspendido</span>';
                        default:
                            return '<span class="badge bg-danger">Eliminado</span>';
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
                        <div class="d-flex justify-content-center gap-1">
                            <button class="btn btn-warning btn-sm modificar-usuario" 
                                data-id="${data}"
                                data-nombre="${row.usuario_nombre}"
                                data-apellido="${row.usuario_apellido}"
                                data-dpi="${row.usuario_dpi}"
                                data-correo="${row.usuario_correo}"
                                data-situacion="${row.usuario_situacion}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-sm eliminar-usuario" 
                                data-id="${data}"
                                data-nombre="${row.usuario_nombre} ${row.usuario_apellido}">
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
        document.querySelectorAll(".modificar-usuario").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                llenarFormularioModificar(e);
            });
        });

        document.querySelectorAll(".eliminar-usuario").forEach((btn) => {
            btn.addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                eliminarUsuario(e);
            });
        });
    }, 500);
};

// FUNCIÓN PARA LLENAR FORMULARIO PARA MODIFICAR
const llenarFormularioModificar = async (e) => {
    const id = e.currentTarget.dataset.id;

    try {
        const url = `/api/usuarios/obtener?id=${id}`;
        const config = {
            method: "GET"
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            const usuario = datos.data;

            // Llenar formulario con datos del usuario
            document.getElementById("usuario_id").value = usuario.usuario_id;
            document.getElementById("usuario_nombre").value = usuario.usuario_nombre;
            document.getElementById("usuario_apellido").value = usuario.usuario_apellido;
            document.getElementById("usuario_dpi").value = usuario.usuario_dpi;
            document.getElementById("usuario_correo").value = usuario.usuario_correo;
            document.getElementById("usuario_situacion").value = usuario.usuario_situacion;

            // Seleccionar rol si existe
            if (usuario.rol_id) {
                document.getElementById("usuario_rol").value = usuario.rol_id;
            }

            // Limpiar campo de contraseña para modificación
            document.getElementById("usuario_contra").value = "";
            document.getElementById("usuario_contra").required = false;

            // Cambiar botones
            BtnGuardar.classList.add("d-none");
            BtnModificar.classList.remove("d-none");

            // Scroll al formulario
            window.scrollTo({ top: 0, behavior: "smooth" });

        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error al cargar datos del usuario"
        });
    }
};

// FUNCIÓN PARA MODIFICAR USUARIO
const modificarUsuario = async (e) => {
    e.preventDefault();

    // Validación manual
    if (
        !FormUsuarios.usuario_nombre.value.trim() ||
        !FormUsuarios.usuario_apellido.value.trim() ||
        !FormUsuarios.usuario_dpi.value.trim() ||
        !FormUsuarios.usuario_correo.value.trim()
    ) {
        Toast.fire({
            icon: "error",
            title: "Complete todos los campos obligatorios"
        });
        return;
    }

    // Validar DPI (13 dígitos)
    if (!/^\d{13}$/.test(FormUsuarios.usuario_dpi.value)) {
        Toast.fire({
            icon: "error",
            title: "El DPI debe tener exactamente 13 dígitos"
        });
        return;
    }

    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(FormUsuarios.usuario_correo.value)) {
        Toast.fire({
            icon: "error",
            title: "Ingrese un correo electrónico válido"
        });
        return;
    }

    try {
        const body = new FormData(FormUsuarios);
        const url = "/api/usuarios/modificar";

        const config = {
            method: "POST",
            body
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Toast.fire({
                icon: "success",
                title: datos.mensaje
            });

            limpiarFormulario();
            buscarUsuarios();
        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error de conexión"
        });
    }
};

// FUNCIÓN PARA ELIMINAR USUARIO
const eliminarUsuario = async (e) => {
    const id = e.currentTarget.dataset.id;
    const nombre = e.currentTarget.dataset.nombre;

    const confirmacion = await Swal.fire({
        title: "¿Eliminar usuario?",
        text: `¿Está seguro de eliminar el usuario "${nombre}"?`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Sí, eliminar",
        cancelButtonText: "Cancelar"
    });

    if (!confirmacion.isConfirmed) return;

    try {
        const url = `/api/usuarios/eliminar?id=${id}`;
        const config = {
            method: "DELETE"
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();

        if (datos.codigo === 1) {
            Toast.fire({
                icon: "success",
                title: datos.mensaje
            });

            buscarUsuarios();
        } else {
            Toast.fire({
                icon: "error",
                title: datos.mensaje
            });
        }
    } catch (error) {
        console.error("Error:", error);
        Toast.fire({
            icon: "error",
            title: "Error de conexión"
        });
    }
};

// FUNCIÓN PARA LIMPIAR FORMULARIO
const limpiarFormulario = () => {
    FormUsuarios.reset();
    document.getElementById("usuario_id").value = "";
    document.getElementById("usuario_contra").required = true;

    BtnGuardar.classList.remove("d-none");
    BtnModificar.classList.add("d-none");
};

// EVENT LISTENERS
FormUsuarios.addEventListener("submit", guardarUsuario);
BtnModificar.addEventListener("click", modificarUsuario);
BtnLimpiar.addEventListener("click", limpiarFormulario);
BtnBuscarUsuarios.addEventListener("click", buscarUsuarios);

// INICIALIZACIÓN
document.addEventListener("DOMContentLoaded", () => {
    cargarRoles();
});

console.log("Módulo de usuarios inicializado correctamente");