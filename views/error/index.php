<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['title']; ?></title>
    <?php include 'views/componentes/head.php'; ?>
    <link rel="stylesheet" href="<?php echo constant('URL') ?>public/css/style.css">
</head>
<body>
    <h1>Error</h1>
    <p><?php echo htmlspecialchars($data['message'], ENT_QUOTES, 'UTF-8'); ?></p>
    <a href="<?php echo constant('URL') ?>">Volver al inicio</a>
</body>
</html>