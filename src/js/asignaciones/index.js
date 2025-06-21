import { Modal } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const formAsignacion = document.getElementById('formAsignacion');
const BtnGuardar = document.getElementById('BtnGuardar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnBuscarAsignaciones = document.getElementById('BtnBuscarAsignaciones');
const BtnHistorial = document.getElementById('BtnHistorial');
const seccionTabla = document.getElementById('seccionTabla');
const selectArmamento = document.getElementById('asignacion_arma');
const selectPersonal = document.getElementById('asignacion_personal');
const selectPersonalHistorial = document.getElementById('personal_historial');
const modalDevolucion = new Modal(document.getElementById('modalDevolucion'));
const modalHistorial = new Modal(document.getElementById('modalHistorial'));
const modalEliminar = new Modal(document.getElementById('modalEliminarAsignacion'));


document.addEventListener('DOMContentLoaded', function() {
    cargarArmamentoDisponible();
    cargarPersonalDisponible();
    establecerFechaActual();
});

const establecerFechaActual = () => {
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('asignacion_fecha_asignacion').value = hoy;
    document.getElementById('fecha_devolucion').value = hoy;
}

const cargarArmamentoDisponible = async () => {
    const url = `/guzman_final_armamento_ingSoft1/asignaciones/obtenerArmamentoDisponibleAPI`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectArmamento.innerHTML = '<option value="">Seleccione armamento</option>';
            data.forEach(arma => {
                const option = document.createElement('option');
                option.value = arma.arma_id;
                option.textContent = `${arma.arma_numero_serie} - ${arma.tipo_nombre} ${arma.calibre_nombre}`;
                selectArmamento.appendChild(option);
            });
        }
    } catch (error) {
        console.log('Error al cargar armamento:', error);
    }
}

const cargarPersonalDisponible = async () => {
    const url = `/guzman_final_armamento_ingSoft1/asignaciones/obtenerPersonalDisponibleAPI`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectPersonal.innerHTML = '<option value="">Seleccione personal</option>';
            selectPersonalHistorial.innerHTML = '<option value="">Seleccione personal para ver historial</option>';
            
            data.forEach(persona => {
                const option = document.createElement('option');
                option.value = persona.personal_id;
                option.textContent = `${persona.personal_grado} ${persona.personal_nombres} ${persona.personal_apellidos}`;
                selectPersonal.appendChild(option);

                const optionHistorial = document.createElement('option');
                optionHistorial.value = persona.personal_id;
                optionHistorial.textContent = `${persona.personal_grado} ${persona.personal_nombres} ${persona.personal_apellidos}`;
                selectPersonalHistorial.appendChild(optionHistorial);
            });
        }
    } catch (error) {
        console.log('Error al cargar personal:', error);
    }
}

const guardarAsignacion = async e => {
    e.preventDefault();
    BtnGuardar.disabled = true;

    if (!validarFormulario(formAsignacion, ['asignacion_id'])) {
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

    const body = new FormData(formAsignacion);
    const url = "/guzman_final_armamento_ingSoft1/asignaciones/guardarAPI";
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
            cargarArmamentoDisponible(); 
            buscarAsignaciones();
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

const buscarAsignaciones = async () => {
    const url = `/guzman_final_armamento_ingSoft1/asignaciones/buscarAPI`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje, data } = datos;

        if (codigo == 1) {
            console.log('Asignaciones encontradas:', data);

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
        buscarAsignaciones();
    } else {
        seccionTabla.style.display = 'none';
    }
}

const datatable = new DataTable('#TableAsignaciones', {
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
            data: 'asignacion_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Armamento', 
            data: 'arma_numero_serie',
            width: '15%',
            render: (data, type, row) => {
                return `${data}<br><small class="text-muted">${row.tipo_nombre} ${row.calibre_nombre}</small>`;
            }
        },
        { 
            title: 'Personal', 
            data: 'personal_nombres',
            width: '15%',
            render: (data, type, row) => {
                return `${row.personal_grado}<br><strong>${data} ${row.personal_apellidos}</strong>`;
            }
        },
        {
            title: 'Fecha Asignación',
            data: 'asignacion_fecha_asignacion',
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
            title: 'Fecha Devolución',
            data: 'asignacion_fecha_devolucion',
            width: '10%',
            render: (data, type, row) => {
                if (data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return '<span class="text-muted">Pendiente</span>';
            }
        },
        {
            title: 'Estado',
            data: 'asignacion_estado',
            width: '10%',
            render: (data, type, row) => {
                let badgeClass = '';
                
                switch(data) {
                    case 'ASIGNADO':
                        badgeClass = 'bg-success';
                        break;
                    case 'DEVUELTO':
                        badgeClass = 'bg-secondary';
                        break;
                    default:
                        badgeClass = 'bg-warning';
                }
                
                return `<span class="badge ${badgeClass}">${data}</span>`;
            }
        },
        {
            title: 'Motivo',
            data: 'asignacion_motivo',
            width: '15%',
            render: (data, type, row) => {
                if (data && data.trim() !== '') {
                    return data.length > 40 ? data.substring(0, 40) + '...' : data;
                }
                return '<span class="text-muted">Sin motivo</span>';
            }
        },
        { 
            title: 'Usuario', 
            data: 'usuario_nombre',
            width: '10%',
            render: (data, type, row) => {
                return `${data} ${row.usuario_apellido}`;
            }
        },
        {
            title: 'Acciones',
            data: 'asignacion_id',
            width: '10%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                let botones = `
                <div class='d-flex justify-content-center gap-1'>`;

                if (row.asignacion_estado === 'ASIGNADO') {
                    botones += `
                    <button class='btn btn-sm btn-warning devolver' 
                       data-id="${data}" 
                       data-armamento="${row.arma_numero_serie}"  
                       data-personal="${row.personal_grado} ${row.personal_nombres} ${row.personal_apellidos}"
                       title="Devolver Armamento">
                       <i class='bi bi-arrow-return-left'></i>
                    </button>`;
                }

                botones += `
                    <button class='btn btn-sm btn-danger eliminar' 
                        data-id="${data}"
                        data-info="${row.arma_numero_serie} - ${row.personal_nombres} ${row.personal_apellidos}"
                        title="Eliminar">
                       <i class="bi bi-trash3"></i>
                    </button>
                </div>`;

                return botones;
            }
        }
    ]
});

const abrirModalDevolucion = (event) => {
    const datos = event.currentTarget.dataset;
    
    document.getElementById('devolucion_asignacion_id').value = datos.id;
    document.getElementById('armamento_devolucion').textContent = datos.armamento;
    document.getElementById('personal_devolucion').textContent = datos.personal;
    
    modalDevolucion.show();
}

const procesarDevolucion = async () => {
    const asignacionId = document.getElementById('devolucion_asignacion_id').value;
    const fechaDevolucion = document.getElementById('fecha_devolucion').value;

    if (!fechaDevolucion) {
        Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "Debe seleccionar la fecha de devolución",
            showConfirmButton: true,
        });
        return;
    }

    const body = new FormData();
    body.append('asignacion_id', asignacionId);
    body.append('fecha_devolucion', fechaDevolucion);

    const url = "/guzman_final_armamento_ingSoft1/asignaciones/devolverAPI";
    const config = {
        method: 'POST',
        body
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        if (codigo == 1) {
            modalDevolucion.hide();
            
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            cargarArmamentoDisponible(); 
            buscarAsignaciones();
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

const mostrarHistorial = () => {
    modalHistorial.show();
}

const cargarHistorialPersonal = async () => {
    const personalId = selectPersonalHistorial.value;
    
    if (!personalId) {
        document.getElementById('contenido_historial').innerHTML = `
            <div class="text-center text-muted">
                <i class="bi bi-info-circle me-2"></i>Seleccione un personal para ver su historial de asignaciones
            </div>`;
        return;
    }

    const url = `/guzman_final_armamento_ingSoft1/asignaciones/obtenerHistorialPersonalAPI?personal_id=${personalId}`;
    const config = {
        method: 'GET'
    }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1 && data.length > 0) {
            let tabla = `
            <div class="table-responsive">
                <table class="table table-striped table-sm">
                    <thead class="table-dark">
                        <tr>
                            <th>Armamento</th>
                            <th>Fecha Asignación</th>
                            <th>Fecha Devolución</th>
                            <th>Estado</th>
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>`;

            data.forEach(asignacion => {
                const fechaAsignacion = new Date(asignacion.asignacion_fecha_asignacion).toLocaleDateString('es-GT');
                const fechaDevolucion = asignacion.asignacion_fecha_devolucion ? 
                    new Date(asignacion.asignacion_fecha_devolucion).toLocaleDateString('es-GT') : 
                    'Pendiente';
                
                const estadoBadge = asignacion.asignacion_estado === 'ASIGNADO' ? 
                    '<span class="badge bg-success">ASIGNADO</span>' : 
                    '<span class="badge bg-secondary">DEVUELTO</span>';

                tabla += `
                    <tr>
                        <td>${asignacion.arma_numero_serie}<br><small class="text-muted">${asignacion.tipo_nombre} ${asignacion.calibre_nombre}</small></td>
                        <td>${fechaAsignacion}</td>
                        <td>${fechaDevolucion}</td>
                        <td>${estadoBadge}</td>
                        <td>${asignacion.asignacion_motivo}</td>
                    </tr>`;
            });

            tabla += `
                    </tbody>
                </table>
            </div>`;

            document.getElementById('contenido_historial').innerHTML = tabla;
        } else {
            document.getElementById('contenido_historial').innerHTML = `
                <div class="text-center text-muted">
                    <i class="bi bi-inbox me-2"></i>No se encontraron asignaciones para este personal
                </div>`;
        }

    } catch (error) {
        console.log(error);
        document.getElementById('contenido_historial').innerHTML = `
            <div class="text-center text-danger">
                <i class="bi bi-exclamation-circle me-2"></i>Error al cargar el historial
            </div>`;
    }
}

const eliminarAsignacion = async (e) => {
    const idAsignacion = e.currentTarget.dataset.id;
    const infoAsignacion = e.currentTarget.dataset.info;

    const AlertaConfirmarEliminar = await Swal.fire({
        position: "center",
        icon: "warning",
        title: "¿Desea ejecutar esta acción?",
        text: `Está completamente seguro que desea eliminar la asignación: ${infoAsignacion}?`,
        showConfirmButton: true,
        confirmButtonText: 'Sí, Eliminar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'No, Cancelar',
        showCancelButton: true
    });

    if (AlertaConfirmarEliminar.isConfirmed) {
        const url = `/guzman_final_armamento_ingSoft1/asignaciones/eliminarAPI?id=${idAsignacion}`;
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
                
                cargarArmamentoDisponible(); 
                buscarAsignaciones();
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

const limpiarTodo = () => {
    formAsignacion.reset();
    establecerFechaActual();
}

// Event Listeners
datatable.on('click', '.devolver', abrirModalDevolucion);
datatable.on('click', '.eliminar', eliminarAsignacion);
formAsignacion.addEventListener('submit', guardarAsignacion);
BtnLimpiar.addEventListener('click', limpiarTodo);
BtnBuscarAsignaciones.addEventListener('click', mostrarTabla);
BtnHistorial.addEventListener('click', mostrarHistorial);
document.getElementById('confirmarDevolucion').addEventListener('click', procesarDevolucion);
selectPersonalHistorial.addEventListener('change', cargarHistorialPersonal);