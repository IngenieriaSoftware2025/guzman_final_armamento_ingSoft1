import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";
import { data } from "jquery";

const formUsuario = document.getElementById('formUsuario');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscarUsuarios = document.getElementById('BtnBuscarUsuarios');
const InputUsuarioDpi = document.getElementById('usuario_dpi');
const seccionTabla = document.getElementById('seccionTabla');

const ValidarDpi = () => {
    const dpi = InputUsuarioDpi.value.trim();

    if (dpi.length < 1) {
        InputUsuarioDpi.classList.remove('is-valid', 'is-invalid');
    } else {
        if (dpi.length < 13) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "DPI INVALIDO",
                text: "El DPI debe tener al menos 13 caracteres",
                showConfirmButton: true,
            });

            InputUsuarioDpi.classList.remove('is-valid');
            InputUsuarioDpi.classList.add('is-invalid');
        } else {
            InputUsuarioDpi.classList.remove('is-invalid');
            InputUsuarioDpi.classList.add('is-valid');
        }
    }
}

const guardarUsuario = async e => {
    e.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(formUsuario, ['usuario_id', 'usuario_contraseña', 'usuario_fecha_creacion',  'usuario_fotografia', 'usuario_situacion',])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(formUsuario);
    const url = "/baseguzman/usuarios/guardarAPI";
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        console.log(datos);
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarUsuarios();
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error);
    }
    BtnGuardar.disabled = false;
}

const BuscarUsuarios = async () => {
    const url = `/baseguzman/usuarios/buscarAPI`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            console.log('Usuarios encontrados:', data);

            if (datatable) {
                datatable.clear().draw();
                datatable.rows.add(data).draw();
            }
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error);
    }
}

const MostrarTabla = () => {
    if (seccionTabla.style.display === 'none') {
        seccionTabla.style.display = 'block';
        BuscarUsuarios();
    } else {
        seccionTabla.style.display = 'none';
    }
}

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
            title: 'Nombre', 
            data: 'usuario_nombre',
            width: '10%'
        },
        { 
            title: 'Apellido', 
            data: 'usuario_apellido',
            width: '10%'
        },{ 
            title: 'DPI', 
            data: 'usuario_dpi',
            width: '10%'
        },
        { 
            title: 'Correo', 
            data: 'usuario_correo',
            width: '15%'
        },
        {
            title: 'Fotografía',
            data: 'usuario_fotografia',
            width: '8%',
            searchable: false,
            orderable: false,
            render: (data, type, row) => {
                if (data && data.trim() !== '') {
                    return `<img src="${data}" alt="Foto" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">`;
                } else {
                    return '<span class="text-muted">Sin foto</span>';
                }
            }
        },
        {
            title: 'Situación',
            data: 'usuario_situacion',
            width: '7%',
            render: (data, type, row) => {
                return data == 1 ? "ACTIVO" : "INACTIVO";
            }
        },
        {
            title: 'Acciones',
            data: 'usuario_id',
            width: '5%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                 <div class='d-flex justify-content-center'>
                     <button class='btn btn-warning modificar mx-1' 
                        data-id="${data}" 
                        data-nombre="${row.usuario_nombre || ''}"  
                        data-apellido="${row.usuario_apellido || ''}"  
                        data-dpi="${row.usuario_dpi || ''}" 
                        data-correo="${row.usuario_correo || ''}"                         
                         title="Modificar">
                         <i class='bi bi-pencil-square me-1'></i> Modificar
                     </button>
                     <button class='btn btn-danger eliminar mx-1' 
                         data-id="${data}"
                         title="Eliminar">
                        <i class="bi bi-trash3 me-1"></i>Eliminar
                     </button>
                 </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('usuario_id').value = datos.id;
    document.getElementById('usuario_nombre').value = datos.nombre;
    document.getElementById('usuario_apellido').value = datos.apellido;
    document.getElementById('usuario_dpi').value = datos.dpi;
    document.getElementById('usuario_correo').value = datos.correo;    

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
    });
}

const limpiarTodo = () => {
    formUsuario.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
}

const ModificarUsuario = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(formUsuario, ['usuario_id', 'usuario_contraseña', 'usuario_fecha_creacion',  'usuario_situacion', 'usuario_fotografia', 'confirmar_contra'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe de validar todos los campos",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(formUsuario);
    const url = '/baseguzman/usuarios/modificarAPI';
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Exito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            BuscarUsuarios();
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Error",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error);
    }
    BtnModificar.disabled = false;
}

const EliminarUsuarios = async (e) => {
    const idUsuario = e.currentTarget.dataset.id;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "info",
        title: "¿Desea ejecutar esta acción?",
        text: 'Esta completamente seguro que desea eliminar este registro',
        showConfirmButton: true,
        confirmButtonText: 'Si, Eliminar',
        confirmButtonColor: 'red',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/baseguzman/usuarios/eliminar?id=${idUsuario}`;
        const config = {
            method: 'GET'
        }

        try {
            const consulta = await fetch(url, config);
            const respuesta = await consulta.json();
            const { codigo, mensaje } = respuesta;

            if (codigo == 1) {
                await Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Exito",
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
        }
    }
}

datatable.on('click', '.eliminar', EliminarUsuarios);
datatable.on('click', '.modificar', llenarFormulario);
formUsuario.addEventListener('submit', guardarUsuario);

document.getElementById('usuario_dpi')
InputUsuarioDpi.addEventListener('change', ValidarDpi);

BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', ModificarUsuario);
BtnBuscarUsuarios.addEventListener('click', MostrarTabla);