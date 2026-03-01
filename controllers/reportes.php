<?php
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Middleware;
use App\Models\EnviosModel;
use App\Models\PedidoModel;
use App\Models\ClienteModel;
use App\Models\ProductosModel;

function index() {
    if (!isset($_SESSION['user']['cedula'])){
        header('Location: ../home/index');
        exit;
    }
    $cedula = $_SESSION['user']['cedula'];
    $Middleware = new Middleware();
    $tipoUsuario = $Middleware->verificarTipoUsuario($cedula);
    //Vista para el emprendedor
    if ('emprendedor' == $tipoUsuario[0]) {
        $envio = new EnviosModel();
        $pedido = new PedidoModel();
        $totalEnvios = $envio->contarEnviosPorEmprendedor($cedula);
        $totalPedidos = $pedido->calcularTotalVentasPorEmprendedor($cedula);
        $totalClientes = $pedido->contarClientesPorEmprendedor($cedula);
        $totalProductosVendidos = $pedido->contarProductosVendidosPorEmprendedor($cedula);
        render('reportes/emprendedor', [
            'totalEnvios' => $totalEnvios,
            'totalPedidos' => $totalPedidos,
            'totalClientes' => $totalClientes,
            'totalProductosVendidos' => $totalProductosVendidos
        ]);

    }
    //Vista para el cliente
    else if ('cliente' == $tipoUsuario[0]) {
        header('Location: ../home/principal');
        exit;
    }
    //Vista para el administrador
    else {
        $envio = new EnviosModel();
        $pedido = new PedidoModel();
        $totalEnvios = $envio->contarEnviosTotales();
        $totalPedidos = $pedido->calcularTotalVentasGlobales();
        $totalClientes = $pedido->contarClientesGlobales();
        $totalProductosVendidos = $pedido->contarProductosVendidosGlobales();
        render('reportes/administrador', [
            'totalEnvios' => $totalEnvios,
            'totalPedidos' => $totalPedidos,
            'totalClientes' => $totalClientes,
            'totalProductosVendidos' => $totalProductosVendidos
        ]);
    }
}

function obtenerDatosGraficos() {
    try {
        $pedido = new PedidoModel(); // Ajusta al nombre de tu modelo
        $cedula = $_SESSION['user']['cedula'];

        $ventas = $pedido->obtenerVentasMensuales($cedula);
        $envios = $pedido->obtenerEnviosMensuales($cedula);
        $productos = $pedido->obtenerProductosMasVendidos($cedula);

        echo json_encode([
            'success' => true,
            'ventas' => $ventas,
            'envios' => $envios,
            'productos' => $productos
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function obtenerDatosGraficosGlobales() {
    try {
        $pedido = new PedidoModel(); // Ajusta al nombre de tu modelo
        $cedula = $_SESSION['user']['cedula'];

        $ventas = $pedido->obtenerVentasMensualesPorEmprendedor();
        $envios = $pedido->obtenerEnviosMensualesGlobal();
        $productos = $pedido->obtenerProductosMasVendidosGlobal();

        echo json_encode([
            'success' => true,
            'ventas' => $ventas,
            'envios' => $envios,
            'productos' => $productos
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
}

function reporteGlobal() {
    $data = json_decode(file_get_contents("php://input"), true);

    $reportType = $data['reportType'] ?? null;
    $dateFrom = $data['dateFrom'] ?? null;
    $dateTo = $data['dateTo'] ?? null;
    
    if (!$reportType) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan indicar el tipo de reporte']);
        exit;
    }

    $cedula = $_SESSION['user']['cedula'];
    // Switch según el tipo de reporte
    switch ($reportType) {
        case 'clientes':
            $clienteModel = new ClienteModel();
            $clientes = $clienteModel->getAll();
            echo json_encode([
                'success' => true,
                'data' => $clientes
            ]);
            exit;
            break;   
        case 'productos':
            $productoModel = new ProductosModel();
            $productos = $data['emprendedor'] == true ? 
                $productoModel->getProductosPorEmprendedor($cedula, $dateFrom, $dateTo) : 
                $productoModel->getAllProductos($dateFrom, $dateTo);
                 
            echo json_encode([
                'success' => true,
                'data' => $productos
            ]);
            exit;
            break;
        case 'ventas':
            $pedidoModel = new PedidoModel();
            $ventas = $data['emprendedor'] == true ? 
                $pedidoModel->mostrarPedidosPorEmprendedor($cedula, $dateFrom, $dateTo) :
                $pedidoModel->mostrarTodasLasVentas($dateFrom, $dateTo);
            echo json_encode([
                'success' => true,
                'data' => $ventas
            ]);
            exit;

        case 'envios':
            $envioModel = new EnviosModel();
            $envios = $envioModel->obtenerEnviosPorEmprendedor($cedula, $dateFrom, $dateTo);
            echo json_encode([
                'success' => true,
                'data' => $envios
            ]);
            exit;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Tipo de reporte no válido']);
            exit;
    }
}

function reporteAdmin() {
    $data = json_decode(file_get_contents("php://input"), true);

    $reportType = $data['reportType'] ?? null;
    $dateFrom = $data['dateFrom'] ?? null;
    $dateTo = $data['dateTo'] ?? null;
    if (!$reportType) {
        http_response_code(400);
        echo json_encode(['error' => 'Faltan indicar el tipo de reporte']);
        exit;
    }

    // Switch según el tipo de reporte
    switch ($reportType) {
        case 'clientes':
            $clienteModel = new ClienteModel();
            $clientes = $clienteModel->getAll();
            echo json_encode([
                'success' => true,
                'data' => $clientes
            ]);
            exit;
            break;   
        case 'productos':
            $productoModel = new ProductosModel();
            $productos = $productoModel->getAllProductos($dateFrom, $dateTo);         
            echo json_encode([
                'success' => true,
                'data' => $productos
            ]);
            exit;
            break;
        case 'ventas':
            $pedidoModel = new PedidoModel();
            $ventas = $pedidoModel->mostrarTodasLasVentas($dateFrom, $dateTo);
            echo json_encode([
                'success' => true,
                'data' => $ventas
            ]);
            exit;
        default:
            http_response_code(400);
            echo json_encode(['error' => 'Tipo de reporte no válido']);
            exit;
    }
}

//Reportes globales de exportacion
function exportar() {
    $data = json_decode(file_get_contents("php://input"), true);

    $format = $data['format'];
    $headers = $data['headers'] ?? [];
    $rows = $data['rows'] ?? [];

    if (!$format || empty($headers) || empty($rows)) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos insuficientes para exportar']);
        return;
    }

    switch ($format) {
        case 'pdf':
            $data = json_decode(file_get_contents('php://input'), true);
            $titulo = $data['titulo'] ?? 'Reporte General';
            if($data['emprendedor'] == true){
                generarPDFEmprendedor($headers, $rows, $titulo);
            }
            else{
                generarPDF($headers, $rows, $titulo);
            }
            break;

        case 'excel':
            generarExcel($headers, $rows);
            break;

        case 'csv':
            generarCSV($headers, $rows);
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Formato no válido']);
    }
}

function generarPDF($headers, $rows, $titulo = 'Reporte General') {
    $fechaGeneracion = date("d/m/Y H:i");

    // Ruta del logo del sistema
    $logoPath = 'public/img/imagengg.png';

    // Convertir logo a base64
    $logoHtml = '';
    if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoHtml = '<img src="data:image/png;base64,' . $logoData . '" style="height: 60px;">';
    }

    $html = '
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            @page {
                margin: 100px 50px 80px 50px;
            }
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 12px;
            }
            header {
                position: fixed;
                top: -80px;
                left: 0;
                right: 0;
                height: 60px;
            }
            .header-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .header-title {
                text-align: center;
                flex-grow: 1;
            }
            footer {
                position: fixed;
                bottom: -60px;
                left: 0;
                right: 0;
                height: 50px;
                text-align: center;
                font-size: 11px;
                color: #555;
            }
            .pagenum:before {
                content: counter(page);
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin-top: 5px;
            }
            thead {
                background-color: #f0f0f0;
            }
            th, td {
                border: 1px solid #333;
                padding: 6px;
                text-align: left;
                font-size: 11px;
            }
            h2 {
                margin: 0;
                font-size: 18px;
            }
            main {
                margin-top: 80px; /* espacio para no chocar con el header */
            }
        </style>
    </head>
    <body>
        <header>
            <div class="header-content">
                <div>' . $logoHtml . '</div>
                <div class="header-title">
                    <h2>' . htmlspecialchars($titulo) . '</h2>
                    <small>Generado el: ' . $fechaGeneracion . '</small>
                </div>
                <div style="width:60px;"></div>
            </div>
            <hr>
        </header>

        <footer>
            Página <span class="pagenum"></span>
        </footer>

        <main>
            <table>
                <thead>
                    <tr>';

    foreach ($headers as $header) {
        $html .= '<th>' . htmlspecialchars($header) . '</th>';
    }

    $html .= '</tr>
                </thead>
                <tbody>';

    foreach ($rows as $row) {
        $html .= '<tr>';
        foreach ($row as $cell) {
            $html .= '<td>' . htmlspecialchars($cell) . '</td>';
        }
        $html .= '</tr>';
    }

    $html .= '</tbody>
            </table>
        </main>
    </body>
    </html>';

    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=reporte.pdf");
    echo $dompdf->output();
}

function generarPDFEmprendedor($headers, $rows, $titulo = 'Reporte General') {
    $fechaGeneracion = date("d/m/Y H:i");

    // Ruta del logo del sistema
    $cedula = $_SESSION['user']['cedula'];
    $Middleware = new Middleware();
    $emprendimientoData = $Middleware->obtenerDatosEmprendimiento($cedula);
    $nombreEmprendimiento = $emprendimientoData['emprendimiento'] ?? 'Emprendimiento';
    $logoPath = $emprendimientoData['logo'] ?? null;


    // Convertir logo a base64
    $logoHtml = '';
    if (file_exists($logoPath)) {
        $logoData = base64_encode(file_get_contents($logoPath));
        $logoHtml = '<img src="data:image/png;base64,' . $logoData . '" style="height: 60px;">';
    }

    $html = '
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            @page {
                margin: 100px 50px 80px 50px;
            }
            body {
                font-family: DejaVu Sans, sans-serif;
                font-size: 12px;
            }
            header {
                position: fixed;
                top: -80px;
                left: 0;
                right: 0;
                height: 60px;
            }
            .header-content {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .header-title {
                text-align: center;
                flex-grow: 1;
            }
            footer {
                position: fixed;
                bottom: -60px;
                left: 0;
                right: 0;
                height: 50px;
                text-align: center;
                font-size: 11px;
                color: #555;
            }
            .pagenum:before {
                content: counter(page);
            }
            table {
                border-collapse: collapse;
                width: 100%;
                margin-top: 5px;
            }
            thead {
                background-color: #f0f0f0;
            }
            th, td {
                border: 1px solid #333;
                padding: 6px;
                text-align: left;
                font-size: 11px;
            }
            h2 {
                margin: 0;
                font-size: 18px;
            }
            main {
                margin-top: 80px; /* espacio para no chocar con el header */
            }
        </style>
    </head>
    <body>
        <header>
            <div class="header-content">
                <div>' . $logoHtml . '</div>
                <div class="header-title">
                    <h2>' . htmlspecialchars($titulo) . ' de ' . htmlspecialchars($nombreEmprendimiento) . '</h2>
                    <small>Generado el: ' . $fechaGeneracion . '</small>
                </div>
                <div style="width:60px;"></div>
            </div>
            <hr>
        </header>

        <footer>
            Página <span class="pagenum"></span>
        </footer>

        <main>
            <table>
                <thead>
                    <tr>';

    foreach ($headers as $header) {
        $html .= '<th>' . htmlspecialchars($header) . '</th>';
    }

    $html .= '</tr>
                </thead>
                <tbody>';

    foreach ($rows as $row) {
        $html .= '<tr>';
        foreach ($row as $cell) {
            $html .= '<td>' . htmlspecialchars($cell) . '</td>';
        }
        $html .= '</tr>';
    }

    $html .= '</tbody>
            </table>
        </main>
    </body>
    </html>';

    $options = new \Dompdf\Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'DejaVu Sans');

    $dompdf = new \Dompdf\Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'landscape');
    $dompdf->render();

    header("Content-Type: application/pdf");
    header("Content-Disposition: attachment; filename=reporte.pdf");
    echo $dompdf->output();
}

function generarExcel($headers, $rows) {
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezados
    $sheet->fromArray($headers, null, 'A1');
    // Filas
    $sheet->fromArray($rows, null, 'A2');

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="reporte.xlsx"');
    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
}

function generarCSV($headers, $rows) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="reporte.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, $headers);
    foreach ($rows as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
}
