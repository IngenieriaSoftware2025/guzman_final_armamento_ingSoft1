import Swal from 'sweetalert2';
import { validarFormulario } from '../funciones';

const FormLogin = document.getElementById('FormLogin');
const BtnIniciar = document.getElementById('BtnIniciar');
const btnText = document.getElementById('btn-text');
const btnSpinner = document.getElementById('btn-spinner');

const login = async (e) => {
    e.preventDefault();
    BtnIniciar.disabled = true;
    btnText.classList.add('d-none');
    btnSpinner.classList.remove('d-none');

    if (!validarFormulario(FormLogin, [''])) {
        await Swal.fire({
            title: "Campos vacíos",
            text: "Debe llenar todos los campos",
            icon: "warning",
            confirmButtonColor: '#667eea'
        });
        resetButton();
        return;
    }

    try {
        const body = new FormData(FormLogin);
        const url = '/guzman_final_armamento_ingSoft1/login'; 
        
        const config = {
            method: 'POST',
            body
        };

        const respuesta = await fetch(url, config);
        const data = await respuesta.json();
        const { codigo, mensaje, usuario } = data;

        if (codigo == 1) {
            await Swal.fire({
                title: '¡Bienvenido!',
                text: `${mensaje}. Hola ${usuario.nombre} ${usuario.apellido}`,
                icon: 'success',
                confirmButtonColor: '#667eea',
                timer: 2000,
                timerProgressBar: true
            });

            FormLogin.reset();
            setTimeout(() => {
                window.location.href = '/guzman_final_armamento_ingSoft1/dashboard';
            }, 1000);
            
        } else {
            await Swal.fire({
                title: '¡Error de acceso!',
                text: mensaje,
                icon: 'error',
                confirmButtonColor: '#dc3545'
            });
            resetButton();
        }

    } catch (error) {
        console.error('Error en login:', error);
        
        await Swal.fire({
            title: '¡Error de conexión!',
            text: 'No se pudo conectar con el servidor. Intente nuevamente.',
            icon: 'error',
            confirmButtonColor: '#dc3545'
        });
        resetButton();
    }
};

const resetButton = () => {
    BtnIniciar.disabled = false;
    btnText.classList.remove('d-none');
    btnSpinner.classList.add('d-none');
};

FormLogin.addEventListener('submit', login);

document.addEventListener('DOMContentLoaded', function() {
    const inputs = FormLogin.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                FormLogin.dispatchEvent(new Event('submit'));
            }
        });
    });
});