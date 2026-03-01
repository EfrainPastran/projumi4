<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesion</title>
    <?php include 'views/componentes/head.php'; ?>
    <link rel="stylesheet" href="<?php constant('URL') ?>public/css/style.css">
</head>
<body>
    <h1>Sesion de Usuario</h1>

    <h3><a href="<?php echo constant('URL') ?>home/index">Volver</a></h3>

    <h3><a href="<?php echo constant('URL') ?>user/register">Registrarse</a></h3>
    
    <br>
    <br>

    <form action="<?php echo constant('URL') ?>user/sesion" method="post">
        <div>
            <label for="cedula">Cédula:</label>
            <input type="text" id="cedula" name="ced" required>
        </div>
        <div>
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="pass" required>
        </div>
        <input type="submit" name="iniciar" value="ENTRAR">
    </form>

</body>
</html>