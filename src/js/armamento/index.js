import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const formArmamento = document.getElementById('formArmamento');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnModificar = document.getElementById('BtnModificar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscarArmamento = document.getElementById('BtnBuscarArmamento');
const seccionTabla = document.getElementById('seccionTabla');

// Referencias a selects
const selectTipo = document.getElementById('arma_tipo');
const selectCalibre = document.getElementById('arma_calibre');
const selectAlmacen = document.getElementById('arma_almacen');

// Cargar datos iniciales al cargar la página
document.addEventListener('DOMContentLoaded', function() {
    cargarTiposArmamento();
    cargarCalibres();
    cargarAlmacenes();
});

const cargarTiposArmamento = async () => {
    const url = `/guzman_final_armamento_ingSoft1/armamento/obtenerTiposAPI`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectTipo.innerHTML = '<option value="">Seleccione tipo de armamento</option>';
            data.forEach(tipo => {
                const option = document.createElement('option');
                option.value = tipo.tipo_id;
                option.textContent = tipo.tipo_nombre;
                selectTipo.appendChild(option);
            });
        }
    } catch (error) {
        console.log('Error al cargar tipos:', error);
    }
}

const cargarCalibres = async () => {
    const url = `/guzman_final_armamento_ingSoft1/armamento/obtenerCalibresAPI`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectCalibre.innerHTML = '<option value="">Seleccione calibre</option>';
            data.forEach(calibre => {
                const option = document.createElement('option');
                option.value = calibre.calibre_id;
                option.textContent = calibre.calibre_nombre;
                selectCalibre.appendChild(option);
            });
        }
    } catch (error) {
        console.log('Error al cargar calibres:', error);
    }
}

const cargarAlmacenes = async () => {
    const url = `/guzman_final_armamento_ingSoft1/armamento/obtenerAlmacenesAPI`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectAlmacen.innerHTML = '<option value="">Seleccione almacén</option>';
            data.forEach(almacen => {
                const option = document.createElement('option');
                option.value = almacen.almacen_id;
                option.textContent = almacen.almacen_nombre;
                selectAlmacen.appendChild(option);
            });
        }
    } catch (error) {
        console.log('Error al cargar almacenes:', error);
    }
}

const guardarArmamento = async e => {
    e.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(formArmamento, ['arma_id', 'arma_observaciones'])) {
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

    const body = new FormData(formArmamento);
    const url = "/guzman_final_armamento_ingSoft1/armamento/guardarAPI";
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
            buscarArmamento();
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

const buscarArmamento = async () => {
    const url = `/guzman_final_armamento_ingSoft1/armamento/buscarAPI`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            console.log('Armamento encontrado:', data);

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
        buscarArmamento();
    } else {
        seccionTabla.style.display = 'none';
    }
}

const datatable = new DataTable('#TableArmamento', {
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
            data: 'arma_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Número de Serie', 
            data: 'arma_numero_serie',
            width: '12%'
        },
        { 
            title: 'Tipo', 
            data: 'tipo_nombre',
            width: '10%'
        },
        { 
            title: 'Calibre', 
            data: 'calibre_nombre',
            width: '8%'
        },
        {
            title: 'Estado',
            data: 'arma_estado',
            width: '12%',
            render: (data, type, row) => {
                let badgeClass = '';
                let texto = '';
                
                switch(data) {
                    case 'BUEN_ESTADO':
                        badgeClass = 'bg-success';
                        texto = 'BUEN ESTADO';
                        break;
                    case 'MAL_ESTADO_REPARABLE':
                        badgeClass = 'bg-warning';
                        texto = 'MAL ESTADO REPARABLE';
                        break;
                    case 'MAL_ESTADO_IRREPARABLE':
                        badgeClass = 'bg-danger';
                        texto = 'MAL ESTADO IRREPARABLE';
                        break;
                    default:
                        badgeClass = 'bg-secondary';
                        texto = data;
                }
                
                return `<span class="badge ${badgeClass}">${texto}</span>`;
            }
        },
        { 
            title: 'Almacén', 
            data: 'almacen_nombre',
            width: '10%'
        },
        {
            title: 'Fecha Ingreso',
            data: 'arma_fecha_ingreso',
            width: '10%',
            render: (data, type, row) => {
                if (data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return 'N/A';
            }
        },
        {
            title: 'Observaciones',
            data: 'arma_observaciones',
            width: '15%',
            render: (data, type, row) => {
                if (data && data.trim() !== '') {
                    return data.length > 50 ? data.substring(0, 50) + '...' : data;
                }
                return '<span class="text-muted">Sin observaciones</span>';
            }
        },
        {
            title: 'Situación',
            data: 'arma_situacion',
            width: '8%',
            render: (data, type, row) => {
                return data == 1 ? 
                    '<span class="badge bg-success">ACTIVO</span>' : 
                    '<span class="badge bg-secondary">INACTIVO</span>';
            }
        },
        {
            title: 'Acciones',
            data: 'arma_id',
            width: '10%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center gap-1'>
                    <button class='btn btn-sm btn-warning modificar' 
                       data-id="${data}" 
                       data-serie="${row.arma_numero_serie || ''}"  
                       data-tipo="${row.arma_tipo || ''}"  
                       data-calibre="${row.arma_calibre || ''}" 
                       data-estado="${row.arma_estado || ''}"
                       data-almacen="${row.arma_almacen || ''}"
                       data-observaciones="${row.arma_observaciones || ''}"
                       data-situacion="${row.arma_situacion || ''}"                       
                       title="Modificar">
                       <i class='bi bi-pencil-square'></i>
                    </button>
                    <button class='btn btn-sm btn-danger eliminar' 
                        data-id="${data}"
                        data-serie="${row.arma_numero_serie || ''}"
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

    document.getElementById('arma_id').value = datos.id;
    document.getElementById('arma_numero_serie').value = datos.serie;
    document.getElementById('arma_tipo').value = datos.tipo;
    document.getElementById('arma_calibre').value = datos.calibre;
    document.getElementById('arma_estado').value = datos.estado;
    document.getElementById('arma_almacen').value = datos.almacen;
    document.getElementById('arma_observaciones').value = datos.observaciones;
    document.getElementById('arma_situacion').value = datos.situacion;

    BtnGuardar.classList.add('d-none');
    BtnModificar.classList.remove('d-none');

    window.scrollTo({
        top: 0,
    });
}

const limpiarTodo = () => {
    formArmamento.reset();
    BtnGuardar.classList.remove('d-none');
    BtnModificar.classList.add('d-none');
}

const modificarArmamento = async (event) => {
    event.preventDefault();
    BtnModificar.disabled = true;

    if (!validarFormulario(formArmamento, ['arma_observaciones'])) {
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

    const body = new FormData(formArmamento);
    const url = '/guzman_final_armamento_ingSoft1/armamento/modificarAPI';
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
            buscarArmamento();
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

const eliminarArmamento = async (e) => {
    const idArmamento = e.currentTarget.dataset.id;
    const serieArmamento = e.currentTarget.dataset.serie;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Desea ejecutar esta acción?",
        text: `Está completamente seguro que desea eliminar el armamento ${serieArmamento}?`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, Eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/guzman_final_armamento_ingSoft1/armamento/eliminarAPI?id=${idArmamento}`;
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
                
                buscarArmamento();
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

// Event Listeners
datatable.on('click', '.eliminar', eliminarArmamento);
datatable.on('click', '.modificar', llenarFormulario);
formArmamento.addEventListener('submit', guardarArmamento);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnModificar.addEventListener('click', modificarArmamento);
BtnBuscarArmamento.addEventListener('click', mostrarTabla);