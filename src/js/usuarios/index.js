import Swal from "sweetalert2";
import DataTable from "datatables.net-bs5";
import { validarFormulario } from "../funciones";
import { lenguaje } from "../lenguaje";

const formUsuario = document.getElementById('formUsuario');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscarUsuarios = document.getElementById('BtnBuscarUsuarios');
const validarDpi = document.getElementById('usuario_dpi');

const validacionDpi = () => {
    const cantidadDigitos = validarDpi.value;

    if (cantidadDigitos.length < 1) {
        validarDpi.classList.remove('is-valid', 'is-invalid');
    } else {
        if (cantidadDigitos.length !== 13) {
            Swal.fire({
                position: "center",
                icon: "warning",
                title: "Revise el número de DPI",
                text: "La cantidad de dígitos debe ser igual a 13",
                showConfirmButton: false,
                timer: 3000
            });

            validarDpi.classList.remove('is-valid');
            validarDpi.classList.add('is-invalid');
        } else {
            validarDpi.classList.remove('is-invalid');
            validarDpi.classList.add('is-valid');
        }
    }
};

const guardarUsuario = async e => {
    e.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(formUsuario, ['usuario_id', 'usuario_situacion'])) {
        Swal.fire({
            position: "center",
            icon: "warning",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe validar todos los campos",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    try {
        const body = new FormData(formUsuario);
        const url = "/guzman_final_armamento_ingSoft1/usuarios/guardarAPI";
        const config = {
            method: 'POST',
            body
        };

        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const { codigo, mensaje } = data;

        if (codigo === 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });
            
            formUsuario.reset();
            BuscarUsuarios();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "Error de conexión",
            showConfirmButton: true,
        });
    }
    BtnGuardar.disabled = false;
};

const datatable = new DataTable('#TableUsuarios', {
    dom: `
        <"row mt-3 justify-content-between"
            <"col" l>
            <"col" B>
            <"col-3" f>
        >
        t
        <"row mt-3 justify-content-between"
            <"col-md-3 d-flex align-items-center" i> 
            <"col-md-8 d-flex justify-content-end" p>
        >
    `,
    language: lenguaje,
    data: [],
    columns: [
        {
            title: 'No.',
            data: 'usuario_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        {
            title: 'Fotografía',
            data: 'usuario_fotografia',
            width: '8%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                if (data && data !== null && data !== '') {
                    return `<img src="/guzman_final_armamento_ingSoft1/public/${data}" alt="Foto usuario" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;" onerror="this.onerror=null; this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNTAiIGhlaWdodD0iNTAiIHZpZXdCb3g9IjAgMCA1MCA1MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjUwIiBoZWlnaHQ9IjUwIiBmaWxsPSIjNjY2NjY2Ii8+CjxwYXRoIGQ9Ik0yNSAyM0MyNy43NjE0IDIzIDMwIDIwLjc2MTQgMzAgMThDMzAgMTUuMjM4NiAyNy43NjE0IDEzIDI1IDEzQzIyLjIzODYgMTMgMjAgMTUuMjM4NiAyMCAxOEMyMCAyMC43NjE0IDIyLjIzODYgMjMgMjUgMjNaIiBmaWxsPSJ3aGl0ZSIvPgo8cGF0aCBkPSJNMjUgMjVDMjAuMDI5NCAyNSAxNiAyOS4wMjk0IDE2IDM0VjM3SDM0VjM0QzM0IDI5LjAyOTQgMjkuOTcwNiAyNSAyNSAyNVoiIGZpbGw9IndoaXRlIi8+Cjwvc3ZnPgo=';">`;
                } else {
                    return `<div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="bi bi-person text-white"></i>
                            </div>`;
                }
            }
        },
        { title: 'Nombre', data: 'usuario_nombre', width: '15%' },
        { title: 'Apellido', data: 'usuario_apellido', width: '15%' },
        { title: 'DPI', data: 'usuario_dpi', width: '12%' },
        { title: 'Correo', data: 'usuario_correo', width: '20%' },
        {
            title: 'Situación',
            data: 'usuario_situacion',
            width: '8%',
            render: (data, type, row) => {
                return data == 1 ? 
                    '<span class="badge bg-success">Activo</span>' : 
                    '<span class="badge bg-danger">Inactivo</span>';
            }
        },
        {
            title: 'Acciones',
            data: 'usuario_id',
            width: '15%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning btn-sm modificar mx-1' 
                         data-id="${data}" 
                         data-nombre="${row.usuario_nombre}"  
                         data-apellido="${row.usuario_apellido}"  
                         data-dpi="${row.usuario_dpi}"  
                         data-correo="${row.usuario_correo}"
                         data-situacion="${row.usuario_situacion}">
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger btn-sm eliminar mx-1' 
                         data-id="${data}">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ],
});

const BuscarUsuarios = async () => {
    const url = '/guzman_final_armamento_ingSoft1/usuarios/buscarAPI';
    const config = {
        method: 'GET'
    };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo === 1) {
            const seccionTabla = document.getElementById('seccionTabla');
            seccionTabla.style.display = 'block';
            
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: false,
                timer: 2000,
            });

            datatable.clear().draw();
            datatable.rows.add(data).draw();
            
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Información",
                text: mensaje,
                showConfirmButton: false,
                timer: 2000,
            });
        }
    } catch (error) {
        console.log(error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "Error de conexión",
            showConfirmButton: true,
        });
    }
};

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('usuario_id').value = datos.id;
    document.getElementById('usuario_nombre').value = datos.nombre;
    document.getElementById('usuario_apellido').value = datos.apellido;
    document.getElementById('usuario_dpi').value = datos.dpi;
    document.getElementById('usuario_correo').value = datos.correo;
    document.getElementById('usuario_situacion').value = datos.situacion;

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
};

const limpiarTodo = () => {
    formUsuario.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
};

const ModificarUsuario = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(formUsuario, ['usuario_contra', 'usuario_fotografia'])) {
        await Swal.fire({
            position: "center",
            icon: "warning",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe validar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    try {
        const body = new FormData(formUsuario);
        const url = '/guzman_final_armamento_ingSoft1/usuarios/modificarAPI';
        const config = {
            method: 'POST',
            body
        };

        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarUsuarios();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "Error de conexión",
            showConfirmButton: true,
        });
    }
    BtnModificar.disabled = false;
};

const EliminarUsuario = async (e) => {
    const idUsuario = e.currentTarget.dataset.id;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "question",
        title: "¿Desea ejecutar esta acción?",
        text: "El usuario será eliminado del sistema",
        showConfirmButton: true,
        confirmButtonText: "Sí, Eliminar",
        confirmButtonColor: "#d33",
        cancelButtonText: "Cancelar",
        showCancelButton: true
    });

    if (!AlertaConfirmarEliminar.isConfirmed) return;

    try {
        const respuesta = await fetch(`/guzman_final_armamento_ingSoft1/usuarios/eliminarAPI?id=${idUsuario}`, {
            method: 'GET'
        });

        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo === 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });
            BuscarUsuarios();
        } else {
            await Swal.fire({
                position: "center",
                icon: "error",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error);
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "Error de conexión",
            showConfirmButton: true,
        });
    }
};

// Event Listeners
validarDpi.addEventListener('change', validacionDpi);
formUsuario.addEventListener('submit', guardarUsuario);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarUsuario);
BtnBuscarUsuarios.addEventListener('click', BuscarUsuarios);

// DataTable Events
datatable.on('click', '.eliminar', EliminarUsuario);
datatable.on('click', '.modificar', llenarFormulario);