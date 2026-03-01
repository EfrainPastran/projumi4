<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <?php include 'views/componentes/head.php'; ?>
    <link rel="stylesheet" href="<?php echo APP_URL;('/public/css/style.css'); ?>">
</head>
<body>
    <div class="container">
        <h1><?php echo $welcome; ?></h1>
        <p>Este es el sistema MVC básico.</p>
        <a href="<?php echo url('auth/register'); ?>" class="btn">Registrarse</a>
        <a href="<?php echo url('auth/consultar'); ?>" class="btn">Consultar</a>
    </div>
    <script src="<?php echo url('public/js/main.js'); ?>" type="module"></script>
</body>
</html>