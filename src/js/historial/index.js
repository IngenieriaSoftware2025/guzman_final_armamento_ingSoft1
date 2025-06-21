import DataTable from "datatables.net-bs5";
import { lenguaje } from "../lenguaje";

const BtnActualizar = document.getElementById('BtnActualizar');

const datatable = new DataTable('#TableHistorial', {
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
    order: [[4, 'desc']],
    columns: [
        {
            title: 'No.',
            data: 'historial_id',
            width: '5%',
            render: (data, type, row, meta) => meta.row + 1
        },
        { 
            title: 'Usuario', 
            data: 'usuario_nombre',
            width: '20%',
            render: (data, type, row) => {
                return `<strong>${data} ${row.usuario_apellido}</strong><br><small class="text-muted">${row.usuario_correo}</small>`;
            }
        },
        { 
            title: 'Actividad', 
            data: 'historial_descripcion',
            width: '35%'
        },
        { 
            title: 'Módulo', 
            data: 'historial_tabla_afectada',
            width: '20%',
            render: (data, type, row) => {
                let badgeClass = 'bg-secondary';
                let texto = data;
                
                switch(data) {
                    case 'guzman_usuarios':
                        badgeClass = 'bg-primary';
                        texto = 'USUARIOS';
                        break;
                    case 'guzman_armamento':
                        badgeClass = 'bg-success';
                        texto = 'ARMAMENTO';
                        break;
                    case 'guzman_asignaciones_armamento':
                        badgeClass = 'bg-warning';
                        texto = 'ASIGNACIONES';
                        break;
                    case 'guzman_personal':
                        badgeClass = 'bg-info';
                        texto = 'PERSONAL';
                        break;
                }
                
                return `<span class="badge ${badgeClass}">${texto}</span>`;
            }
        },
        {
            title: 'Fecha y Hora',
            data: 'historial_fecha',
            width: '20%',
            render: (data, type, row) => {
                if (data) {
                    const fecha = new Date(data);
                    return `${fecha.toLocaleDateString('es-GT')}<br><small>${fecha.toLocaleTimeString('es-GT')}</small>`;
                }
                return 'N/A';
            }
        }
    ]
});

const cargarActividades = async () => {
    const url = `/guzman_final_armamento_ingSoft1/historial/obtenerActividadesAPI`;
    const config = { method: 'GET' };

    try {
        const respuesta = await fetch(url, config);
        const datos = await respuesta.json();
        const { codigo, data } = datos;

        if (codigo == 1) {
            datatable.clear().draw();
            datatable.rows.add(data).draw();
        }
    } catch (error) {
        console.log('Error al cargar actividades:', error);
    }
};

// Event Listeners
BtnActualizar.addEventListener('click', cargarActividades);

// Cargar al inicializar
document.addEventListener('DOMContentLoaded', cargarActividades);