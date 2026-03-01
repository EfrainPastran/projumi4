<?php $_SESSION['user']['error'] = 'home'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PROJUMI - Fundación Juvenil Misionera</title>
    <?php include 'views/componentes/head.php'; ?>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Estilos personalizados -->
    <link rel="stylesheet" href="<?php echo APP_URL; ?>/public/css/index.css">
    
</head>
<body>
    <header class="hero-section">
        <div class="container text-center py-5">
            <div class="logo-container mb-4">
                <img src="<?php echo APP_URL; ?>/public/img/unnamed.jpg" alt="PROJUMI Logo" class="rounded-logo">
        
            </div>
            <div class="hero-content">
                <h1 class="display-4 fw-bold text-white">PROJUMI</h1>
                <p class="lead text-white">Proyecto Juvenil Misionero</p>
                <a href="#services" class="btn btn-primary btn-lg mt-3">Conoce nuestros servicios</a>
            </div>
        </div>
    </header>

    <!-- Sobre PROJUMI -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">Sobre Nosotros</h2>
                    <p class="lead">PROJUMI (proyecto juvenil misionero) Es una asociacion civil enfocada en ayudar a las personas caritativamente y con programas de ayuda y capacitación.</p>
                    <p>Nuestra fundación nace del deseo de servir a la comunidad, especialmente a los jóvenes, brindando herramientas para su desarrollo integral a través de programas sociales, religiosos y educativos.</p>
                    <button class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#aboutModal">Conoce más</button>
                </div>
                <div class="col-lg-6">
                    <img src= "<?php echo APP_URL; ?>/public/img/jovenes.jpg" alt="Grupo de jóvenes" class="img-fluid rounded shadow">
                   
                </div>
            </div>
        </div>
    </section>

    <!-- Nuestros Servicios -->
    <section id="services" class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center fw-bold mb-5">Nuestros Servicios</h2>
            <div class="row g-4">
                <!-- Servicio 1 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?php echo APP_URL; ?>/public/img/oracion-980x622-1.jpeg" class="card-img-top" alt="Servicio Socio Religioso">
                        
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Servicio Socio Religioso</h5>
                            <p class="card-text">Programas espirituales y comunitarios para el crecimiento personal y en comunidad.</p>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#Closemodal">Más información</button>
                        </div>
                    </div>
                </div>
                
                <!-- Servicio 2 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?php echo APP_URL; ?>/public/img/capacitación Conektate.png" class="card-img-top" alt="Cursos de Capacitación">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Cursos de Capacitación</h5>
                            <p class="card-text">Formación en habilidades técnicas y blandas para el desarrollo personal y profesional.</p>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#Closemodal">Más información</button>
                        </div>
                    </div>
                </div>
                
                <!-- Servicio 3 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src= "<?php echo APP_URL; ?>/public/img/red-autonomos-seguridad-social.jpg" alt="Red de Emprendimientos">
                       
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Red de Emprendimientos</h5>
                            <p class="card-text">Apoyo y asesoría para jóvenes emprendedores con ideas innovadoras.</p>
                            <a href= "<?php echo APP_URL; ?>/home/principal" class="btn btn-outline-primary">Más información</a>
                           
                        </div>
                    </div>
                </div>
                
                <!-- Servicio 4 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?php echo APP_URL; ?>/public/img/Caridad.png" class="card-img-top" alt="Caridad y Acción">
                        
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Caridad y Acción</h5>
                            <p class="card-text">Programas de ayuda social para comunidades vulnerables.</p>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#Closemodal">Más información</button>
                        </div>
                    </div>
                </div>
                
                <!-- Servicio 5 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?php echo APP_URL; ?>/public/img/atencion-centros.jpg" class="card-img-top" alt="Centro de Salud">
                        
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Centro de Salud y Desintoxicación</h5>
                            <p class="card-text">Atención médica básica y programas de rehabilitación.</p>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#Closemodal">Más información</button>
                        </div>
                    </div>
                </div>
                
                <!-- Servicio 6 -->
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="<?php echo APP_URL; ?>/public/img/Captura.png" class="card-img-top" alt="Salud Mental">
                        
                        <div class="card-body">
                            <h5 class="card-title fw-bold">Salud Mental: Acompañamiento y Seguimiento</h5>
                            <p class="card-text">Apoyo psicológico y seguimiento personalizado para el bienestar emocional.</p>
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#Closemodal">Más información</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contacto -->
    <section id="contact" class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto text-center">
                    <h2 class="fw-bold mb-4">Contáctanos</h2>
                    <p>¿Quieres saber más sobre nuestros servicios o cómo puedes participar?</p>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#contactModal">Escríbenos</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Fundación PROJUMI</h5>
                    <p>Proyecto Juvenil Misionero</p>
                    <p>"Servir con amor y compromiso"</p>
                </div>
                <div class="col-md-3">
                    <h5>Enlaces</h5>
                    <ul class="list-unstyled">
                        <li><a href="#about" class="text-white">Nosotros</a></li>
                        <li><a href="#services" class="text-white">Servicios</a></li>
                        <li><a href="#contact" class="text-white">Contacto</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Síguenos</h5>
                    <div class="social-links">
                        <a href="#" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <p class="mb-0">&copy; 2023 Fundación PROJUMI. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Modal Sobre PROJUMI -->
    <div class="modal fade" id="aboutModal" tabindex="-1" aria-labelledby="aboutModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="aboutModalLabel">Sobre PROJUMI</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <img src="<?php echo APP_URL; ?>/public/img/imagengg.png" alt="Misión PROJUMI" class="img-fluid mb-3">
                            
                        </div>
                        <div class="col-md-6">
                            <h4>Nuestra Misión</h4>
                            <p>PROJUMI es una organización sin fines de lucro comprometida con el desarrollo integral de los jóvenes y comunidades vulnerables a través de programas educativos, espirituales y de salud.</p>
                            <h4 class="mt-4">Nuestra Visión</h4>
                            <p>Ser un referente en el trabajo juvenil misionero, transformando vidas y comunidades a través del servicio, la educación y la fe.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal En proceso -->
    <div class="modal fade" id="Closemodal" tabindex="-1" aria-labelledby="CloseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="CloseModalLabel">UPSS!!</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        
                        <div class="col-md-6">
                            <h4>Servicio en Proceso</h4>
                            <p>Estamos trabajando para ti para ofrecerte este servicio.</p>
                           
                            <p>Proximamente!.</p>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Contacto -->
    <div class="modal fade" id="contactModal" tabindex="-1" aria-labelledby="contactModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="contactModalLabel">Contáctanos</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="contactForm">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Mensaje</label>
                            <textarea class="form-control" id="message" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="service" class="form-label">Servicio de interés</label>
                            <select class="form-select" id="service">
                                <option value="">Seleccione un servicio</option>
                                <option value="religioso">Servicio Socio Religioso</option>
                                <option value="capacitacion">Cursos de Capacitación</option>
                                <option value="emprendimientos">Red de Emprendimientos</option>
                                <option value="caridad">Caridad y Acción</option>
                                <option value="salud">Centro de Salud</option>
                                <option value="mental">Salud Mental</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary" form="contactForm">Enviar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Script personalizado -->
    <script src="<?php echo APP_URL; ?>/public/js/script.js" type="module"></script>
    
</body>
</html>