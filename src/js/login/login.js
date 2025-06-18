import Swal from 'sweetalert2';
import { validarFormulario } from '../funciones';

const FormLogin = document.getElementById('FormLogin');
const BtnIniciar = document.getElementById('BtnIniciar');
const inputDPI = document.getElementById('usuario_dpi');

// Validación de DPI - solo números
inputDPI.addEventListener('input', function() {
    this.value = this.value.replace(/[^0-9]/g, '');
});

const login = async (e) => {
    e.preventDefault();
    
    BtnIniciar.disabled = true;

    if (!validarFormulario(FormLogin, [''])) {
        Swal.fire({
            title: "Campos vacíos",
            text: "Debe llenar todos los campos",
            icon: "info"
        });
        BtnIniciar.disabled = false;
        return;
    }

    // Validar DPI de 13 dígitos
    const dpi = inputDPI.value.trim();
    if (dpi.length !== 13) {
        Swal.fire({
            title: "DPI inválido",
            text: "El DPI debe tener exactamente 13 dígitos",
            icon: "warning"
        });
        BtnIniciar.disabled = false;
        return;
    }

    try {
        const body = new FormData(FormLogin);
        const url = '/guzman_final_armamento_ingSof1t1/API/login';

        const config = {
            method: 'POST',
            body
        };

        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const { codigo, mensaje, detalle, usuario } = data;

        if (codigo == 1) {
            await Swal.fire({
                title: 'Éxito',
                text: `Bienvenido ${usuario?.nombre || ''} ${usuario?.apellido || ''}`,
                icon: 'success',
                showConfirmButton: true,
                timer: 2000,
                timerProgressBar: true,
                background: '#e0f7fa',
                customClass: {
                    title: 'custom-title-class',
                    text: 'custom-text-class'
                }
            });

            FormLogin.reset();
            location.href = '/guzman_final_armamento_ingSof1t1/inicio';
        } else {
            Swal.fire({
                title: '¡Error!',
                text: mensaje,
                icon: 'warning',
                showConfirmButton: true,
                timer: 3000,
                timerProgressBar: true,
                background: '#ffebee',
                customClass: {
                    title: 'custom-title-class',
                    text: 'custom-text-class'
                }
            });
        }

    } catch (error) {
        console.log(error);
        Swal.fire({
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor',
            icon: 'error'
        });
    }

    BtnIniciar.disabled = false;
};

FormLogin.addEventListener('submit', login);