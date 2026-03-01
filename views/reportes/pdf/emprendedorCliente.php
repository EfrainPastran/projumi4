<?php if (!isset($clientes)) die("Clientes no definidos"); ?>

<?php
// Variables disponibles: $clientes, $nombreEmprendimiento, $logoPath
ob_start();
?>

<html>
<head>
    <style>
        @page {
            margin: 100px 50px 80px 50px;
        }
        header {
            position: fixed;
            top: -80px;
            left: 0;
            right: 0;
            height: 80px;
            text-align: center;
        }
        footer {
            position: fixed;
            bottom: -60px;
            left: 0;
            right: 0;
            height: 50px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .pagenum:before {
            content: counter(page);
        }
        table {
            border-collapse: collapse;
            width: 100%;
            font-size: 14px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: left;
        }
        thead {
            background-color: #ddd;
        }
    </style>
</head>
<body>
<header>
    <div style="display: flex; align-items: center; justify-content: space-between;">
        <div>
            <?php if ($logoPath && file_exists($logoPath)) {
                $logoData = base64_encode(file_get_contents($logoPath));
                echo '<img src="data:image/png;base64,' . $logoData . '" style="height: 60px;">';
            } ?>
        </div>
        <div><h2><?= htmlspecialchars($nombreEmprendimiento) ?></h2></div>
        <div style="width: 60px;"></div>
    </div>
    <hr>
    <h3 style="margin: 5px 0;">Reporte de Clientes</h3>
</header>

<footer>
    Página <span class="pagenum"></span> | Generado el: <?= date("d/m/Y H:i") ?>
</footer>

<main>
    <table>
        <thead>
        <tr>
            <th>Cédula</th>
            <th>Nombre y Apellido</th>
            <th>Correo</th>
            <th>Dirección</th>
            <th>Teléfono</th>
            <th>Fecha de Nacimiento</th>
            <th>Estatus</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($clientes as $cliente): ?>
            <tr>
                <td><?= htmlspecialchars($cliente['cedula']) ?></td>
                <td><?= htmlspecialchars($cliente['nombre'] . ' ' . $cliente['apellido']) ?></td>
                <td><?= htmlspecialchars($cliente['correo']) ?></td>
                <td><?= htmlspecialchars($cliente['direccion']) ?></td>
                <td><?= htmlspecialchars($cliente['telefono']) ?></td>
                <td><?= date('d-m-Y', strtotime($cliente['fecha_nacimiento'])) ?></td>
                <td><?= $cliente['estatus'] == 1 ? 'Activo' : 'Inactivo' ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</main>
</body>
</html>

<?php
$html = ob_get_clean();
