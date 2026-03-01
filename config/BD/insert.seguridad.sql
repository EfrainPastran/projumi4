USE `seguridad` ;

--
-- modulo
--

--
-- Volcado de datos para la tabla `t_modulo`
--
INSERT INTO seguridad.t_modulo (`id_modulo`, `nombre`, `ruta`, `icono`, `menu_padre`, `orden`, `estatus`) VALUES
(1, 'Inicio', 'home/principal', 'fas fa-home', NULL, 1, 1),
(2, 'Productos', 'productos/index', 'fas fa-store', NULL, 2, 1),
(3, 'Envios', 'envios/index', 'fas fa-truck', NULL, 3, 1),
(4, 'Pedidos', 'pedidos/index', 'fas fa-clipboard-list', NULL, 4, 1),
(5, 'Pagos', 'pagos/index', 'fas fa-dollar-sign', NULL, 5, 1),
(6, 'Reportes', 'reportes/index', 'fa-solid fa-file-pdf', NULL, 6, 1),
(7, 'Ventas', '#', 'fa-solid fa-ticket', NULL, 7, 1),
(8, 'Ventas por lote', 'ventaslote/index', 'fa-solid fa-clipboard-list', 7, 8, 1),
(9, 'Ventas presencial', 'ventaspresencial/index', 'fa-solid fa-handshake', 7, 9, 1),
(10, 'Seguridad', '#', 'fa-solid fa-lock', NULL, 10, 1),
(11, 'Bitácora', 'bitacora/index', 'fa-solid fa-clipboard-list', 10, 11, 1),
(12, 'Usuarios', 'usuarios/index', 'fa-solid fa-users-cog', 10, 12, 1),
(13, 'Roles', 'roles/index', 'fa-solid fa-key', 10, 13, 1),
(14, 'Módulos', 'modulos/index', 'fa-solid fa-cubes', 10, 14, 1),
(15, 'Mantenimiento', 'mantenimiento/index', 'fas fa-database', 10, 15, 1),
(16, 'Configuración', '#', 'fa-solid fa-cog', NULL, 16, 1),
(17, 'Eventos', 'eventos/index', 'fa-solid fa-calendar-days', 16, 17, 1),
(18, 'Empresa de envio', 'empresa/index', 'fa-solid fa-industry', 16, 18, 1),
(19, 'Categoría', 'categorias/index', 'fas fa-tags', 16, 19, 1),
(20, 'Emprendedores', 'emprendedor/index', 'fas fa-users', 16, 20, 1),
(21, 'Clientes', 'clientes/index', 'fas fa-users', 16, 21, 1),
(22, 'Inicia Sesion', '#" data-bs-toggle="modal" data-bs-target="#loginModal', 'fas fa-sign-in-alt', 0, 22, 1),
(23, 'Registrarse', '#" data-bs-toggle="modal" data-bs-target="#registroClienteModal', 'fas fa-user-plus', 0, 23, 1);

--
-- permisos
--

--
-- Volcado de datos para la tabla `t_permisos`
--
INSERT INTO `t_permisos` (`id_permisos`, `nombre`, `descriccion_de_permiso`, `estatus`) VALUES
(1, 'registrar', 'Permite crear registros', 1),
(2, 'consultar', 'Permite ver registros', 1),
(4, 'actualizar', 'Permite modificar registros', 1),
(5, 'eliminar', 'Permite eliminar registros', 1);

--
-- rol
--

--
-- Volcado de datos para la tabla `t_rol`
--

INSERT INTO `t_rol` (`id_rol`, `nombre`, `descripcion_rol`, `estatus`) VALUES
(1, 'Administrador', 'Podras publicar todos lo que necesitas vender', 1),
(2, 'Super Usuario', 'Soporte tecnico', 1),
(3, 'Emprendedor', 'Solo para ventas', 1),
(4, 'Cliente', 'Solo para compras', 1),
(5, 'Visitante', 'Nuevo usuario', 1);

--
-- permiso_rol_modo SuperUsuario
--

INSERT INTO seguridad.t_permiso_rol_modulo (fk_rol, fk_modulo, fk_permiso, estatus) VALUES
(2, 1, 1, 1),
(2, 1, 2, 1),
(2, 1, 4, 1),
(2, 1, 5, 1),
(2, 2, 1, 1),
(2, 2, 2, 1),
(2, 2, 4, 1),
(2, 2, 5, 1),
(2, 3, 1, 1),
(2, 3, 2, 1),
(2, 3, 4, 1),
(2, 3, 5, 1),
(2, 4, 1, 1),
(2, 4, 2, 1),
(2, 4, 4, 1),
(2, 4, 5, 1),
(2, 5, 1, 1),
(2, 5, 2, 1),
(2, 5, 4, 1),
(2, 5, 5, 1),
(2, 6, 1, 1),
(2, 6, 2, 1),
(2, 6, 4, 1),
(2, 6, 5, 1),
(2, 7, 1, 1),
(2, 7, 2, 1),
(2, 7, 4, 1),
(2, 7, 5, 1),
(2, 8, 1, 1),
(2, 8, 2, 1),
(2, 8, 4, 1),
(2, 8, 5, 1),
(2, 9, 1, 1),
(2, 9, 2, 1),
(2, 9, 4, 1),
(2, 9, 5, 1),
(2, 10, 1, 1),
(2, 10, 2, 1),
(2, 10, 4, 1),
(2, 10, 5, 1),
(2, 11, 1, 1),
(2, 11, 2, 1),
(2, 11, 4, 1),
(2, 11, 5, 1),
(2, 12, 1, 1),
(2, 12, 2, 1),
(2, 12, 4, 1),
(2, 12, 5, 1),
(2, 13, 1, 1),
(2, 13, 2, 1),
(2, 13, 4, 1),
(2, 13, 5, 1),
(2, 14, 1, 1),
(2, 14, 2, 1),
(2, 14, 4, 1),
(2, 14, 5, 1),
(2, 15, 1, 1),
(2, 15, 2, 1),
(2, 15, 4, 1),
(2, 15, 5, 1),
(2, 16, 1, 1),
(2, 16, 2, 1),
(2, 16, 4, 1),
(2, 16, 5, 1),
(2, 17, 1, 1),
(2, 17, 2, 1),
(2, 17, 4, 1),
(2, 17, 5, 1),
(2, 18, 1, 1),
(2, 18, 2, 1),
(2, 18, 4, 1),
(2, 18, 5, 1),
(2, 19, 1, 1),
(2, 19, 2, 1),
(2, 19, 4, 1),
(2, 19, 5, 1),
(2, 20, 1, 1),
(2, 20, 2, 1),
(2, 20, 4, 1),
(2, 20, 5, 1),
(2, 21, 1, 1),
(2, 21, 2, 1),
(2, 21, 4, 1),
(2, 21, 5, 1);

--
-- Permisos para Administrador
--

INSERT INTO seguridad.t_permiso_rol_modulo (fk_rol, fk_modulo, fk_permiso, estatus) VALUES
(1, 1, 1, 1),
(1, 1, 2, 1),
(1, 1, 4, 1),
(1, 1, 5, 1),
(1, 6, 1, 1),
(1, 6, 2, 1),
(1, 6, 4, 1),
(1, 6, 5, 1),
(1, 16, 1, 1),
(1, 16, 2, 1),
(1, 16, 4, 1),
(1, 16, 5, 1);

--
-- Permisos para Emprededor
--

INSERT INTO seguridad.t_permiso_rol_modulo (fk_rol, fk_modulo, fk_permiso, estatus) VALUES
(3, 1, 1, 1),
(3, 1, 2, 1),
(3, 1, 4, 1),
(3, 1, 5, 1),
(3, 2, 1, 1),
(3, 2, 2, 1),
(3, 2, 4, 1),
(3, 2, 5, 1),
(3, 3, 1, 1),
(3, 3, 2, 1),
(3, 3, 4, 1),
(3, 3, 5, 1),
(3, 4, 1, 1),
(3, 4, 2, 1),
(3, 4, 4, 1),
(3, 4, 5, 1),
(3, 5, 1, 1),
(3, 5, 2, 1),
(3, 5, 4, 1),
(3, 5, 5, 1),
(3, 6, 1, 1),
(3, 6, 2, 1),
(3, 6, 4, 1),
(3, 6, 5, 1),
(3, 7, 1, 1),
(3, 7, 2, 1),
(3, 7, 4, 1),
(3, 7, 5, 1),
(3, 8, 1, 1),
(3, 8, 2, 1),
(3, 8, 4, 1),
(3, 8, 5, 1),
(3, 9, 1, 1),
(3, 9, 2, 1),
(3, 9, 4, 1),
(3, 9, 5, 1);

--
-- Permisos para Cliente
--

INSERT INTO seguridad.t_permiso_rol_modulo (fk_rol, fk_modulo, fk_permiso, estatus) VALUES
(4, 1, 1, 1),
(4, 1, 2, 1),
(4, 1, 4, 1),
(4, 1, 5, 1),
(4, 2, 1, 1),
(4, 2, 2, 1),
(4, 2, 4, 1),
(4, 2, 5, 1),
(4, 3, 1, 1),
(4, 3, 2, 1),
(4, 3, 4, 1),
(4, 3, 5, 1),
(4, 4, 1, 1),
(4, 4, 2, 1),
(4, 4, 4, 1),
(4, 4, 5, 1),
(4, 5, 1, 1),
(4, 5, 2, 1),
(4, 5, 4, 1);

--
-- Permisos para visitante
--

INSERT INTO seguridad.t_permiso_rol_modulo (fk_rol, fk_modulo, fk_permiso, estatus) VALUES
(5, 1, 1, 1),
(5, 1, 2, 1),
(5, 1, 4, 1),
(5, 1, 5, 1),
(5, 22, 1, 1),
(5, 22, 2, 1),
(5, 22, 4, 1),
(5, 22, 5, 1),
(5, 23, 1, 1),
(5, 23, 2, 1),
(5, 23, 4, 1),
(5, 23, 5, 1);

--
-- usuario
--

--
-- Volcado de datos para la tabla `t_usuario`
--

INSERT INTO `t_usuario` (`id_usuario`, `cedula`, `nombre`, `apellido`, `correo`, `password`, `direccion`, `telefono`, `fecha_registro`, `fecha_nacimiento`, `estatus`, `fk_rol`) VALUES
(1, '27759045', 'Efrain', 'Pastran', 'efrain_pastran@gmail.com', '$2y$10$Qh3CtEJrZmv2B5fstc1ypOnybC7262FAl1rTw.5JspQzmE9HJVP6C', 'avenida tuya', '04165567448', '2025-05-13 23:24:57', '1997-05-01', 1, 3),
(2, '28123456', 'Rosmery', 'Mejia', 'rosmerydelvalle@gmail.com', '$2y$10$QP4NnioumUvUW5d2dZKJ9O3zoGgFyy0zCJQfoLYSUlrE8TWvKvs7G', '', '', '2025-05-17 01:13:58', '1997-05-01', 1, 3),
(3, '20123456', 'Jesus', 'Rivas', 'rivas@gmail.com', '$2y$10$QP4NnioumUvUW5d2dZKJ9O3zoGgFyy0zCJQfoLYSUlrE8TWvKvs7G', 'avenida Vargas', '04245623516', '2025-07-14 12:49:30', '1999-04-29', 1, 2),
(4, '7363406', 'Edgar', 'Leal', 'eleal@gmail.com', '$2y$10$QP4NnioumUvUW5d2dZKJ9O3zoGgFyy0zCJQfoLYSUlrE8TWvKvs7G', '', '04245623516', '2025-07-14 14:14:36', '1999-03-13', 1, 1),
(5, '26502663', 'Luis', 'Sivira', 'rivas@gmail.com', '$2y$10$FPIXCT0LF3mGGqOz6kmEjOddyogO66gGBWRGZf8JFbORsrQ.Ytwpq', 'garagatal', '04245623516', '2025-07-15 00:00:00', '1999-04-29', 1, 4);


--
-- mantenimiento
--

--
-- bitacora
--

--
-- Volcado de datos para la tabla `t_bitacora`
--

INSERT INTO `t_bitacora` (`id_bitacora`, `modulo_accionado`, `descripcion_accion`, `fecha_registro`, `fk_usuario`) VALUES
(32, 'Registro de producto', 'El usuario ha registrado un nuevo producto: G', '2025-07-14 13:12:18', 1),
(33, 'Registro de producto', 'El usuario ha registrado un nuevo producto: T', '2025-07-14 15:17:43', 1),
(34, 'Registro de producto', 'El usuario ha registrado un nuevo producto: T', '2025-07-14 15:19:42', 1),
(35, 'Registro de producto', 'El usuario ha registrado un nuevo producto: C', '2025-07-14 15:21:35', 2),
(36, 'Registro de producto', 'El usuario ha registrado un nuevo producto: M', '2025-07-14 15:22:56', 2),
(37, 'Registro de producto', 'El usuario ha registrado un nuevo producto: M', '2025-07-14 15:24:28', 2),
(38, 'Registro de producto', 'El usuario ha registrado un nuevo producto: P', '2025-07-14 15:26:24', 2);

--
-- notificaciones
--

--
-- Volcado de datos para la tabla `t_notificacion`
--

INSERT INTO `t_notificacion` (`id_notificacion`, `titulo`, `descripcion`, `fecha`, `fk_usuario_emisor`, `fk_usuario_receptor`, `status`, `ruta`) VALUES
(1, 'Pedido registrado', 'Se ha registrado un nuevo pedido PED-24', '2025-07-15 16:55:47', 5, 2, 0, '/pedidos/index'),
(2, 'Pedido registrado', 'Se ha registrado un nuevo pedido PED-25', '2025-07-15 16:57:17', 5, 1, 0, '/pedidos/index');

--
-- personal
--






