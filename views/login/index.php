<div class="row justify-content-center">
    <form class="col-lg-4 border rounded shadow p-4 bg-light" id="FormLogin">
        <h3 class="text-center mb-4"><b>INICIO DE SESIÓN</b></h3>
        <div class="text-center mb-4">
            <img src="<?= asset('./images/login.jpg') ?>" alt="Logo" width="200px" class="img-fluid rounded-circle">
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="usuario_dpi" class="form-label">Ingrese su DPI</label>
                <input type="text" name="usuario_dpi" id="usuario_dpi" class="form-control" 
                       placeholder="Ingresa tu DPI (13 dígitos)" maxlength="13" pattern="[0-9]{13}">
            </div>
        </div>
        <div class="row mb-3">
            <div class="col">
                <label for="usuario_password" class="form-label">Contraseña</label>
                <input type="password" name="usuario_password" id="usuario_password" class="form-control" 
                       placeholder="Ingresa tu contraseña">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <button type="submit" class="btn btn-primary w-100 btn-lg" id="BtnIniciar">
                    Iniciar sesión
                </button>
            </div>
        </div>
    </form>
</div>

<script src="<?= asset('build/js/login/login.js') ?>"></script>