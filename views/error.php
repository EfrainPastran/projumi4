<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Error - Página no encontrada</title>
  <?php include 'views/componentes/head.php'; ?>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/w.css">
  <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/p.css">
</head>
<body class="boerror">
  <div class="error-container">
    <div class="error-icon">
      <i class="fas fa-exclamation-triangle"></i>
    </div>
    <div class="error-code"><?php echo $data['code']; ?></div>
    <div class="error-message">¡Ups! <?php echo $data['error']; ?></div>
    <a>La página que buscas puede haber sido eliminada, cambiada de nombre o no estar disponible temporalmente.</a>
<?php
$errorPage = $_SESSION['user']['error'] ?? null;
?>
<?php if ($errorPage === 'home'): ?>

    <!-- Si viene de home -->
    <a href="<?php echo APP_URL; ?>/home/index" class="btn btn-home mt-3">
        <i class="fas fa-home me-2"></i> Volver al inicio
    </a>

<?php elseif ($errorPage === 'emprendimiento'): ?>

    <!-- Si viene de emprendimiento -->
    <a href="<?php echo APP_URL; ?>/home/principal" class="btn btn-home mt-3">
        <i class="fas fa-home me-2"></i> Volver al inicio
    </a>

<?php else: ?>

    <!-- Si no viene de ninguno (o no existe la variable) -->
    <a href="<?php echo APP_URL; ?>/home/index" class="btn btn-home mt-3">
        <i class="fas fa-home me-2"></i> Volver al inicio
    </a>

<?php endif; ?>
    
  </div>

</body>
</html>