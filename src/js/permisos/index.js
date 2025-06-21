import { Modal } from "bootstrap";
import Swal from "sweetalert2";
import { validarFormulario } from '../funciones';
import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const formPermisos = document.getElementById('formPermisos');
const BtnAsignar = document.getElementById('BtnAsignar');
const BtnLimpiar = document.getElementById('BtnLimpiar');
const BtnVerPermisos = document.getElementById('BtnVerPermisos');
const selectUsuario = document.getElementById('permiso_usuario');
const selectAplicacion = document.getElementById('permiso_aplicacion');
const selectUsuarioPermisos = document.getElementById('usuario_permisos');
const seccionPermisosUsuario = document.getElementById('seccionPermisosUsuario');
const seccionTablaPermisos = document.getElementById('seccionTablaPermisos');
const modalRevocar = new Modal(document.getElementById('modalRevocarPermiso'));

document.addEventListener('DOMContentLoaded', function() {
    cargarUsuarios();
    cargarAplicaciones();
});

const cargarUsuarios = async () => {
    const url = `/guzman_final_armamento_ingSoft1/permisos/obtenerUsuariosAPI`;
    const config = { method: 'GET' }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectUsuario.innerHTML = '<option value="">Seleccione usuario</option>';
            selectUsuarioPermisos.innerHTML = '<option value="">Seleccione usuario para ver permisos</option>';
            
            data.forEach(usuario => {
                const option1 = document.createElement('option');
                option1.value = usuario.usuario_id;
                option1.textContent = `${usuario.usuario_nombre} ${usuario.usuario_apellido}`;
                selectUsuario.appendChild(option1);

                const option2 = document.createElement('option');
                option2.value = usuario.usuario_id;
                option2.textContent = `${usuario.usuario_nombre} ${usuario.usuario_apellido}`;
                selectUsuarioPermisos.appendChild(option2);
            });
        }
    } catch (error) {
        console.log('Error al cargar usuarios:', error);
    }
}

const cargarAplicaciones = async () => {
    const url = `/guzman_final_armamento_ingSoft1/permisos/obtenerAplicacionesAPI`;
    const config = { method: 'GET' }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            selectAplicacion.innerHTML = '<option value="">Seleccione aplicación</option>';
            data.forEach(app => {
                const option = document.createElement('option');
                option.value = app.app_id;
                option.textContent = `${app.app_nombre} - ${app.app_descripcion}`;
                selectAplicacion.appendChild(option);
            });
        }
    } catch (error) {
        console.log('Error al cargar aplicaciones:', error);
    }
}

const asignarPermiso = async e => {
    e.preventDefault();
    BtnAsignar.disabled = true;

    if (!validarFormulario(formPermisos)) {
        Swal.fire({
            position: "center",
            icon: "info",
            title: "FORMULARIO INCOMPLETO",
            text: "Debe completar todos los campos",
            showConfirmButton: true,
        });
        BtnAsignar.disabled = false;
        return;
    }

    const body = new FormData(formPermisos);
    const url = "/guzman_final_armamento_ingSoft1/permisos/asignarPermisoAPI";
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

            limpiarFormulario();
            if (selectUsuarioPermisos.value) {
                cargarPermisosUsuario();
            }
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
    BtnAsignar.disabled = false;
}

const cargarPermisosUsuario = async () => {
    const usuarioId = selectUsuarioPermisos.value;
    
    if (!usuarioId) {
        seccionPermisosUsuario.style.display = 'none';
        return;
    }

    const url = `/guzman_final_armamento_ingSoft1/permisos/obtenerPermisosUsuarioAPI?usuario_id=${usuarioId}`;
    const config = { method: 'GET' }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            seccionPermisosUsuario.style.display = 'block';
            
            let contenido = '';
            if (data.length > 0) {
                contenido = `
                <div class="row">
                    <div class="col-12">
                        <h5 class="mb-3">Usuario: ${data[0].usuario_nombre} ${data[0].usuario_apellido}</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead class="table-info">
                                    <tr>
                                        <th>Aplicación</th>
                                        <th>Descripción</th>
                                        <th>Nivel de Permiso</th>
                                        <th>Fecha Asignación</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>`;

                data.forEach(permiso => {
                    let badgeClass = '';
                    switch(permiso.permiso_nivel) {
                        case 'LECTURA': badgeClass = 'bg-info'; break;
                        case 'ESCRITURA': badgeClass = 'bg-warning'; break;
                        case 'TOTAL': badgeClass = 'bg-success'; break;
                        default: badgeClass = 'bg-secondary';
                    }

                    const fecha = new Date(permiso.permiso_fecha_asignacion).toLocaleDateString('es-GT');
                    
                    contenido += `
                        <tr>
                            <td><strong>${permiso.app_nombre}</strong></td>
                            <td>${permiso.app_descripcion}</td>
                            <td><span class="badge ${badgeClass}">${permiso.permiso_nivel}</span></td>
                            <td>${fecha}</td>
                            <td>
                                <button class="btn btn-sm btn-danger revocar-permiso" 
                                    data-id="${permiso.permiso_app_id}"
                                    data-info="${permiso.app_nombre} - ${permiso.permiso_nivel}">
                                    <i class="bi bi-shield-x"></i>
                                </button>
                            </td>
                        </tr>`;
                });

                contenido += `
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>`;
            } else {
                contenido = `
                <div class="text-center text-muted">
                    <i class="bi bi-shield-exclamation display-4"></i>
                    <p class="mt-3">Este usuario no tiene permisos asignados</p>
                </div>`;
            }

            document.getElementById('contenidoPermisosUsuario').innerHTML = contenido;
            document.querySelectorAll('.revocar-permiso').forEach(btn => {
                btn.addEventListener('click', abrirModalRevocar);
            });

        } else {
            seccionPermisosUsuario.style.display = 'none';
        }

    } catch (error) {
        console.log('Error al cargar permisos:', error);
        seccionPermisosUsuario.style.display = 'none';
    }
}

const datatable = new DataTable('#TablePermisos', {
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
            data: 'permiso_app_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Usuario', 
            data: 'usuario_nombre',
            width: '25%',
            render: (data, type, row) => {
                return `${data} ${row.usuario_apellido}`;
            }
        },
        { 
            title: 'Aplicación', 
            data: 'app_nombre',
            width: '20%',
            render: (data, type, row) => {
                return `<strong>${data}</strong><br><small class="text-muted">${row.app_descripcion}</small>`;
            }
        },
        {
            title: 'Nivel',
            data: 'permiso_nivel',
            width: '15%',
            render: (data, type, row) => {
                let badgeClass = '';
                switch(data) {
                    case 'LECTURA': badgeClass = 'bg-info'; break;
                    case 'ESCRITURA': badgeClass = 'bg-warning'; break;
                    case 'TOTAL': badgeClass = 'bg-success'; break;
                    default: badgeClass = 'bg-secondary';
                }
                return `<span class="badge ${badgeClass}">${data}</span>`;
            }
        },
        {
            title: 'Fecha Asignación',
            data: 'permiso_fecha_asignacion',
            width: '15%',
            render: (data, type, row) => {
                if (data) {
                    const fecha = new Date(data);
                    return fecha.toLocaleDateString('es-GT');
                }
                return 'N/A';
            }
        },
        {
            title: 'Acciones',
            data: 'permiso_app_id',
            width: '10%',
            searchable: false,
            orderable: false,
            render: (data, type, row, meta) => {
                return `
                <div class='d-flex justify-content-center'>
                    <button class='btn btn-sm btn-danger revocar' 
                        data-id="${data}"
                        data-info="${row.app_nombre} - ${row.usuario_nombre} ${row.usuario_apellido}"
                        title="Revocar Permiso">
                       <i class="bi bi-shield-x"></i>
                    </button>
                </div>`;
            }
        }
    ]
});

const mostrarTodosLosPermisos = async () => {
    const url = `/guzman_final_armamento_ingSoft1/permisos/obtenerPermisosUsuarioAPI?usuario_id=0`; // 0 para todos
    
    try {
        seccionTablaPermisos.style.display = 'block';
    
        await Swal.fire({
            position: "center",
            icon: "info",
            title: "Funcionalidad en desarrollo",
            text: "Esta funcionalidad será implementada próximamente",
            showConfirmButton: true,
        });
        
    } catch (error) {
        console.log('Error:', error);
    }
}

const abrirModalRevocar = (event) => {
    const datos = event.currentTarget.dataset;
    
    document.getElementById('confirmarRevocarPermiso').dataset.id = datos.id;
    document.getElementById('infoPermisoRevocar').textContent = datos.info;
    
    modalRevocar.show();
}

const revocarPermiso = async (event) => {
    const permisoId = event.currentTarget.dataset.id;
    
    const url = `/guzman_final_armamento_ingSoft1/permisos/revocarPermisoAPI?permiso_id=${permisoId}`;
    const config = { method: 'GET' }

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, mensaje } = datos;

        modalRevocar.hide();

        if (codigo == 1) {
            await Swal.fire({
                position: "center",
                icon: "success",
                title: "Éxito",
                text: mensaje,
                showConfirmButton: true,
            });

            if (selectUsuarioPermisos.value) {
                cargarPermisosUsuario();
            }
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
        modalRevocar.hide();
        await Swal.fire({
            position: "center",
            icon: "error",
            title: "Error",
            text: "Error de conexión con el servidor",
            showConfirmButton: true,
        });
    }
}

const limpiarFormulario = () => {
    formPermisos.reset();
}

// Event Listeners
formPermisos.addEventListener('submit', asignarPermiso);
BtnLimpiar.addEventListener('click', limpiarFormulario);
BtnVerPermisos.addEventListener('click', mostrarTodosLosPermisos);
selectUsuarioPermisos.addEventListener('change', cargarPermisosUsuario);
document.getElementById('confirmarRevocarPermiso').addEventListener('click', revocarPermiso);
datatable.on('click', '.revocar', abrirModalRevocar);