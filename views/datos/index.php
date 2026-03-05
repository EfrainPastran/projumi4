<?php if (!isset($_SESSION['user']['cedula'])): ?>
    <script>window.location.href = '<?= APP_URL ?>home/index';</script>
    <?php exit(); ?>
<?php endif; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Mis Métodos de pago</title>
    <?php include 'views/componentes/head.php'; ?>
    <link rel="icon" type="image/x-icon" href="/images/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/roles.css">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    include "views/navbar.php";
    
    // Mostrar mensajes flash
    if (isset($_SESSION['flash_success'])) {
        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                '.$_SESSION['flash_success'].'
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['flash_success']);
    }
    
    if (isset($_SESSION['flash_error'])) {
        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                '.$_SESSION['flash_error'].'
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>';
        unset($_SESSION['flash_error']);
    }
    ?>

    <br>
    <br>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="mb-0">Gestión de Datos Bancarios - PROJUMI</h2>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarCuentaModal">
                                <i class="fas fa-plus me-2"></i>Nueva Cuenta
                            </button>
                            <div class="input-group buscar-input">
                                <input type="text" class="form-control" placeholder="Buscar cuenta..." id="buscarCuenta">
                                <button class="btn btn-outline-secondary" type="button" id="btnBuscar">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>

                        <div class="table-container">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Banco</th>
                                            <th>Número de Cuenta</th>
                                            <th>Teléfono</th>
                                            <th>Correo</th>
                                            <th>Método de Pago</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($datos_cuenta as $cuenta): ?>
                                            <tr>
                                                <td><?php echo $cuenta['id_datos_cuenta']; ?></td>
                                                <td><?php echo htmlspecialchars($cuenta['banco']); ?></td>
                                                <td><?php echo htmlspecialchars($cuenta['numero_cuenta']); ?></td>
                                                <td><?php echo htmlspecialchars($cuenta['telefono']); ?></td>
                                                <td><?php echo htmlspecialchars($cuenta['correo']); ?></td>
                                                <td><?php echo htmlspecialchars($cuenta['metodo_pago']); ?></td>
                                                <td class="acciones-btn">
                                                    <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editarCuentaModal" 
                                                        data-id="<?php echo $cuenta['id_datos_cuenta']; ?>"
                                                        data-banco="<?php echo htmlspecialchars($cuenta['banco']); ?>"
                                                        data-numero="<?php echo htmlspecialchars($cuenta['numero_cuenta']); ?>"
                                                        data-telefono="<?php echo htmlspecialchars($cuenta['telefono']); ?>"
                                                        data-correo="<?php echo htmlspecialchars($cuenta['correo']); ?>"
                                                        data-metodo="<?php echo $metodos_pago['metodo_pago']; ?>">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#eliminarCuentaModal"
                                                        data-id="<?php echo $cuenta['id_datos_cuenta']; ?>"
                                                        data-banco="<?php echo htmlspecialchars($cuenta['banco']); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Agregar Cuenta -->
    <div class="modal fade" id="agregarCuentaModal" tabindex="-1" aria-labelledby="agregarCuentaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="agregarCuentaModalLabel">Agregar Nueva Cuenta Bancaria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formAgregar" action="<?php echo APP_URL; ?>/datos/register" method="post">
                    <div class="modal-body">
                        <input type="hidden" name="fk_emprendedor" value="<?php echo $id_emprendedor; ?>">
                        <div class="mb-3">
                            <label for="banco" class="form-label">Banco</label>
                            <select class="form-select select2-bancos" id="banco" name="banco" required>
                                <option value="">Seleccione un banco</option>
                                <!-- Los bancos se cargarán dinámicamente con JS -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="numero_cuenta" class="form-label">Número de Cuenta</label>
                            <input type="text" class="form-control" id="numero_cuenta" name="numero_cuenta" required data-tipo="numeros">
                        </div>
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" required>
                        </div>
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" required data-min="5" data-max="45">
                        </div>
                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select class="form-select select2-metodos" id="metodo_pago" name="fk_metodo_pago" required>
                                <option value="">Seleccione un método</option>
                                <?php foreach ($metodo_pago as $metodo): ?>
                                    <option value="<?php echo $metodo['id_metodo_pago']; ?>">
                                        <?php echo htmlspecialchars($metodo['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Editar Cuenta -->
    <div class="modal fade" id="editarCuentaModal" tabindex="-1" aria-labelledby="editarCuentaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarCuentaModalLabel">Editar Cuenta Bancaria</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="formEditar" action="<?php echo APP_URL; ?>/datos/update" method="post">
                    <input type="hidden" id="idCuentaEditar" name="id_datos_cuenta">
                    <input type="hidden" name="fk_emprendedor" value="<?php echo $id_emprendedor; ?>">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="bancoEditar" class="form-label">Banco</label>
                            <input type="text" class="form-control" id="bancoEditar" name="banco" readonly>
                            <!--<select class="form-select select2-bancos" id="bancoEditar" name="banco" required>-->
                                <!-- Los bancos se cargarán dinámicamente con JS -->
                            <!--</select>-->
                        </div>
                        <div class="mb-3">
                            <label for="numeroCuentaEditar" class="form-label">Número de Cuenta</label>
                            <input type="text" class="form-control" id="numeroCuentaEditar" name="numero_cuenta" required data-tipo="numeros">
                        </div>
                        <div class="mb-3">
                            <label for="telefonoEditar" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefonoEditar" name="telefono" required data-tipo="numeros">
                        </div>
                        <div class="mb-3">
                            <label for="correoEditar" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correoEditar" name="correo" required>
                        </div>
                        <div class="mb-3">
                            <label for="metodoPagoEditar" class="form-label">Método de Pago</label>
                            <select class="form-select select2-metodos" id="metodoPagoEditar" name="fk_metodo_pago" required>
                            <?php foreach ($metodo_pago as $metodo): ?>
                                    <option value="<?php echo $metodo['id_metodo_pago']; ?>">
                                        <?php echo htmlspecialchars($metodo['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Actualizar Cuenta</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Eliminar Cuenta -->
    <div class="modal fade" id="eliminarCuentaModal" tabindex="-1" aria-labelledby="eliminarCuentaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eliminarCuentaModalLabel">Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="<?php echo APP_URL; ?>/datos/delete" method="post">
                    <input type="hidden" id="idCuentaEliminar" name="id">
                    <div class="modal-body">
                        <p>¿Estás seguro que deseas eliminar esta cuenta bancaria? Esta acción no se puede deshacer.</p>
                        <p class="fw-bold">Banco: <span id="bancoCuentaEliminar"></span></p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-danger">Eliminar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="<?php echo APP_URL; ?>/public/js/validaciones.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof Validaciones !== 'undefined') {
                Validaciones.init('#formAgregar');
                Validaciones.init('#formEditar');
            }
        });
    </script>  
    <script>
        // Lista completa de bancos de Venezuela
        const bancosVenezuela = [
            "Banesco",
            "Banco de Venezuela",
            "Banco Mercantil",
            "BBVA Provincial",
            "Banco Bicentenario",
            "Banco del Tesoro",
            "Banco Nacional de Crédito (BNC)",
            "Banco Occidental de Descuento (BOD)",
            "Banco Plaza",
            "Banco Caroní",
            "Banco Exterior",
            "Banco Sofitasa",
            "Banco Fondo Común",
            "Banco Venezolano de Crédito (BVC)",
            "Banco Agrícola de Venezuela",
            "Bancrecer",
            "Bancaribe",
            "Banplus",
            "Banco Activo",
            "Banco del Sur",
            "Banco de la Gente Emprendedora (BANGENTE)",
            "Banco Internacional de Desarrollo",
            "Banco de Comercio Exterior (BANCOEX)",
            "Banco de la Fuerza Armada Nacional Bolivariana (BANFANB)",
            "Banco de Desarrollo Económico y Social de Venezuela (BANDES)",
            "Banco del Pueblo Soberano",
            "Mi Banco",
            "100% Banco",
            "Banco Bicentenario del Pueblo"
        ];

        $(document).ready(function() {
            // Inicializar Select2
            $('.select2-bancos').select2({
                placeholder: "Seleccione un banco",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#agregarCuentaModal')
            });

            // Inicializar Select2 para el modal de agregar
    $('#agregarCuentaModal').on('shown.bs.modal', function() {
        $('#metodo_pago').select2({
            placeholder: "Seleccione un método de pago",
            allowClear: true,
            dropdownParent: $('#agregarCuentaModal'),
            width: '100%'
        });
    });

    // Limpiar Select2 cuando se cierra el modal
    $('#agregarCuentaModal').on('hidden.bs.modal', function() {
        $('#metodo_pago').val(null).trigger('change');
    });

            // Cargar bancos en los selects
            function cargarBancos(selectId, bancoSeleccionado = '') {
                const $select = $(selectId);
                $select.empty();
                $select.append('<option value="">Seleccione un banco</option>');
                
                bancosVenezuela.forEach(banco => {
                    const option = new Option(banco, banco);
                    if (banco === bancoSeleccionado) {
                        $(option).attr('selected', 'selected');
                    }
                    $select.append(option);
                });
                
                $select.trigger('change');
            }

            function mostrarAlerta(campo, mensaje, esError = true) {
        // Eliminar alertas previas
        $(campo).next('.invalid-feedback').remove();
        $(campo).removeClass('is-invalid is-valid');
        
        if (mensaje) {
            const feedback = `<div class="invalid-feedback">${mensaje}</div>`;
            $(campo).addClass(esError ? 'is-invalid' : 'is-valid')
                   .after(feedback);
        }
    }

    // Validación en tiempo real para cada campo
    $('select[name="banco"]').on('change', function() {
        const valido = $(this).val() !== "";
        mostrarAlerta(this, valido ? "" : "Seleccione un banco", !valido);
    });

    $('select[name="fk_metodo_pago"]').on('change', function() {
        const valido = $(this).val() !== "";
        mostrarAlerta(this, valido ? "" : "Seleccione un método de pago", !valido);
    });

    $('input[name="correo"]').on('input', function() {
        const correo = $(this).val().trim();
        const valido = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo) && !/[<>"'`;=]/.test(correo);
        
        if (correo === "") {
            mostrarAlerta(this, "");
        } else {
            mostrarAlerta(this, valido ? "Correo válido" : "Ingrese un correo válido sin caracteres especiales", !valido);
        }
    });

    // Validación mejorada para número de cuenta
    $('input[name="numero_cuenta"]').on('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 20);
        const valido = this.value.length === 20;
        
        if (this.value === "") {
            mostrarAlerta(this, "");
        } else {
            mostrarAlerta(this, valido ? "Número válido" : "Deben ser 20 dígitos", !valido);
        }
    });

    // Validación mejorada para teléfono
    $('input[name="telefono"]').on('input', function() {
        this.value = this.value.replace(/\D/g, '');
        if (!this.value.startsWith('04')) {
            this.value = '04' + this.value.replace(/^04/, '');
        }
        this.value = this.value.slice(0, 11);
        
        const valido = this.value.length === 11;
        if (this.value === "") {
            mostrarAlerta(this, "");
        } else {
            mostrarAlerta(this, valido ? "Teléfono válido" : "Debe comenzar con 04 y tener 11 dígitos", !valido);
        }
    });

    // Función de validación general actualizada
    function validarFormulario(form) {
        let valido = true;
        const $form = $(form);
        
        // Validar banco (solo en agregar)
        if (form.id === 'agregarCuentaModal') {
            const banco = $form.find('select[name="banco"]').val();
            if (!banco) {
                mostrarAlerta($form.find('select[name="banco"]')[0], "Seleccione un banco", true);
                valido = false;
            }
        }
        
        // Validar número de cuenta
        const cuenta = $form.find('input[name="numero_cuenta"]').val().trim();
        if (!/^\d{20}$/.test(cuenta)) {
            mostrarAlerta($form.find('input[name="numero_cuenta"]')[0], "Deben ser 20 dígitos", true);
            valido = false;
        }
        
        // Validar teléfono
        const telefono = $form.find('input[name="telefono"]').val().trim();
        if (!/^04\d{9}$/.test(telefono)) {
            mostrarAlerta($form.find('input[name="telefono"]')[0], "Debe comenzar con 04 y tener 11 dígitos", true);
            valido = false;
        }
        
        // Validar correo
        const correo = $form.find('input[name="correo"]').val().trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
            mostrarAlerta($form.find('input[name="correo"]')[0], "Ingrese un correo válido", true);
            valido = false;
        } else if (/[<>"'`;=]/.test(correo)) {
            mostrarAlerta($form.find('input[name="correo"]')[0], "No se permiten caracteres especiales", true);
            valido = false;
        }
        
        // Validar método de pago
        const metodoPago = $form.find('select[name="fk_metodo_pago"]').val();
        if (!metodoPago) {
            mostrarAlerta($form.find('select[name="fk_metodo_pago"]')[0], "Seleccione un método de pago", true);
            valido = false;
        }
        
        return valido;
    }

    // Mostrar errores con SweetAlert2
    function mostrarError(mensaje) {
        Swal.fire({
            icon: 'error',
            title: 'Error de validación',
            text: mensaje,
            confirmButtonColor: '#3085d6',
        });
    }

    // Validación en tiempo real
    $('input[name="numero_cuenta"]').on('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 20);
    });
    
    $('input[name="telefono"]').on('input', function() {
        this.value = this.value.replace(/\D/g, '');
        if (!this.value.startsWith('04')) {
            this.value = '04' + this.value.replace(/^04/, '');
        }
        this.value = this.value.slice(0, 11);
    });
    
    $('input[name="correo"]').on('input', function() {
        this.value = this.value.replace(/[<>"'`;=]/g, '');
    });

            // Modal de agregar
            $('#agregarCuentaModal').on('show.bs.modal', function() {
                cargarBancos('#banco');
            });

            // Modal de edición
            $('#editarCuentaModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var banco = button.data('banco');
                var numero = button.data('numero');
                var telefono = button.data('telefono');
                var correo = button.data('correo');
                var metodo = button.data('metodo');
                
                var modal = $(this);
                modal.find('#idCuentaEditar').val(id);
                modal.find('#bancoEditar').val(banco);
                modal.find('#numeroCuentaEditar').val(numero);
                modal.find('#telefonoEditar').val(telefono);
                modal.find('#correoEditar').val(correo);
                modal.find('#metodoPagoEditar').val(metodo).trigger('change');
                
                // Cargar bancos con el seleccionado
                cargarBancos('#bancoEditar', banco);
            });
            
            // Modal de eliminación
            $('#eliminarCuentaModal').on('show.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                var id = button.data('id');
                var banco = button.data('banco');
                
                var modal = $(this);
                modal.find('#idCuentaEliminar').val(id);
                modal.find('#bancoCuentaEliminar').text(banco);
            });

            // Validar al enviar formularios
            $('#agregarCuentaModal form, #editarCuentaModal form').on('submit', function(e) {
                if (!validarFormulario(this)) {
                    e.preventDefault();
                }
            });
            
            // Búsqueda simple
            $('#btnBuscar').click(function() {
                var searchText = $('#buscarCuenta').val().toLowerCase();
                $('table tbody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.indexOf(searchText) > -1);
                });
            });
            
            $('#buscarCuenta').keyup(function(e) {
                if (e.keyCode === 13) {
                    $('#btnBuscar').click();
                }
            });
        });
    </script>
</body>
</html>