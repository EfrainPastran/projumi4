<script>
window.APP_URL = "<?php echo APP_URL; ?>";
</script>

<div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="loginModalLabel">Iniciar Sesion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="loginForm" novalidate>
                    <div class="mb-3">
                        <label for="ced" class="form-label">Cedula</label>
                        <input type="text" class="form-control" id="ced" name="cedula" autocomplete="username" required>
                        <span id="scedula" class="small-text"></span>
                    </div>
                    <div class="mb-3">
                        <label for="pass" class="form-label">contraseña</label>
                        <input type="password" class="form-control" id="pass" name="password" autocomplete="current-password" required>
                        <span id="spass" class="small-text"></span>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="rememberMe">
                        <label class="form-check-label" for="rememberMe">Recordarme</label>
                    </div>
                    <input type="submit" class="btn btn-primary w-100" name="iniciar" value="Iniciar Sesion">
                </form>
                <div id="loginError" class="alert alert-danger mt-2 d-none" role="alert"></div>
                <div class="text-center mt-3">
                    <a href="#" class="text-decoration-none" data-password-reset-trigger="true">&iquest;Olvidaste tu contrase&ntilde;a?</a>
                </div>
                <hr>
                <div class="text-center">
                    <p>&iquest;No tienes una cuenta? <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#registroClienteModal" data-bs-dismiss="modal">Registrate</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="passwordResetModal" tabindex="-1" aria-labelledby="passwordResetModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="passwordResetModalLabel">Recuperar contraseña</h5>
                    <small class="text-white" id="passwordResetDescription">Te enviaremos un código al correo asociado a la cedula.</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="passwordResetAlert" class="alert d-none" role="alert"></div>
                <div id="passwordResetEmailHint" class="small text-muted mb-3 d-none"></div>

                <form id="passwordResetRequestForm" data-reset-step="1" novalidate>
                    <div class="mb-3">
                        <label for="passwordResetCedula" class="form-label">Cedula</label>
                        <input type="text" class="form-control" id="passwordResetCedula" maxlength="10" inputmode="numeric" autocomplete="username" required>
                        <div class="form-text">Usaremos esta cedula para buscar el correo registrado.</div>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="passwordResetRequestButton">Enviar codigo</button>
                    </div>
                </form>

                <form id="passwordResetVerifyForm" data-reset-step="2" class="d-none" novalidate>
                    <div class="mb-3">
                        <label for="passwordResetCodigo" class="form-label">Código de verificación</label>
                        <input type="text" class="form-control" id="passwordResetCodigo" maxlength="6" inputmode="numeric" autocomplete="one-time-code" required>
                        <div class="form-text">Ingresa el código de 6 dígitos enviado a tu correo.</div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary" id="passwordResetVerifyButton">Verificar código</button>
                        <button type="button" class="btn btn-outline-secondary" id="passwordResetResendButton">Reenviar código</button>
                    </div>
                </form>

                <form id="passwordResetCompleteForm" data-reset-step="3" class="d-none" novalidate>
                    <div class="mb-3">
                        <label for="passwordResetPassword" class="form-label">Nueva contraseña</label>
                        <input type="password" class="form-control" id="passwordResetPassword" autocomplete="new-password" required>
                        <div class="form-text">La contraseña debe tener al menos 8 caracteres.</div>
                    </div>
                    <div class="mb-3">
                        <label for="passwordResetPasswordConfirm" class="form-label">Confirmar contraseña</label>
                        <input type="password" class="form-control" id="passwordResetPasswordConfirm" autocomplete="new-password" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary" id="passwordResetCompleteButton">Guardar nueva contraseña</button>
                    </div>
                </form>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <button type="button" class="btn btn-link px-0 d-none" id="passwordResetChangeCedulaButton">Cambiar cédula</button>
                    <button type="button" class="btn btn-link px-0" id="passwordResetCloseToLoginButton">Volver al inicio de sesión</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="registroClienteModal" tabindex="-1" aria-labelledby="registroClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registroClienteModalLabel">Registro de Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="container">
                    <div class="register-container">
                        <div class="form-header">
                            <p class="text-muted">Complete todos los campos obligatorios</p>
                        </div>

                        <form id="formRegistrarUsuarioCliente" method="POST">
                            <div class="form-section">
                                <h5 class="section-title">Informacion Personal</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="cedula" class="form-label required-field">Cedula</label>
                                        <input type="number" class="form-control" id="cedula" name="cedula" required>
                                        <div class="invalid-feedback" id="error-cedula"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="fecha_nacimiento" class="form-label required-field">Fecha de Nacimiento</label>
                                        <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                                        <div class="invalid-feedback" id="error-fecha_nacimiento"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="nombre" class="form-label required-field">Nombres</label>
                                        <input type="text" class="form-control" id="nombre" name="nombre" required>
                                        <div class="invalid-feedback" id="error-nombre"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="apellido" class="form-label required-field">Apellidos</label>
                                        <input type="text" class="form-control" id="apellido" name="apellido" required>
                                        <div class="invalid-feedback" id="error-apellido"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="telefono" class="form-label required-field">Telefono</label>
                                        <input type="tel" class="form-control" id="telefono" name="telefono" required>
                                        <div class="invalid-feedback" id="error-telefono"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="edad" class="form-label">Edad</label>
                                        <input type="number" class="form-control" id="edad" name="edad" readonly disabled>
                                        <div class="invalid-feedback" id="error-edad"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mt-4">
                                <h5 class="section-title">Informacion de Contacto</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="email" class="form-label required-field">Correo Electronico</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                        <div class="invalid-feedback" id="error-email"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="direccion" class="form-label required-field">Direccion</label>
                                        <textarea class="form-control" id="direccion" name="direccion" rows="1" required></textarea>
                                        <div class="invalid-feedback" id="error-direccion"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mt-4">
                                <h5 class="section-title">Seguridad</h5>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="password" class="form-label required-field">contraseña</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                        <div class="invalid-feedback" id="error-password"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="confirm_pass" class="form-label required-field">Confirmar contraseña</label>
                                        <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" required>
                                        <div class="invalid-feedback" id="error-confirm_pass"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mt-4">
                                <div class="form-check mb-3">
                                    <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                                    <label class="form-check-label" for="acceptTerms">
                                        Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">terminos y condiciones</a>
                                    </label>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Registrarse
                                </button>
                            </div>
                        </form>

                        <div class="text-center mt-3">
                            <p>&iquest;Ya tienes una cuenta? <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Inicia Sesion</a></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
