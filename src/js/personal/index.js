import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const formPersonal = document.getElementById('formPersonal');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscarPersonal = document.getElementById('BtnBuscarPersonal');
const seccionTabla = document.getElementById('seccionTabla');
const InputPersonalDpi = document.getElementById('personal_dpi');

// Validación de DPI en tiempo real
const ValidarDpi = () => {
    const dpi = InputPersonalDpi.value.trim();

    if (dpi.length < 1) {
        InputPersonalDpi.classList.remove('is-valid', 'is-invalid');
    } else {
        if (dpi.length < 13) {
            Swal.fire({
                position: "center",
                icon: "error",
                title: "DPI INVALIDO",
                text: "El DPI debe tener exactamente 13 dígitos",
                showConfirmButton: true,
            });

            InputPersonalDpi.classList.remove('is-valid');
            InputPersonalDpi.classList.add('is-invalid');
        } else {
            InputPersonalDpi.classList.remove('is-invalid');
            InputPersonalDpi.classList.add('is-valid');
        }
    }
}

const guardarPersonal = async e => {
    e.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(formPersonal, ['personal_id'])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe completar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnGuardar.disabled = false;
        return;
    }

    const body = new FormData(formPersonal);
    const url = "/guzman_final_armamento_ingSoft1/personal/guardarAPI";
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
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            buscarPersonal();
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
            text: "Error de conexión con el servidor",
            showConfirmButton: true,
        });
    }
    BtnGuardar.disabled = false;
}

const buscarPersonal = async () => {
    const url = `/guzman_final_armamento_ingSoft1/personal/buscarAPI`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            console.log('Personal encontrado:', data);

            if (datatable) {
                datatable.clear().draw();
                datatable.rows.add(data).draw();
            }
        } else {
            await Swal.fire({
                position: "center",
                icon: "info",
                title: "Información",
                text: mensaje,
                showConfirmButton: true,
            });
        }

    } catch (error) {
        console.log(error);
    }
}

const mostrarTabla = () => {
    if (seccionTabla.style.display === 'none') {
        seccionTabla.style.display = 'block';
        buscarPersonal();
    } else {
        seccionTabla.style.display = 'none';
    }
}

const datatable = new DataTable('#TablePersonal', {
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
            data: 'personal_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Nombres', 
            data: 'personal_nombres',
            width: '18%'
        },
        { 
            title: 'Apellidos', 
            data: 'personal_apellidos',
            width: '18%'
        },
        {
            title: 'Grado',
            data: 'personal_grado',
            width: '15%',
            render: (data, type, row) => {
                let badgeClass = '';
                
                if (data.includes('General') || data.includes('Coronel')) {
                    badgeClass = 'bg-danger';
                } else if (data.includes('Mayor') || data.includes('Capitán') || data.includes('Teniente')) {
                    badgeClass = 'bg-warning';
                } else if (data.includes('Sargento')) {
                    badgeClass = 'bg-info';
                } else {
                    badgeClass = 'bg-success';
                }
                
                return `<span class="badge ${badgeClass}">${data}</span>`;
            }
        },
        { 
            title: 'Unidad', 
            data: 'personal_unidad',
            width: '20%',
            render: (data, type, row) => {
                return data.length > 30 ? data.substring(0, 30) + '...' : data;
            }
        },
        { 
            title: 'DPI', 
            data: 'personal_dpi',
            width: '12%'
        },
        {
            title: 'Situación',
            data: 'personal_situacion',
            width: '7%',
            render: (data, type, row) => {
                return data == 1 ? 
                    '<span class="badge bg-success">ACTIVO</span>' : 
                    '<span class="badge bg-secondary">INACTIVO</span>';
            }
        },
        {
            title: 'Acciones',
            data: 'personal_id',
            width: '10%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center gap-1'>
                    <button class='btn btn-sm btn-warning modificar' 
                       data-id="${data}" 
                       data-nombres="${row.personal_nombres || ''}"  
                       data-apellidos="${row.personal_apellidos || ''}"  
                       data-grado="${row.personal_grado || ''}" 
                       data-unidad="${row.personal_unidad || ''}"
                       data-dpi="${row.personal_dpi || ''}"
                       data-situacion="${row.personal_situacion || ''}"                       
                       title="Modificar">
                       <i class='bi bi-pencil-square'></i>
                    </button>
                    <button class='btn btn-sm btn-danger eliminar' 
                        data-id="${data}"
                        data-nombres="${row.personal_nombres || ''}"
                        data-apellidos="${row.personal_apellidos || ''}"
                        title="Eliminar">
                       <i class="bi bi-trash3"></i>
                    </button>
                </div>`;
            }
        }
    ]
});

const llenarFormulario = (event) => {
    const datos = event.currentTarget.dataset;

    document.getElementById('personal_id').value = datos.id;
    document.getElementById('personal_nombres').value = datos.nombres;
    document.getElementById('personal_apellidos').value = datos.apellidos;
    document.getElementById('personal_grado').value = datos.grado;
    document.getElementById('personal_unidad').value = datos.unidad;
    document.getElementById('personal_dpi').value = datos.dpi;
    document.getElementById('personal_situacion').value = datos.situacion;

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
    });
}

const limpiarTodo = () => {
    formPersonal.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
    
    // Limpiar clases de validación
    InputPersonalDpi.classList.remove('is-valid', 'is-invalid');
}

const modificarPersonal = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(formPersonal, [])) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe completar todos los campos obligatorios",
            showConfirmButton: true,
        });
        BtnModificar.disabled = false;
        return;
    }

    const body = new FormData(formPersonal);
    const url = '/guzman_final_armamento_ingSoft1/personal/modificarAPI';
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
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            limpiarTodo();
            buscarPersonal();
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
            text: "Error de conexión con el servidor",
            showConfirmButton: true,
        });
    }
    BtnModificar.disabled = false;
}

const eliminarPersonal = async (e) => {
    const idPersonal = e.currentTarget.dataset.id;
    const nombresPersonal = e.currentTarget.dataset.nombres;
    const apellidosPersonal = e.currentTarget.dataset.apellidos;
    const nombreCompleto = `${nombresPersonal} ${apellidosPersonal}`;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Desea ejecutar esta acción?",
        text: `Está completamente seguro que desea eliminar a ${nombreCompleto}?`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, Eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/guzman_final_armamento_ingSoft1/personal/eliminarAPI?id=${idPersonal}`;
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
                    title: "Éxito",
                    text: mensaje,
                    showConfirmButton: true,
                });
                
                buscarPersonal();
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
                text: "Error de conexión con el servidor",
                showConfirmButton: true,
            });
        }
    }
}

datatable.on('click', '.eliminar', eliminarPersonal);
datatable.on('click', '.modificar', llenarFormulario);
formPersonal.addEventListener('submit', guardarPersonal);
InputPersonalDpi.addEventListener('blur', ValidarDpi);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', modificarPersonal);
BtnBuscarPersonal.addEventListener('click', mostrarTabla);