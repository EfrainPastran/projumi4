<?php
use App\Models\mantenimientomodel;
use App\Middleware;
function index() {
    if (!isset($_SESSION['user']['cedula'])) {
        header('Location: ../home/index');
        exit;
    }
    $cedula = $_SESSION['user']['cedula'];
    $middleware = new Middleware();
    $tipoUsuario = $middleware->verificarTipoUsuario($cedula);
    
    if ('emprendedor' == $tipoUsuario[0] || 'cliente' == $tipoUsuario[0]) {
        header('Location: ../home/index');
        exit;
    }


    render('mantenimiento/index', ['tipoUsuario' => $tipoUsuario]);
}

function backup(){
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userModel = new mantenimientomodel();
    // Generar backup para la base de datos principal
    $backupFileMain = $userModel->generateBackup('main');
    
    // Generar backup para la base de datos projumi
    $backupFileProjumi = $userModel->generateBackup('projumi');
    
    if ($backupFileMain && $backupFileProjumi) {
        // Crear un archivo ZIP que contenga ambos backups
        $zip = new ZipArchive();
        $zipFileName = 'backups/backup_dual_' . date('Y-m-d_H-i-s') . '.zip';
        
        if ($zip->open($zipFileName, ZipArchive::CREATE)) {
            $zip->addFile($backupFileMain, 'backup_main_' . date('Y-m-d') . '.sql');
            $zip->addFile($backupFileProjumi, 'backup_projumi_' . date('Y-m-d') . '.sql');
            $zip->close();
            
            // Forzar descarga del archivo ZIP
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . basename($zipFileName) . '"');
            header('Content-Length: ' . filesize($zipFileName));
            readfile($zipFileName);
            
            // Eliminar archivos temporales
            unlink($backupFileMain);
            unlink($backupFileProjumi);
            unlink($zipFileName);
            exit;
        } else {
            echo "Error al crear el archivo ZIP.";
        }
    } else {
        echo "Error al generar los backups.";
    }
}


}

function restore() {
    // Verificar si se ha enviado el formulario y el archivo
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['restoreFile'])) {
        $file = $_FILES['restoreFile'];
        
        // Verificar si el archivo es un ZIP
        if ($file['type'] !== 'application/zip' && pathinfo($file['name'], PATHINFO_EXTENSION) !== 'zip') {
            echo "Error: Por favor, suba un archivo ZIP válido.";
            return;
        }

        $userModel = new mantenimientomodel();
        $uploadDir = 'temp_restore/';
        
        // Crear directorio temporal para la restauración
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $zipPath = $uploadDir . $file['name'];
        
        // Mover el archivo subido al directorio temporal
        if (move_uploaded_file($file['tmp_name'], $zipPath)) {
            $zip = new ZipArchive();
            if ($zip->open($zipPath) === TRUE) {
                // Extraer el contenido (los dos archivos SQL) en el directorio temporal
                $zip->extractTo($uploadDir);
                $zip->close();
                
                // Nombres de archivos esperados (asumiendo que los nombras consistentemente)
                // Es mejor iterar los archivos extraídos si el nombre no es fijo.
                // Aquí usamos nombres comunes basados en tu función de backup:
                $files = scandir($uploadDir);
                $mainFile = null;
                $projumiFile = null;

                foreach ($files as $f) {
                    if (strpos($f, 'backup_main_') !== false && pathinfo($f, PATHINFO_EXTENSION) === 'sql') {
                        $mainFile = $uploadDir . $f;
                    }
                    if (strpos($f, 'backup_projumi_') !== false && pathinfo($f, PATHINFO_EXTENSION) === 'sql') {
                        $projumiFile = $uploadDir . $f;
                    }
                }

                $restauracionExitosa = true;
                $mensaje = [];

                // 1. Restaurar base de datos 'main'
                if ($mainFile) {
                    try {
                        $userModel->restoreDatabase($mainFile, 'main');
                        $mensaje[] = "Base de datos 'main' restaurada exitosamente.";
                    } catch (Exception $e) {
                        $restauracionExitosa = false;
                        $mensaje[] = "ERROR al restaurar 'main': " . $e->getMessage();
                    }
                } else {
                    $mensaje[] = "ADVERTENCIA: No se encontró el archivo SQL para 'main'.";
                }
                
                // 2. Restaurar base de datos 'projumi'
                if ($restauracionExitosa && $projumiFile) { // Solo si 'main' fue exitosa o no se procesó
                    try {
                        $userModel->restoreDatabase($projumiFile, 'projumi');
                        $mensaje[] = "Base de datos 'projumi' restaurada exitosamente.";
                    } catch (Exception $e) {
                        $restauracionExitosa = false;
                        $mensaje[] = "ERROR al restaurar 'projumi': " . $e->getMessage();
                    }
                } else if ($restauracionExitosa) {
                    $mensaje[] = "ADVERTENCIA: No se encontró el archivo SQL para 'projumi'.";
                }

                // Limpieza de archivos temporales
                if (file_exists($zipPath)) unlink($zipPath);
                if (file_exists($mainFile)) unlink($mainFile);
                if (file_exists($projumiFile)) unlink($projumiFile);
                if (is_dir($uploadDir)) rmdir($uploadDir); // Eliminar directorio temporal si está vacío

                if ($restauracionExitosa) {
                    echo "Restauración Dual Completa: " . implode(" ", $mensaje);
                } else {
                    echo "Restauración Fallida o Parcial: " . implode(" ", $mensaje);
                }

            } else {
                echo "Error: No se pudo abrir el archivo ZIP para la restauración.";
            }
        } else {
            echo "Error: No se pudo subir el archivo para la restauración.";
        }
    } else {
        // En un entorno MVC real, aquí se renderizaría la vista para subir el archivo.
        echo "Por favor, utiliza un formulario para subir el archivo de respaldo (ZIP).";
    }
}