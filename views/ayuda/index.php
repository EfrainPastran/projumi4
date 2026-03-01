<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manual de Usuario</title>
    <?php include 'views/componentes/head.php'; ?>
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/ayuda.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
    	<img class="img_logo rounded-circle me-2"src="<?php echo APP_URL; ?>/public/ayuda/projumi.jpg" alt="Projecto juvenil misionero">
        <br>
        <h1>Manual de Usuario</h1>
        <h2>Guía completa para el uso de Nuestro Sistema Projumi</h2>
        <button id="toggle-menu" class="menu-button">☰ Menú</button>
    </header>

    <div class="container">
        <aside class="sidebar">
            <nav id="menu-navegacion">
                <ul>
                    <section id="Conoce">
                        <h3>Explora</h3>
                        <li><a href="<?php echo APP_URL; ?>/home/principal">Volver al inicio</a></li>
                        <li><a href="#introduccion">Introducción</a></li>
                        <li><a href="#sobre-nosotros">Sobre Nosotros</a></li>
                        <li><a href="#nuestro-sitio-web">Nuestro Sitio Web</a></li>
                        <li><a href="#primeros-pasos">Primeros Pasos</a></li>
                    </section>
        
                    <section id="Funcionalidades">
                        <h3>Funcionalidades</h3>
                        <li><a href="#funcionalidades">Sobre el Menu Principal</a></li>
                        <li><a href="#emprendedores">Sobre Emprendedores</a></li>
                        <li><a href="#categorias">Sobre Categorías</a></li>
                        <li><a href="#productos">Sobre Productos</a></li>
                        <li><a href="#carrito">Sobre Carrito</a></li>
                        <li><a href="#pagos">Sobre pagos</a></li>
                        <li><a href="#envios">Sobre envios</a></li>
                        <li><a href="#pedidos">Sobre pedidos</a></li>
                        <li><a href="#ventas_evento">Sobre Ventas por eventos</a></li>
                        <li><a href="#eventos">Sobre Eventos</a></li>
                        <li><a href="#empresa_envio">Sobre Empresas de envios</a></li>
                        <li><a href="#cliente">Sobre Clientes</a></li>
                        <li><a href="#reportes">Sobre Reportes</a></li>
                    </section>
                </ul>
            </nav>
        </aside>

        <main class="contenido">
            <section id="introduccion" class="seccion">
                <h2>Introducción</h2>
                <p>Este manual esta diseñado para que los usuarios tengan mejor interacion al
                momento que deseen visitar nuestro sitio web y a su vez conocer 
                nuestro sistema y poder ser parte de ello, mostraremos paso a paso como
                seria el proceso de su uso y acceso.
                </p>
                <img src="<?php echo APP_URL; ?>/public/ayuda/introduccion.jpeg" alt="Captura de pantalla">
            </section>
            
            <section id="sobre-nosotros" class="seccion">
                <h2>Sobre Nosotros</h2>
                <p>PROJUMI (proyecto juvenil misionero) Es una asociacion civil enfocada en ayudar a las personas 
                caritativamente y con programas de ayuda y capacitación.

                Nuestra fundación nace del deseo de servir a la comunidad, especialmente a los jóvenes, 
                brindando herramientas para su desarrollo integral a través de programas sociales,
                religiosos y educativos.
                </p>
                <img src="<?php echo APP_URL; ?>/public/ayuda/sobre_nosotros.jpeg" alt="Captura de pantalla">
            </section>
            
                 <section id="nuestro-sitio-web" class="seccion">
                <h2>Nuestro Sitio Web</h2>
                <div class="paso">
                    <h3>Paso 1: Visita</h3>
                    <p>Sitio web</p>
                </div>
                <div class="paso">
                    <h3>Paso 2: Registrarse</h3>
                    <p>Ejecuta el link y accede a Nuestro Sistema Web</p>
                </div>
            </section>

            <section id="primeros-pasos" class="seccion">
                <h2>Primeros pasos</h2>
                <p>Al abrir la aplicacion podras navegar en nuestro sistema y conocer las funcionalidades 
                que te brindamos.
            Acceder a la RED DE EMPRENDEDORES</p>
                <img src="<?php echo APP_URL; ?>/public/ayuda/primeros_paso.jpeg" alt="Captura de pantalla">
            </section>

            <section id="funcionalidades" class="seccion">
                <h2>Funcionalidades principales del Sistema Web</h2>

                 <h3>0- Inicio</h3>
                  <p>Visualizara en el menu cada una de las funcionalidades del sistema</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/inicio.jpeg" alt="Captura de pantalla">
            </section>
            <section id="emprendedores" class="seccion">
                <h3>1-Emprendedores</h3>
                 <p>En este Modulo pueden visualizar los paso a paso de como se Registra, Lista, Agrega, Modifica, Aprueba y Elimina un emprendedor.</p>
                  <h3>Menu</h3>
                   <p>Se visualiza en el menu el despliegue donde esta ubicado EMPRENDEDOR.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/menu.jpeg" alt="Captura de pantalla">

                  <h3>Listar Emprendedor</h3>
                   <p>Se muestra el Listado actual de los emprededores</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/listar.jpeg" alt="Captura de pantalla">

                  <h3>Agregar Emprendedor</h3>
                   <h4>PASO 1:</h4> <p>Ingresar los datos solicitados (Datos Personales)</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/agregar1.jpeg" alt="Captura de pantalla">
                   <h4>PASO 2:</h4> <p>Ingresar los datos solicitados (Datos Familiares y Salud)</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/agregar2.jpeg" alt="Captura de pantalla">
                   <h4>PASO 3:</h4> <p>Ingresar los datos solicitados (Educacion y Religion)</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/agregar3.jpeg" alt="Captura de pantalla">
                   <h4>PASO 4:</h4> <p>Ingresar los datos solicitados (Emprendimientos y Projumi)</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/agregar4.jpeg" alt="Captura de pantalla">

                 <h3>Modificar Emprendedor</h3>
                   <h4>PASO 1:</h4> <p>Ingresar los datos que seran editados (Datos Personales)</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/modificar1.jpeg" alt="Captura de pantalla">
                   <h4>PASO 2:</h4> <p>Ingresar los datos que seran editados (Datos Familiares y Salud)</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/modificar2.jpeg" alt="Captura de pantalla">
                   <h4>PASO 3:</h4> <p>Ingresar los datos que seran editados (Educacion y Religion)</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/modificar3.jpeg" alt="Captura de pantalla">
                   <h4>PASO 4:</h4> <p>Ingresar los datos que seran editados (Emprendimiento y Projumi</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/modificar4.jpeg" alt="Captura de pantalla">

                 <h3>Aprobar Emprendedor</h3>
                  <p>Se aprueba el registro del emprendedor</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/aprobarEmp.jpeg" alt="Captura de pantalla">

                 <h3>Registro del Emprendedor</h3>
                  <p>Mostramos el listados de los emprededores registrados</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/registroEmp.jpeg" alt="Captura de pantalla">

                 <h3>Eliminar Emprendedor</h3>
                  <p>Se elimina un emprendedor</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/eliminarEmp.jpeg" alt="Captura de pantalla">
            </section>
            <section id="categorias" class="seccion">
                 <h3>Categorias</h3>
                  <h3>Menu</h3>
                   <p>Se visualiza en el menu el despliegue donde esta ubicado CATEGORIA.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/menuCat.jpeg" alt="Captura de pantalla">

                 <h3>Listar Categorias</h3>
                  <p>Se muestra el Listado actual de las categorias</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/listarCat.jpeg" alt="Captura de pantalla">

                 <h3>Agregar Categorias</h3>
                  <p>Se Agrega los datos de las categorias nueva</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/agregarCat.jpeg" alt="Captura de pantalla">

                 <h3>Editar Categorias</h3>
                  <p>Se Editan los datos de la categoria selecionada</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/modificarCat.jpeg" alt="Captura de pantalla">

                 <h3>Eliminar Categorias</h3>
                  <p>Se elimina la categoria seleccionada</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/eliminarCat.jpeg" alt="Captura de pantalla">
            </section>
            <section id="productos" class="seccion">
                 <h3>Productos</h3>
                  <h3>Menu</h3>
                   <p>Se visualiza en el menu el despliegue donde esta ubicado PRODUCTO.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/menuProd.jpeg" alt="Captura de pantalla">

                 <h3>Listar Productos</h3>
                  <p>Se muestra el Listado actual de los productos</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/listarProd.jpeg" alt="Captura de pantalla">

                 <h3>Agregar Productos</h3>
                  <p>Se Agrega los datos del producto nuevo</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/agregarProd.jpeg" alt="Captura de pantalla">

                 <h3>Editar Productos</h3>
                  <p>Se Editan los datos del Producto selecionado</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/modificarProd.jpeg" alt="Captura de pantalla">

                 <h3>Eliminar Productos</h3>
                  <p>Se Eliminan los datos del Producto selecionado</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/modificarProd.jpeg" alt="Captura de pantalla">
            </section>
            <section id="carrito" class="seccion">
                 <h3>Comprar por Carrito</h3>
                  <h3>Menu</h3>
                   <p>Se visualiza en el menu el despliegue donde esta ubicado CARRITO.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/menuCarr.jpeg" alt="Captura de pantalla">

                 <h3>Ver el Producto</h3>
                   <p>Se visualiza el producto detallado.</p>
                     <img src="<?php echo APP_URL; ?>/public/ayuda/verProdCarr.jpeg" alt="Captura de pantalla">

                 <h3>Agregar Producto al Carrito</h3>
                   <p>Se Agrega los productos al Carrito</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/agregarCarr.jpeg" alt="Captura de pantalla">

                 <h3>Ver Carrito</h3>
                   <p>Se visualiza los Productos en el Carrito.</p>
                     <img src="<?php echo APP_URL; ?>/public/ayuda/VerCarr.jpeg" alt="Captura de pantalla">   

                <h3>Listar Carrito</h3>
                  <p>Se muestra el Listado actual de la Compra en el Carrito</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/listarCarr.jpeg" alt="Captura de pantalla">

                 <h3>Confirmar Compra</h3>
                   <p>Se confirma la compra de los productos en el Carrito.</p>
                     <img src="<?php echo APP_URL; ?>/public/ayuda/conComCarr.jpeg" alt="Captura de pantalla">   

                 <h3>Verificar Envio de la Compra</h3>
                  <p>Se verifica el envio de la compra de los productos.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/veriEnvCarr.jpeg" alt="Captura de pantalla">   

                 <h3>Datos si es por Empresa de Envio</h3>
                   <p>Se verifica los datos a suminitrar de la empresa de envio donde desea recibir el pedido.</p>
                     <img src="<?php echo APP_URL; ?>/public/ayuda/datosEnvEmpCarr.jpeg" alt="Captura de pantalla">   

                 <h3>Datos si es por Delivery</h3>
                   <p>Se verifica los datos de quien recibe el pedido.</p>
                     <img src="<?php echo APP_URL; ?>/public/ayuda/datosEnvDelCarr.jpeg" alt="Captura de pantalla">  

                 <h3>Realizar Pago de la Compra por Carrito</h3>
                  <p>Se muestra registro de pago de la compra del carrito.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/realPagCarr.jpeg" alt="Captura de pantalla"> 

                 <h3>Ver datos del Pago Movil</h3>
                   <p>Se visualizan los datod de Pago Movil donde se realizara el pago de la Compra.</p>
                     <img src="<?php echo APP_URL; ?>/public/ayuda/DatPMCarr.jpeg" alt="Captura de pantalla">   

                 <h3>Confirmar Pago de la Compra</h3>
                   <p>Se confirma el pago de la compra de los productos.</p>
                     <img src="<?php echo APP_URL; ?>/public/ayuda/ConfPagCarr.jpeg" alt="Captura de pantalla">  
            </section>
            <section id="pagos" class="seccion">
                 <h3>8- Pagos</h3>
                   <h3>Menu</h3>
                    <P>Se visualiza en el menu el despliegue donde esta ubicado PAGO.</P>
                      <img src="<?php echo APP_URL; ?>/public/ayuda/menuPago.jpeg" alt="Captura de pantalla">

                <h3>Listar Pago Emprendedor</h3>
                  <p>Se muestra el Listado de los pago por Emprendedor.</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/lisPagEmp.jpeg" alt="Captura de pantalla">

                 <h3>Consultar Detalles del Pago</h3>
                  <p>Se consulta el detalle del pago realizado.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/consDetPag.jpeg" alt="Captura de pantalla">

                 <h3>Validar el Pago</h3>
                  <p>Se valida el pago realizado.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/valPag.jpeg" alt="Captura de pantalla">

                <h3>Ver Comprobante de Pago</h3>
                  <p>Se visualiza el comprabante de pago.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/compPag.jpeg" alt="Captura de pantalla">
                    <img src="<?php echo APP_URL; ?>/public/ayuda/comprobante.jpeg" alt="Captura de pantalla">

                 <h3>Aprobar Pago</h3>
                  <p>Se aprueba el pago si tiene los datos correctos.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/aproPag.jpeg" alt="Captura de pantalla">
            </section>
            <section id="cpagos" class="seccion">
                 <h3>Listar Pago del Cliente</h3>
                  <p>Se listan los pago de los clientes que haya realizado compra.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/listPagClie.jpeg" alt="Captura de pantalla">

                <h3>Mis pagos Cliente</h3>
                  <p>Se listan los pago de los clientes que haya realizado compra.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/mispagos.jpeg" alt="Captura de pantalla">
            </section>
            <section id="envios" class="seccion">
                <h3>Mis Envios (VISTA EMPRENDEDOR)</h3>
                  <p>Se Visualizara la lista de los envios por emprendedor.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/MisenvEmp.jpeg" alt="Captura de pantalla">

                 <h3>Ver detalles del Envio (VISTA EMPRENDEDOR)</h3>
                  <p>Se Visualizara los detalles de los envios por emprendedor.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/VerdetEnvEmp.jpeg" alt="Captura de pantalla">

                 <h3>Confirmar Numero de Seguimiento (VISTA EMPRENDEDOR)</h3>
                  <p>Se confirmar el nuemro del seguimiento del envio.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/confSeg.jpeg" alt="Captura de pantalla">

                 <h3>Cambia el Estatus a "EN PROCESO" (VISTA EMPRENDEDOR)</h3>
                   <p>El estatus del envio cambia a (En Proceso).</p>
                     <img src="<?php echo APP_URL; ?>/public/ayuda/CamEstProc.jpeg" alt="Captura de pantalla">

                 <h3>Mis Envios por Delivery (VISTA EMPRENDEDOR)</h3>
                  <p>Se Visualizara la lista de los envios por emprendedor.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/envDelVisEmp.jpeg" alt="Captura de pantalla">

                <h3>Mis Envios Confirma Numero de Delivery (VISTA EMPRENDEDOR)</h3>
                  <p>Se confirma si el numero de envio ha sido el correcto.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/MisEnvConf.jpeg" alt="Captura de pantalla">
            </section>
            <section id="cenvios" class="seccion">
                 <h3>Mis Entregas (VISTA CLIENTE)</h3>
                  <p>Se visualiza las entregas realizada.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/MisEntCli.jpeg" alt="Captura de pantalla">

                <h3>Mis Entregas por Delivery (VISTA CLIENTE)</h3>
                  <p>Se visualiza las entregas realizada por delivery.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/MisEntDeli.jpeg" alt="Captura de pantalla">

                <h3>Aprobado el Estatus Del Delivery (VISTA CLIENTE)</h3>
                  <p>Al ser aprobado cambia el estatus del delivery.</p>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/AproDel.jpeg" alt="Captura de pantalla">
            </section>
            <section id="pedidos" class="seccion">
                 <h3>Pedidos</h3>
                   <h3>Menu</h3>
                    <P>Se visualiza en el menu el despliegue donde esta ubicado ENVIO.</P>
                      <img src="<?php echo APP_URL; ?>/public/ayuda/menuPago.jpeg" alt="Captura de pantalla">

                 <h3>Listar Pedido (VISTA EMPRENDEDOR)</h3>
                    <P>Se listan todos los pedidos del emprendedor.</P>
                      <img src="<?php echo APP_URL; ?>/public/ayuda/lispedemp.jpeg" alt="Captura de pantalla">

                <h3>Ver Detalles (VISTA EMPRENDEDOR)</h3>
                    <P>Ver detalles del pedido.</P>
                      <img src="<?php echo APP_URL; ?>/public/ayuda/VerDEtPed.jpeg" alt="Captura de pantalla">
                   <P>Pendiente = Para Aprobar el Pago</P>
                   <P>Al realizar el pago cambia el status</P>
                   <p>En proceso = En proceso de envio</p>

                 <h3>Validar el Envio (VISTA EMPRENDEDOR)</h3>
                    <P>Al validar el envio pasa el estatus a "EN TRANSITO"</P>
                      <img src="<?php echo APP_URL; ?>/public/ayuda/ValEnvi.jpeg" alt="Captura de pantalla">
            </section>
            <section id="cpedidos" class="seccion">
                <h3>Pedidos (VISTA CLIENTE)</h3>
                 <h3>Listar Pedido (VISTA CLIENTE)</h3>
                    <P>Se listan todos los pedidos del cliente.</P>
                      <img src="<?php echo APP_URL; ?>/public/ayuda/listPed.jpeg" alt="Captura de pantalla">

                <h3>Ver Detalles (VISTA CLIENTE)</h3>
                   <P>Ver detalles del pedido.</P>
                      <img src="<?php echo APP_URL; ?>/public/ayuda/detpedi.jpeg" alt="Captura de pantalla">
                   <P>Pendiente = Para aprobar el pago</P>
                   <P>Al realizar El pago cambia el status</P>
                   <p>En proceso = En proceso de envio</p> 

                 <h3>Validar el Envio (VISTA CLIENTE)</h3>
                    <P>Al validar el envio pasa el estatus a "EN TRANSITO"</P>
                      <img src="<?php echo APP_URL; ?>/public/ayuda/transito.jpeg" alt="Captura de pantalla">
            </section>
            <section id="ventas_evento" class="seccion">
                <h3>Realizar Ventas por Eventos</h3>
                 <p>En este modulo vamos a visualizar el listado de los evento, como agregar un evento y ver los detalles de dicho evento</p>
            </section>
            <section id="eventos" class="seccion">
                <h3>Listar Eventos del Emprendedor</h3>
                 <p>Muestra el listado de los eventos ya registrados</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/evento1.jpeg" alt="Captura de pantalla">

                <h3>Agregar un Evento</h3>
                 <p>Como agregar un evento paso a paso</p>
                  <img src="<?php echo APP_URL; ?>/public/ayuda/evento2.jpeg" alt="Captura de pantalla">

                <h3>Detalles del Evento</h3>
                 <p>Visualiza los detalles de cada evento que ya este registado</p>
                  <img src="<?php echo APP_URL; ?>/public/ayuda/evento3.jpeg" alt="Captura de pantalla">
            </section>
            <section id="empresa_envio" class="seccion">
                 <h3>Empresa De Envio</h3>
                <h3>Listar Empresa de Envio</h3>
                  <p>Se muestra el Listado actual de las Empresas de Envio</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/ListaEmp.jpeg" alt="Captura de pantalla">

                 <h3>Agregar Empresa de Envio</h3>
                  <p>Se Agrega los datos de la Nueva EMpresa de Envio</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/agregarEmp.jpeg" alt="Captura de pantalla">

                 <h3>Editar Empresa de Envio</h3>
                  <p>Se Editan los datos de la Empresa de Envio selecionada</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/modifiEmp.jpeg" alt="Captura de pantalla">

                 <h3>Eliminar Empresa de Envio</h3>
                  <p>Se Eliminan los datos de La Empresa de Envio selecionada</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/elimiEmp.jpeg" alt="Captura de pantalla">
            </section>
            <section id="cliente" class="seccion">
                   <h3>Cliente</h3>
                <h3>Listar Cliente</h3>
                  <p>Se muestra el Listado actual de los Clientes</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/cliente1.jpeg" alt="Captura de pantalla">

                 <h3>Agregar Clientes</h3>
                  <p>Se Agrega los datos del Cliente</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/cliente2.jpeg" alt="Captura de pantalla">

                 <h3>Editar Cliente</h3>
                  <p>Se Editan los datos del Cliente selecionado</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/cliente3.jpeg" alt="Captura de pantalla">

                 <h3>Eliminar Cliente</h3>
                  <p>Se Eliminan los datos del Cliente selecionada</p>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/cliente4.jpeg" alt="Captura de pantalla">
            </section>
            <section id="reportes" class="seccion">
                    <h3>Reportes</h3>
                 <h3>Mostrar Reportes</h3>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/repor1.jpeg" alt="Captura de pantalla">

                 <h3>Generar Reporte por Tipo y Rango</h3>
                    <img src="<?php echo APP_URL; ?>/public/ayuda/repor2.jpeg" alt="Captura de pantalla">

                 <h3>Ver Detalles del Reporte</h3>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/repor3.jpeg" alt="Captura de pantalla">

                 <h3>Exportar Historial</h3>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/repor4.jpeg" alt="Captura de pantalla">

                 <h3>Confirmar Exportacion</h3>
                   <img src="<?php echo APP_URL; ?>/public/ayuda/repor5.jpeg" alt="Captura de pantalla">
            </section>  
        </main>
    </div>

    <footer>
        <p>FUNDACION PROJUMI 2025</p>
    </footer>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('toggle-menu');
    const menu = document.getElementById('menu-navegacion');

    toggleBtn.addEventListener('click', function () {
        menu.classList.toggle('open');
    });
});
</script>
    <script src="<?php echo APP_URL; ?>/public/js/ayuda.js" type="module"></script>
</body>
</html>
