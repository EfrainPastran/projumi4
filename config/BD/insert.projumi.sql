
USE `projumi` ;

--
-- municipio
--

INSERT INTO `t_municipio` (`id_municipio`, `nombre`, `estatus`) VALUES
(1, 'Iribarren', 1),
(2, 'Jiménez', 1),
(3, 'Crespo', 1),
(4, 'Andrés Eloy Blanco', 1),
(5, 'Urdaneta', 1),
(6, 'Torres', 1),
(7, 'Palavecino', 1),
(8, 'Simón Planas', 1),
(9, 'Morán', 1);

--
-- parroquia
--

INSERT INTO `t_parroquia` (`id_parroquia`, `parroquia`, `estatus`, `fk_municipio`) VALUES
(1, 'Catedral', 1, 1),
(2, 'Concepción', 1, 1),
(3, 'El Cují', 1, 1),
(4, 'Juan de Villegas', 1, 1),
(5, 'Santa Rosa', 1, 1),
(6, 'Tamaca', 1, 1),
(7, 'Unión', 1, 1),
(8, 'Águedo Felipe Alvarado', 1, 1),
(9, 'Buena Vista', 1, 1),
(10, 'Juárez', 1, 1),
(11, 'Coronel Mariano Peraza', 1, 2),
(12, 'Cuara', 1, 2),
(13, 'Diego de Lozada', 1, 2),
(14, 'José Bernardo Dorante', 1, 2),
(15, 'Juan Bautista Rodríguez', 1, 2),
(16, 'Paraíso de San José', 1, 2),
(17, 'San Miguel', 1, 2),
(18, 'Tintorero', 1, 2),
(19, 'Duaca', 1, 3),
(20, 'José María Blanco', 1, 3),
(21, 'Freitez', 1, 3),
(22, 'Pío Tamayo', 1, 4),
(23, 'Quebrada Honda de Guache', 1, 4),
(24, 'Yacambú', 1, 4),
(25, 'Siquisique', 1, 5),
(26, 'San Miguel', 1, 5),
(27, 'Moroturo', 1, 5),
(28, 'Xaguas', 1, 5),
(29, 'Carora', 1, 6),
(30, 'Cascabel', 1, 6),
(31, 'La Ceiba', 1, 6),
(32, 'Río Claro', 1, 6),
(33, 'Trinidad Samuel', 1, 6),
(34, 'Antonio Díaz', 1, 6),
(35, 'Camacaro', 1, 6),
(36, 'Castañeda', 1, 6),
(37, 'Cecilio Zubillaga', 1, 6),
(38, 'Chiquinquirá', 1, 6),
(39, 'El Blanco', 1, 6),
(40, 'Espinoza de Los Monteros', 1, 6),
(41, 'Lara', 1, 6),
(42, 'Las Mercedes', 1, 6),
(43, 'Manuel Morillo', 1, 6),
(44, 'Montaña Verde', 1, 6),
(45, 'Montes de Oca', 1, 6),
(46, 'Torres', 1, 6),
(47, 'Reyes Vargas', 1, 6),
(48, 'Altagracia', 1, 7),
(49, 'José Gregorio Bastidas', 1, 7),
(50, 'Agua Viva', 1, 7),
(51, 'Sarare', 1, 8),
(52, 'Buría', 1, 8),
(53, 'Anzoátegui', 1, 9),
(54, 'Bolívar', 1, 9),
(55, 'Santa Cruz de Guárico', 1, 9),
(56, 'Hilario Luna y Luna', 1, 9),
(57, 'Humocaro Bajo', 1, 9),
(58, 'Humocaro Alto', 1, 9),
(59, 'La Candelaria', 1, 9),
(60, 'Morán', 1, 9);

--
-- emprendedor
--

INSERT INTO `t_emprendedor` (`id_emprededor`, `cedula`, `lugar_nacimiento`, `estado_civil`, `nacionalidad`, `rif`, `sexo`, `alergia_medicamento`, `alergia_alimento`, `operado`, `sacramento`, `grupo_sangre`, `religion`, `grupo_activo`, `cantidad_hijos`, `carga_familiar`, `casa_propia`, `alquiler`, `titulo_academico`, `profesion`, `oficio`, `hobby`, `conocimiento_projumi`, `motivo_projumi`, `aporte_projumi`, `imagen`, `emprendimiento`, `estatus`, `fk_parroquia`) VALUES
(1, 27759045, 'Tachira', 'Soltero', 'V', '277590451', 'Masculino', 'Si', 'No', 'No', NULL, '', 'Cristiano', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'public/emprendedores/leche_miel.jpg', 'Leche y Miel', 1, 1),
(2, 28123456, 'Barquisimeto - Edo Lara', 'Soltera', 'V', '281234562', 'F', 'No', 'Si camarones (Todo lo del mar)', 'No', 'Matrimonio', 'No', 'Catolica', 'Actualmente solo en projumi', 0, 1, 'Si', 'Familiar', 'TSU En informatica', 'TSU En informatica', 'Emprendedora y estudiante universitario', 'Bailar - Leer - Viajar', 'Por una invitacion de unas compañeras que asistian a una red', 'Para dar a conocer mi emprendimiento y compartir mis conocim', 'Compartir mis conocimientos de mi emprendimiento por los cur', 'public/emprendedores/dulce_mordida.jpg', 'Dulce Mordida', 1, 1),
(3, 27123456, 'Barquisimeto - Edo Lara', 'Comprometida', 'V', '261234563', 'F', 'No', 'No', 'Si, vesicular', 'Confirmacion', 'A+', 'Catolica', 'Ninguno', 0, 3, 'No', 'No', 'Ninguno', 'TSU en administracion', 'No especifica', 'Manualidades', 'Por medio de Maikelys', 'Porque se hacer labor de voluntariado, darme a conocer', 'Apoyo y conociemiento', 'public/emprendedores/jeanette.jpg', 'Manualidades', 1, 1);

--
-- categoria
--

INSERT INTO `t_categoria` (`id_categoria`, `nombre`, `descripcion`, `estatus`) VALUES
(1, 'Bisuteria', '', '1'),
(2, 'Paleteria', 'Ejemplo', '0'),
(3, 'Muñequeria', '', '1'),
(4, 'Reposteria', '', '1'),
(5, 'Arte movil', '', '1'),
(6, 'Decoraciones', '', '1'),
(7, 'Manualidades', '', '1');

--
-- cliente
--

INSERT INTO `t_cliente` (`id_cliente`, `cedula`, `fecha_registro`, `estatus`) VALUES
(25, 7363406, '2025-07-14', 1),
(26, 27759045, '2025-07-14', 1),
(27, 26274075, '2025-07-14', 1),
(28, 26502663, '2025-07-15', 1);

--
-- empresa_envio
--

INSERT INTO `t_empresa_envio` (`id_empresa_envio`, `nombre`, `telefono`, `direccion`, `estatus`) VALUES
(1, 'MRW', '02512514852', 'Av venezuela', 1),
(2, 'Tealca', '02515425412', 'Av Zona industrial I', 1);

--
-- cambio
--

INSERT INTO `t_cambio` (`id_cambio`, `tasa_cambio`, `fecha_cambio`, `estatus`) VALUES
(1, 90.00, '2025-05-16 00:32:24', 1),
(2, 92.00, '2025-05-16 00:32:24', 1),
(6, 95.24, '2025-05-27 13:01:10', 1),
(7, 96.53, '2025-05-29 17:01:16', 1),
(8, 97.31, '2025-06-01 17:01:57', 1),
(9, 97.31, '2025-06-02 08:01:07', 1),
(10, 97.42, '2025-06-03 17:01:05', 1),
(11, 97.90, '2025-06-04 17:01:18', 1),
(12, 97.90, '2025-06-05 08:01:07', 1),
(13, 99.09, '2025-06-08 17:01:18', 1),
(14, 102.16, '2025-06-15 14:01:11', 1),
(15, 102.16, '2025-06-15 14:01:11', 1),
(16, 102.81, '2025-06-18 17:01:12', 1),
(17, 103.74, '2025-06-19 17:01:13', 1),
(18, 106.86, '2025-06-26 17:02:15', 1),
(19, 106.86, '2025-06-27 13:01:15', 1),
(20, 107.62, '2025-06-28 17:01:09', 1),
(21, 107.62, '2025-06-29 11:01:11', 1),
(22, 108.19, '2025-07-01 13:05:03', 1),
(23, 108.98, '2025-07-02 11:02:17', 1),
(24, 109.77, '2025-07-03 16:01:15', 1),
(25, 115.33, '2025-07-13 23:02:03', 1),
(26, 115.33, '2025-07-14 14:02:42', 1),
(27, 118.29, '2025-07-17 23:01:10', 1),
(28, 118.29, '2025-07-18 14:02:15', 1),
(29, 119.14, '2025-07-21 19:02:12', 1);

--
-- moneda
--

INSERT INTO `t_moneda` (`id_moneda`, `nombre`, `simbolo`, `pais`, `estatus`, `fk_cambio`) VALUES
(1, 'Bolivar', 'Bs', 'Venezuela', 1, 1),
(2, 'Dolar', '$', 'EUU', 1, 2);

--
-- metodo_pago
--

INSERT INTO `t_metodo_pago` (`id_metodo_pago`, `nombre`, `descripcion`, `estatus`) VALUES
(1, 'Efectivo', '', 1),
(2, 'Transferencia', '', 1),
(3, 'Pago Movil', '', 1);

--
-- evento
--

--
-- producto
--

INSERT INTO `t_producto` (`id_producto`, `nombre`, `precio`, `descripcion`, `stock`, `fecha_ingreso`, `fk_categoria`, `fk_emprendedor`, `status`) VALUES
(23, 'Granola', 1, 'son frutos secos con una textura crujiente.', 49, '2025-07-14 07:12:18', 4, 1, 1),
(24, 'Tortas Fria', 2, 'Ricas tortas para un evento expecial', 50, '2025-07-14 09:17:43', 4, 1, 1),
(25, 'Tortas de chocolate', 3, 'Ricas tortas para pasar un deleite', 50, '2025-07-14 09:19:42', 4, 1, 1),
(26, 'Carros de madera', 2, 'bonitos carros de madera', 50, '2025-07-14 09:21:35', 7, 2, 1),
(27, 'Movil', 2, 'Movil decorativo para tu casa', 50, '2025-07-14 09:22:56', 6, 2, 1),
(28, 'Munecas de tela', 1, 'bellas munecas de tela', 50, '2025-07-14 09:24:28', 3, 2, 1),
(29, 'Pulseras', 2, 'bonitas pulseras para toda ocasiona', 48, '2025-07-14 09:26:24', 1, 2, 1);

--
-- galeria
--

INSERT INTO `t_galeria` (`id_galeria`, `fk_producto`, `ruta`) VALUES
(33, 24, 'public/6875037765b79_686d74a7ecf55_imhgygtyages.jpg'),
(34, 25, 'public/687503ee6ac08_686803877e040_images.jpg'),
(35, 26, 'public/6875045f70926_686e15da0c214_Sin títsdsdulo.jpg'),
(36, 27, 'public/687504b0b8b87_686ed164ebf21_Sin tísdsdtulo.jpg'),
(37, 28, 'public/6875050c6ddaa_munequeria-pitusacreaciones-lapalma.jpg'),
(38, 29, 'public/687505807677d_que-es-la-bisuteria-y-tipos.jpg'),
(40, 23, 'public/6879f6f06cbf6_686ef439152c1_receta-basica-granola-casera.jpg');

--
-- datos_cuenta
--

INSERT INTO `t_datos_cuenta` (`id_datos_cuenta`, `telefono`, `banco`, `correo`, `numero_cuenta`, `fk_emprendedor`, `fk_metodo_pago`) VALUES
(3, '04245623516', 'Banco de Venezuela', 'rosmery@gmail.com', '01027484539464864846', 2, 2),
(4, '04123456789', 'Banco Mercantil', 'efrain@gmail.com', '01053647484983747646', 1, 3);

--
-- detalle_metodo_pago
--

INSERT INTO `t_detalle_metodo_pago` (`id_detalle_metodo_pago`, `fk_metodo_pago`, `fk_moneda`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 3, 1),
(4, 2, 1);

--
-- ventas_evento
--

--
-- pedidos
--

INSERT INTO `t_pedidos` (`id_pedidos`, `fecha_pedido`, `estatus`, `fk_cliente`) VALUES
(24, '2025-07-15 10:55:46', 'Pendiente', 26),
(25, '2025-07-15 10:57:17', 'Pendiente', 26);

--
-- detalle_pedido
--

INSERT INTO `t_detalle_pedido` (`id_detalle_pedido`, `producto_id_producto`, `pedidos_id_pedido`, `cantidad`, `precio_unitario`) VALUES
(1, 23, 25, 1, 1),
(2, 29, 24, 2, 2);

--
-- delivery
--

--
-- pagos
--

INSERT INTO `t_pagos` (`id_pagos`, `estatus`, `fk_pedido`, `fecha_pago`) VALUES
(24, 'Pendiente', 24, '2025-07-15 10:55:47'),
(25, 'Pendiente', 25, '2025-07-15 10:57:17');

--
-- detalle_pago
--

INSERT INTO `t_detalle_pago` (`id_detalle_pago`, `fk_pago`, `fk_detalle_metodo_pago`, `monto`, `referencia`, `comprobante`) VALUES
(72, 24, 2, 4.00, '', ''),
(73, 25, 1, 115.33, '', '');

--
-- envio
--

INSERT INTO `t_envio` (`id_envio`, `direccion_envio`, `estatus`, `numero_seguimiento`, `fk_empresa_envio`, `fk_pedido`) VALUES
(18, 'caribe', 'Pendiente', '', 1, 24),
(19, 'coriano', 'Pendiente', '', 1, 25);