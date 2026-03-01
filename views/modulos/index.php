<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Modulos</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/roles.css">
</head>
<body>
    <?php
        include "views/navbar.php";
    ?>
    <br>
    <br>

    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header">
                        <h2 class="mb-0">Gestión de Modulos - PROJUMI</h2>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-4">
                            <div class="input-group buscar-input">
                                <input type="text" class="form-control" placeholder="Buscar empresa..." id="buscarEmpresa">
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
                                            <th>Nombre</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($data as $modulo): ?>
                                            <tr>
                                                <td><?php echo $modulo['id_modulo']; ?></td>
                                                <td><?php echo htmlspecialchars($modulo['nombre']); ?></td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {           
            // Búsqueda simple
            $('#btnBuscar').click(function() {
                var searchText = $('#buscarEmpresa').val().toLowerCase();
                $('table tbody tr').each(function() {
                    var rowText = $(this).text().toLowerCase();
                    $(this).toggle(rowText.indexOf(searchText) > -1);
                });
            });
           
            $('#buscarEmpresa').keyup(function(e) {
                if (e.keyCode === 13) {
                    $('#btnBuscar').click();
                }
            });
        });
    </script>
</body>
</html>