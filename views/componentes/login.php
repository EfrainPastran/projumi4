<!-- Modal de Login -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm">
                
                        <div class="mb-3">
                            <label for="loginCedula" class="form-label">Cedula</label>
                            <input type="text" class="form-control" id="ced" name="cedula" required>
                           <span id="scedula" class="small-text"></span>
                        </div>
                        <div class="mb-3">
                            <label for="loginPassword" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="pass" name="password" required>
                            <span id="spass" class="small-text"></span>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="rememberMe">
                            <label class="form-check-label" for="rememberMe">Recordarme</label>
                        </div>
                        <input type="submit" class="btn btn-primary w-100" name="iniciar" value="Iniciar Sesión">
                    </form>
                    <div id="loginError" class="alert alert-danger mt-2 d-none" role="alert"></div>
                    <div class="text-center mt-3">
                        <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                    </div>
                    <hr>
                    <div class="text-center">
                        <p>¿No tienes una cuenta? <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#registroClienteModal" data-bs-dismiss="modal">Regístrate</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal de Registro de Cliente -->
<div class="modal fade" id="registroClienteModal" tabindex="-1" aria-labelledby="registroClienteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable"> <!-- modal-lg para mayor ancho -->
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
              <!-- Sección Información Personal -->
              <div class="form-section">
                <h5 class="section-title">Información Personal</h5>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="cedula" class="form-label required-field">Cédula</label>
                    <input type="number" class="form-control" id="cedula" name="cedula" required>
                  </div>
                  <div class="col-md-6">
                    <label for="fecha_nacimiento" class="form-label required-field">Fecha de Nacimiento</label>
                    <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required>
                  </div>
                  <div class="col-md-6">
                    <label for="nombre" class="form-label required-field">Nombres</label>
                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                  </div>
                  <div class="col-md-6">
                    <label for="apellido" class="form-label required-field">Apellidos</label>
                    <input type="text" class="form-control" id="apellido" name="apellido" required>
                  </div>
                  <div class="col-md-6">
                    <label for="telefono" class="form-label required-field">Teléfono</label>
                    <input type="tel" class="form-control" id="telefono" name="telefono" required>
                  </div>
                  <div class="col-md-6">
                    <label for="edad" class="form-label">Edad</label>
                    <input type="number" class="form-control" id="edad" name="edad" readonly disabled>
                  </div>
                </div>
              </div>

              <!-- Sección Contacto -->
              <div class="form-section mt-4">
                <h5 class="section-title">Información de Contacto</h5>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="email" class="form-label required-field">Correo Electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                    <div class="form-text">Ejemplo: usuario@dominio.com</div>
                  </div>
                  <div class="col-md-6">
                    <label for="direccion" class="form-label required-field">Dirección</label>
                    <textarea class="form-control" id="direccion" name="direccion" rows="1" required></textarea>
                  </div>
                </div>
              </div>

              <!-- Sección Seguridad -->
              <div class="form-section mt-4">
                <h5 class="section-title">Seguridad</h5>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label for="password" class="form-label required-field">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                    <div class="form-text">Mínimo 8 caracteres</div>
                  </div>
                  <div class="col-md-6">
                    <label for="confirm_pass" class="form-label required-field">Confirmar Contraseña</label>
                    <input type="password" class="form-control" id="confirm_pass" name="confirm_pass" required>
                  </div>
                </div>
              </div>

              <!-- Sección Términos -->
              <div class="form-section mt-4">
                <div class="form-check mb-3">
                  <input class="form-check-input" type="checkbox" id="acceptTerms" required>
                  <label class="form-check-label" for="acceptTerms">
                    Acepto los <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">términos y condiciones</a>
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
              <p>¿Ya tienes una cuenta? <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#loginModal" data-bs-dismiss="modal">Inicia Sesión</a></p>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
</div>