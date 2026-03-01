<?php
use App\Models\notificacionModel;
use App\Models\ProductosModel;
function obtenerNotificaciones() {
    $Notificacion = new notificacionModel();
    $producto = new productosModel();

    $notificacioneStockBajo = $producto->getProductosStockBajo($_SESSION['user']['id_usuario']);
    $notificacionesStock = [];
    if (!empty($notificacioneStockBajo)) {
        foreach ($notificacioneStockBajo as $prod) {
            $notificacionesStock[] = [
                'id_notificacion' => 0,
                'mensaje' => 'El producto <strong>' . htmlspecialchars($prod['nombre']) . '</strong> tiene un stock bajo de <strong>' . intval($prod['stock']) . '</strong> unidades.',
                'fecha' => date('Y-m-d H:i:s'),
                'icono' => 'fas fa-exclamation-triangle',
                'color' => 'text-danger',
                'status' => 2,
                'url' => '/productos/producto',
                'tipo' => 'stock'
            ];
        }
    }

    // Notificaciones generales de la base de datos
    $notificacionesDB = $Notificacion->obtenerNotificacionesPorUsuario($_SESSION['user']['id_usuario']);

    $notificaciones = array_merge(
        $notificacionesStock ?: [],
        $notificacionesDB ?: []
    );

    header('Content-Type: application/json');
    if (!empty($notificaciones)) {
        echo json_encode($notificaciones);
    } else {
        echo json_encode(['error' => 'No se encontraron notificaciones']);
    }
}

function marcarLeida() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Notificacion = new notificacionModel();

        $id = $_POST['id_notificacion'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID de notificación no proporcionado.']);
            exit;
        }

        $resultado = $Notificacion->marcarComoLeida($id);
        echo json_encode(['success' => $resultado]);
        exit;
    }
}

function eliminar() {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $Notificacion = new notificacionModel();

        $id = $_POST['id_notificacion'] ?? null;
        if (!$id) {
            echo json_encode(['success' => false, 'message' => 'ID de notificación no proporcionado.']);
            exit;
        }

        $resultado = $Notificacion->eliminar($id);
        if ($resultado) {
            echo json_encode(['success' => true, 'message' => 'Notificación eliminada correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar notificación']);
        }
        exit;
    }
}

?>