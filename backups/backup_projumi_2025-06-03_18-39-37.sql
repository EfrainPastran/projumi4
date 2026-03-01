-- Backup de la base de datos PROJUMI
-- Generado el: 2025-06-03 18:39:37



-- Estructura para tabla `t_cambio`
DROP TABLE IF EXISTS `t_cambio`;
CREATE TABLE `t_cambio` (
  `id_cambio` int(11) NOT NULL AUTO_INCREMENT,
  `tasa_cambio` decimal(10,2) NOT NULL,
  `fecha_cambio` datetime NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_cambio`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_cambio`
INSERT INTO `t_cambio` (`id_cambio`,`tasa_cambio`,`fecha_cambio`,`estatus`) VALUES 
('1','90.00','2025-05-16 00:32:24','1'),
('2','92.00','2025-05-16 00:32:24','1'),
('6','95.24','2025-05-27 13:01:10','1'),
('7','96.53','2025-05-29 17:01:16','1'),
('8','97.31','2025-06-01 17:01:57','1'),
('9','97.31','2025-06-02 08:01:07','1'),
('10','97.42','2025-06-03 17:01:05','1');


-- Estructura para tabla `t_categoria`
DROP TABLE IF EXISTS `t_categoria`;
CREATE TABLE `t_categoria` (
  `id_categoria` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(35) NOT NULL,
  `descripcion` varchar(45) NOT NULL,
  `estatus` varchar(45) NOT NULL,
  PRIMARY KEY (`id_categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_categoria`
INSERT INTO `t_categoria` (`id_categoria`,`nombre`,`descripcion`,`estatus`) VALUES 
('1','Bisuteria','','1'),
('2','Paleteria','Ejemplo','0'),
('3','Muñequeria','','1'),
('4','Reposteria','','1'),
('5','Arte movil','','1'),
('6','Decoraciones','','1'),
('7','Manualidades','','1'),
('9','neuva','asdlkasdklj','1');


-- Estructura para tabla `t_cliente`
DROP TABLE IF EXISTS `t_cliente`;
CREATE TABLE `t_cliente` (
  `id_cliente` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `correo` varchar(45) NOT NULL,
  `telefono` varchar(45) NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `fecha_registro` date NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_cliente`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_cliente`
INSERT INTO `t_cliente` (`id_cliente`,`cedula`,`nombre`,`apellido`,`correo`,`telefono`,`fecha_nacimiento`,`fecha_registro`,`estatus`) VALUES 
('1','26502663','Luis','Sivira','luissivira669@gmail.com','416354654','2025-05-21','2025-05-06','1');


-- Estructura para tabla `t_datos_cuenta`
DROP TABLE IF EXISTS `t_datos_cuenta`;
CREATE TABLE `t_datos_cuenta` (
  `id_datos_cuenta` int(11) NOT NULL AUTO_INCREMENT,
  `telefono` varchar(45) NOT NULL,
  `banco` varchar(45) NOT NULL,
  `correo` varchar(45) NOT NULL,
  `numero_cuenta` varchar(30) NOT NULL,
  `fk_emprendedor` int(11) NOT NULL,
  `fk_metodo_pago` int(11) NOT NULL,
  PRIMARY KEY (`id_datos_cuenta`,`fk_emprendedor`,`fk_metodo_pago`),
  KEY `fk_emprendedor` (`fk_emprendedor`),
  KEY `fk_metodo_pago` (`fk_metodo_pago`),
  CONSTRAINT `t_datos_cuenta_ibfk_1` FOREIGN KEY (`fk_emprendedor`) REFERENCES `t_emprendedor` (`id_emprededor`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `t_datos_cuenta_ibfk_2` FOREIGN KEY (`fk_metodo_pago`) REFERENCES `t_metodo_pago` (`id_metodo_pago`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_datos_cuenta`
INSERT INTO `t_datos_cuenta` (`id_datos_cuenta`,`telefono`,`banco`,`correo`,`numero_cuenta`,`fk_emprendedor`,`fk_metodo_pago`) VALUES 
('1','04120254852','Banco del tesoro','pedro@gmail.com','016354789654123654','1','3');


-- Estructura para tabla `t_delivery`
DROP TABLE IF EXISTS `t_delivery`;
CREATE TABLE `t_delivery` (
  `id_delivery` int(11) NOT NULL AUTO_INCREMENT,
  `direccion_exacta` varchar(60) NOT NULL,
  `destinatario` varchar(45) NOT NULL,
  `telefono_destinatario` varchar(45) NOT NULL,
  `correo_destinatario` varchar(45) DEFAULT NULL,
  `telefono_delivery` varchar(45) DEFAULT NULL,
  `fk_pedido` int(11) NOT NULL,
  `estatus` varchar(30) NOT NULL,
  PRIMARY KEY (`id_delivery`,`fk_pedido`),
  KEY `fk_pedido` (`fk_pedido`),
  CONSTRAINT `t_delivery_ibfk_1` FOREIGN KEY (`fk_pedido`) REFERENCES `t_pedidos` (`id_pedidos`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_delivery`
INSERT INTO `t_delivery` (`id_delivery`,`direccion_exacta`,`destinatario`,`telefono_destinatario`,`correo_destinatario`,`telefono_delivery`,`fk_pedido`,`estatus`) VALUES 
('1','Av florencio nuevasd sd','Laura Perez Dudamen','0125578764','mario@gmail.com','0125578764','1','En proceso'),
('2','Av florencio','Laura Perez','0125578764','mario@gmail.com','234234234','1','En proceso'),
('3','Av florencio','Laura Perez','0125578764','mario@gmail.com','2323423','1','En proceso'),
('4','Urb yucatan','Cesar Alejandro','04120318406','cesar@gmail.com',NULL,'72',''),
('5','Mi direccion','Pablo Moran','04268749563','pablo@gmail.com',NULL,'79',''),
('6','Av Moran','Pablo Moran','04268749563','pablo@gmail.com',NULL,'91',''),
('7','asdasdasdas','Pablo Moran','04268749563','pablo@gmail.com',NULL,'100','Pendiente');


-- Estructura para tabla `t_detalle_metodo_pago`
DROP TABLE IF EXISTS `t_detalle_metodo_pago`;
CREATE TABLE `t_detalle_metodo_pago` (
  `id_detalle_metodo_pago` int(11) NOT NULL AUTO_INCREMENT,
  `fk_metodo_pago` int(11) NOT NULL,
  `fk_moneda` int(11) NOT NULL,
  PRIMARY KEY (`id_detalle_metodo_pago`),
  KEY `fk_metodo_pago` (`fk_metodo_pago`),
  KEY `fk_moneda` (`fk_moneda`),
  CONSTRAINT `t_detalle_metodo_pago_ibfk_1` FOREIGN KEY (`fk_metodo_pago`) REFERENCES `t_metodo_pago` (`id_metodo_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `t_detalle_metodo_pago_ibfk_2` FOREIGN KEY (`fk_moneda`) REFERENCES `t_moneda` (`id_moneda`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_detalle_metodo_pago`
INSERT INTO `t_detalle_metodo_pago` (`id_detalle_metodo_pago`,`fk_metodo_pago`,`fk_moneda`) VALUES 
('1','1','1'),
('2','1','2'),
('3','3','1'),
('4','2','1');


-- Estructura para tabla `t_detalle_pago`
DROP TABLE IF EXISTS `t_detalle_pago`;
CREATE TABLE `t_detalle_pago` (
  `id_detalle_pago` int(11) NOT NULL AUTO_INCREMENT,
  `fk_pago` int(11) NOT NULL,
  `fk_detalle_metodo_pago` int(11) NOT NULL,
  `monto` decimal(10,2) NOT NULL,
  `referencia` varchar(20) DEFAULT NULL,
  `comprobante` varchar(100) NOT NULL,
  PRIMARY KEY (`id_detalle_pago`),
  KEY `fk_detalle_metodo_pago` (`fk_detalle_metodo_pago`),
  KEY `fk_pago` (`fk_pago`),
  CONSTRAINT `t_detalle_pago_ibfk_1` FOREIGN KEY (`fk_detalle_metodo_pago`) REFERENCES `t_detalle_metodo_pago` (`id_detalle_metodo_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `t_detalle_pago_ibfk_2` FOREIGN KEY (`fk_pago`) REFERENCES `t_pagos` (`id_pagos`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_detalle_pago`
INSERT INTO `t_detalle_pago` (`id_detalle_pago`,`fk_pago`,`fk_detalle_metodo_pago`,`monto`,`referencia`,`comprobante`) VALUES 
('1','1','2','51.00',NULL,''),
('2','1','1','900.00',NULL,''),
('25','13','3','100.00','dwfwf',''),
('26','13','2','50.00','123123',''),
('27','14','2','200.00','',''),
('28','26','2','270.00','',''),
('29','27','2','270.00','',''),
('30','28','4','2000.00','432543543',''),
('31','28','2','112.78','',''),
('32','29','4','2000.00','432543543',''),
('33','29','2','112.78','',''),
('34','30','4','2000.00','432543543',''),
('35','30','2','112.78','',''),
('36','31','4','2000.00','432543543',''),
('37','31','2','112.78','',''),
('38','32','4','2000.00','432543543',''),
('39','32','2','112.78','',''),
('40','33','4','2000.00','432543543',''),
('41','33','2','112.78','',''),
('42','34','4','2000.00','432543543',''),
('43','34','2','112.78','',''),
('44','35','1','12150.00','',''),
('45','36','1','2700.00','',''),
('46','37','2','44.99','',''),
('47','38','2','20.00','',''),
('48','39','4','4049.98','',''),
('49','40','2','30.00','',''),
('50','41','3','57144.00','123123123',''),
('51','42','4','954.00','1234',''),
('52','43','4','954.00','13123213',''),
('53','44','4','954.00','123123','comprobante_0'),
('54','45','3','954.00','23123','comprobante_0'),
('55','46','4','954.00','3234','comprobante_0'),
('56','47','4','954.00','2323','comprobantes/comprobante_16227.jpg'),
('57','48','4','954.00','7868678','public/comprobantes/comprobante_24603.jpg'),
('58','49','4','2858.00','63565456','public/comprobantes/comprobante_66358.png'),
('59','50','4','1946.20','dsfsf','public/comprobantes/comprobante_99157.jpg');


-- Estructura para tabla `t_detalle_pedido`
DROP TABLE IF EXISTS `t_detalle_pedido`;
CREATE TABLE `t_detalle_pedido` (
  `producto_ID_PRODUCTO` int(11) NOT NULL,
  `pedidos_ID_PEDIDO` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `precio_unitario` int(11) NOT NULL,
  PRIMARY KEY (`producto_ID_PRODUCTO`,`pedidos_ID_PEDIDO`),
  KEY `pedidos_ID_PEDIDO` (`pedidos_ID_PEDIDO`),
  CONSTRAINT `t_detalle_pedido_ibfk_1` FOREIGN KEY (`producto_ID_PRODUCTO`) REFERENCES `t_producto` (`id_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `t_detalle_pedido_ibfk_2` FOREIGN KEY (`pedidos_ID_PEDIDO`) REFERENCES `t_pedidos` (`id_pedidos`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_detalle_pedido`
INSERT INTO `t_detalle_pedido` (`producto_ID_PRODUCTO`,`pedidos_ID_PEDIDO`,`cantidad`,`precio_unitario`) VALUES 
('2','2','3','45'),
('2','3','1','45'),
('2','4','6','45'),
('2','5','6','45'),
('2','6','6','45'),
('2','7','6','45'),
('2','8','6','45'),
('2','12','2','20'),
('2','13','2','20'),
('2','14','2','20'),
('2','16','2','20'),
('2','78','1','45'),
('2','80','1','45'),
('3','1','2','8'),
('3','2','2','8'),
('3','12','1','10'),
('3','13','1','10'),
('3','14','1','10'),
('3','16','1','10'),
('3','28','2','100'),
('3','29','2','100'),
('3','30','1','100'),
('3','31','1','100'),
('3','32','1','100'),
('3','33','1','100'),
('3','51','6','45'),
('3','92','6','100'),
('25','58','4','45'),
('25','62','3','45'),
('25','63','3','45'),
('25','64','3','45'),
('25','65','3','45'),
('25','67','3','45'),
('25','68','3','45'),
('25','72','3','45'),
('25','76','3','45'),
('25','77','3','10'),
('25','79','2','10'),
('25','91','3','10'),
('25','93','1','10'),
('25','94','1','10'),
('25','95','1','10'),
('25','96','1','10'),
('25','97','1','10'),
('25','98','1','10'),
('25','99','1','10'),
('25','100','3','10'),
('25','101','2','10');


-- Estructura para tabla `t_emprendedor`
DROP TABLE IF EXISTS `t_emprendedor`;
CREATE TABLE `t_emprendedor` (
  `id_emprededor` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellido` varchar(50) NOT NULL,
  `lugar_nacimiento` varchar(45) NOT NULL,
  `estado_civil` varchar(20) NOT NULL,
  `nacionalidad` varchar(20) NOT NULL,
  `rif` varchar(30) DEFAULT NULL,
  `sexo` varchar(20) NOT NULL,
  `alergia_medicamento` varchar(45) DEFAULT NULL,
  `alergia_alimento` varchar(45) DEFAULT NULL,
  `operado` varchar(45) DEFAULT NULL,
  `sacramento` varchar(45) DEFAULT NULL,
  `grupo_sangre` varchar(20) NOT NULL,
  `religion` varchar(45) DEFAULT NULL,
  `grupo_activo` varchar(45) DEFAULT NULL,
  `cantidad_hijos` int(11) DEFAULT NULL,
  `carga_familiar` int(11) DEFAULT NULL,
  `casa_propia` varchar(10) DEFAULT NULL,
  `alquiler` varchar(10) DEFAULT NULL,
  `titulo_academico` varchar(45) DEFAULT NULL,
  `profesion` varchar(45) DEFAULT NULL,
  `oficio` varchar(45) DEFAULT NULL,
  `hobby` varchar(45) DEFAULT NULL,
  `conocimiento_projumi` varchar(60) DEFAULT NULL,
  `motivo_projumi` varchar(60) DEFAULT NULL,
  `aporte_projumi` varchar(60) DEFAULT NULL,
  `imagen` varchar(100) NOT NULL,
  `emprendimiento` varchar(50) NOT NULL,
  `estatus` int(11) NOT NULL,
  `fk_parroquia` int(11) NOT NULL,
  PRIMARY KEY (`id_emprededor`,`fk_parroquia`),
  KEY `fk_parroquia` (`fk_parroquia`),
  CONSTRAINT `t_emprendedor_ibfk_1` FOREIGN KEY (`fk_parroquia`) REFERENCES `t_parroquia` (`id_parroquia`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_emprendedor`
INSERT INTO `t_emprendedor` (`id_emprededor`,`cedula`,`nombre`,`apellido`,`lugar_nacimiento`,`estado_civil`,`nacionalidad`,`rif`,`sexo`,`alergia_medicamento`,`alergia_alimento`,`operado`,`sacramento`,`grupo_sangre`,`religion`,`grupo_activo`,`cantidad_hijos`,`carga_familiar`,`casa_propia`,`alquiler`,`titulo_academico`,`profesion`,`oficio`,`hobby`,`conocimiento_projumi`,`motivo_projumi`,`aporte_projumi`,`imagen`,`emprendimiento`,`estatus`,`fk_parroquia`) VALUES 
('1','27759045','Efrain','Pastran','Tachira','Soltero','V','277590451','Masculino','Si','No','No',NULL,'','Cristiano',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'public/emprendedores/leche_miel.jpg','Leche y Miel','1','1'),
('2','28123456','Rosmery','Mejias','Barquisimeto - Edo Lara','Soltera','V','281234562','F','No','Si camarones (Todo lo del mar)','No','Matrimonio','No','Catolica','Actualmente solo en projumi','0','1','Si','Familiar','TSU En informatica','TSU En informatica','Emprendedora y estudiante universitario','Bailar - Leer - Viajar','Por una invitacion de unas compañeras que asistian a una red','Para dar a conocer mi emprendimiento y compartir mis conocim','Compartir mis conocimientos de mi emprendimiento por los cur','public/emprendedores/dulce_mordida.jpg','Dulce Mordida','1','1'),
('3','28123456','Jenny','Colmenarez','Barquisimeto - Edo Lara','Comprometida','V','261234563','F','No','No','Si, vesicular','Confirmacion','A+','Catolica','Ninguno','0','3','No','No','Ninguno','TSU en administracion','No especifica','Manualidades','Por medio de Maikelys','Porque se hacer labor de voluntariado, darme a conocer','Apoyo y conociemiento','public/emprendedores/jeanette.jpg','Manualidades','1','1'),
('4','25123456','Jeanette Thais','Lucena Alvarez','La guaira','Soltera','V','251234564','F','nimesulide','nimesulide','Si','No especifica','No','Catolica','Ninguno','1','3','No','No','TSU Contabilidad Computarizada','No especifica','Repostera, Ama de casa','No especifica','Atraves de la Sr Maikelys','Para capacitacion como Emprendedora, servir como voluntaria','Colaborar en lo que este a mi alcance','public/emprendedores/jeanette.jpg','Reposteria Delicias Jeanette','1','1'),
('5','26234567','Noemi','Bermudez','Barquisimeto - Edo Lara','Soltera','V','09177844-7','F','Mertiolate','No','Si','Ninguno','No especifica','Catolica','No','5','1','Si','No','TSU','Preescolar','Manualista textil','Hacer Muñecas','Por medio de compañeros emprendedores','Para estar mejor organizada','Cumplir con actividades de projumi en lo que mas se pueda','public/emprendedores/sannys.jpg','Manualidades Sannys','1','1'),
('6','34567890','Maikhelym','Asuaje Gonzales','Guanare','Soltera','V','8058597','F','Penisilina','No','Si','Ninguno','No especifica','Catolica','Colegios parroquia fatima','1','5','Casa propi','No','Profesora en educacion integral','Educadora','Ninguno','Bailar','Desde algunos años realize algunos cursos','Por la gran labor, voluntariado, capacitacion, para la forma','Voluntariado, conocimiento, talleres','public/emprendedores/dulce_experiencia.jpg','Dulce experiencia','1','1');


-- Estructura para tabla `t_empresa_envio`
DROP TABLE IF EXISTS `t_empresa_envio`;
CREATE TABLE `t_empresa_envio` (
  `id_empresa_envio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `telefono` varchar(45) NOT NULL,
  `direccion` varchar(45) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_empresa_envio`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_empresa_envio`
INSERT INTO `t_empresa_envio` (`id_empresa_envio`,`nombre`,`telefono`,`direccion`,`estatus`) VALUES 
('1','MRW','02512514852','Av venezuela','1'),
('2','Tealca','02515425412','Av Zona industrial I','1'),
('4','zlaksdj','4654464654','asdfsdf','0');


-- Estructura para tabla `t_envio`
DROP TABLE IF EXISTS `t_envio`;
CREATE TABLE `t_envio` (
  `id_envio` int(11) NOT NULL AUTO_INCREMENT,
  `direccion_envio` varchar(45) NOT NULL,
  `estatus` varchar(45) NOT NULL,
  `numero_seguimiento` varchar(45) NOT NULL,
  `fk_empresa_envio` int(11) NOT NULL,
  `fk_pedido` int(11) NOT NULL,
  PRIMARY KEY (`id_envio`,`fk_empresa_envio`,`fk_pedido`),
  KEY `fk_empresa_envio` (`fk_empresa_envio`),
  KEY `fk_pedido` (`fk_pedido`),
  CONSTRAINT `t_envio_ibfk_1` FOREIGN KEY (`fk_empresa_envio`) REFERENCES `t_empresa_envio` (`id_empresa_envio`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `t_envio_ibfk_2` FOREIGN KEY (`fk_pedido`) REFERENCES `t_pedidos` (`id_pedidos`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_envio`
INSERT INTO `t_envio` (`id_envio`,`direccion_envio`,`estatus`,`numero_seguimiento`,`fk_empresa_envio`,`fk_pedido`) VALUES 
('2','Av venezuela','En proceso','23123','1','2'),
('3','Zona Norte','Pendiente','','1','76'),
('4','Barquisimeto obelisco','Pendiente','','1','80');


-- Estructura para tabla `t_evento`
DROP TABLE IF EXISTS `t_evento`;
CREATE TABLE `t_evento` (
  `id_eventos` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date DEFAULT NULL,
  `nombre` varchar(45) NOT NULL,
  `direccion` varchar(45) NOT NULL,
  `status` int(11) NOT NULL,
  PRIMARY KEY (`id_eventos`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Estructura para tabla `t_galeria`
DROP TABLE IF EXISTS `t_galeria`;
CREATE TABLE `t_galeria` (
  `id_galeria` int(11) NOT NULL AUTO_INCREMENT,
  `fk_producto` int(11) NOT NULL,
  `ruta` varchar(160) NOT NULL,
  PRIMARY KEY (`id_galeria`),
  KEY `fk_producto` (`fk_producto`),
  CONSTRAINT `t_galeria_ibfk_1` FOREIGN KEY (`fk_producto`) REFERENCES `t_producto` (`id_producto`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Estructura para tabla `t_metodo_pago`
DROP TABLE IF EXISTS `t_metodo_pago`;
CREATE TABLE `t_metodo_pago` (
  `id_metodo_pago` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(100) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_metodo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_metodo_pago`
INSERT INTO `t_metodo_pago` (`id_metodo_pago`,`nombre`,`descripcion`,`estatus`) VALUES 
('1','Efectivo','','1'),
('2','Transferencia','','1'),
('3','Pago Movil','','1');


-- Estructura para tabla `t_moneda`
DROP TABLE IF EXISTS `t_moneda`;
CREATE TABLE `t_moneda` (
  `id_moneda` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(15) NOT NULL,
  `simbolo` varchar(5) NOT NULL,
  `pais` varchar(45) NOT NULL,
  `estatus` int(11) NOT NULL,
  `fk_cambio` int(11) NOT NULL,
  PRIMARY KEY (`id_moneda`,`fk_cambio`),
  KEY `fk_cambio` (`fk_cambio`),
  CONSTRAINT `t_moneda_ibfk_1` FOREIGN KEY (`fk_cambio`) REFERENCES `t_cambio` (`id_cambio`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_moneda`
INSERT INTO `t_moneda` (`id_moneda`,`nombre`,`simbolo`,`pais`,`estatus`,`fk_cambio`) VALUES 
('1','Bolivar','Bs','Venezuela','1','1'),
('2','Dolar','$','EUU','0','2');


-- Estructura para tabla `t_municipio`
DROP TABLE IF EXISTS `t_municipio`;
CREATE TABLE `t_municipio` (
  `id_municipio` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(35) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_municipio`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_municipio`
INSERT INTO `t_municipio` (`id_municipio`,`nombre`,`estatus`) VALUES 
('1','Iribarren','1');


-- Estructura para tabla `t_pagos`
DROP TABLE IF EXISTS `t_pagos`;
CREATE TABLE `t_pagos` (
  `id_pagos` int(11) NOT NULL AUTO_INCREMENT,
  `estatus` varchar(45) NOT NULL,
  `fk_pedido` int(11) NOT NULL,
  `fecha_pago` datetime DEFAULT NULL,
  PRIMARY KEY (`id_pagos`,`fk_pedido`),
  KEY `fk_pedido` (`fk_pedido`),
  CONSTRAINT `t_pagos_ibfk_1` FOREIGN KEY (`fk_pedido`) REFERENCES `t_pedidos` (`id_pedidos`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_pagos`
INSERT INTO `t_pagos` (`id_pagos`,`estatus`,`fk_pedido`,`fecha_pago`) VALUES 
('1','Por verificar','1','2025-05-16 00:51:44'),
('13','Por verificar','1','2025-05-16 08:48:35'),
('14','Procesando','29','2025-05-24 23:34:45'),
('26','confirmado','51','2025-05-26 23:57:22'),
('27','confirmado','58','2025-05-27 01:07:18'),
('28','confirmado','62','2025-05-27 01:11:14'),
('29','confirmado','63','2025-05-27 01:12:57'),
('30','confirmado','64','2025-05-27 01:13:22'),
('31','confirmado','65','2025-05-27 01:13:47'),
('32','confirmado','67','2025-05-27 01:14:25'),
('33','confirmado','68','2025-05-27 05:58:10'),
('34','confirmado','72','2025-05-27 06:09:07'),
('35','confirmado','76','2025-05-27 06:14:25'),
('36','confirmado','77','2025-05-27 06:24:21'),
('37','Pendiente','78','2025-05-27 08:06:03'),
('38','Pendiente','79','2025-05-27 08:07:14'),
('39','Pendiente','80','2025-05-27 08:08:13'),
('40','Pendiente','91','2025-05-27 14:53:36'),
('41','Aprobado','92','2025-05-27 17:15:26'),
('42','Pendiente','93','2025-05-28 16:54:45'),
('43','Pendiente','94','2025-05-28 16:56:53'),
('44','Pendiente','95','2025-05-28 17:02:00'),
('45','Pendiente','96','2025-05-28 17:05:11'),
('46','Pendiente','97','2025-05-28 17:08:05'),
('47','Pendiente','98','2025-05-28 17:11:17'),
('48','Pendiente','99','2025-05-28 17:13:44'),
('49','Pendiente','100','2025-05-30 03:38:59'),
('50','Aprobado','101','2025-06-02 08:41:05');


-- Estructura para tabla `t_parroquia`
DROP TABLE IF EXISTS `t_parroquia`;
CREATE TABLE `t_parroquia` (
  `id_parroquia` int(11) NOT NULL AUTO_INCREMENT,
  `parroquia` varchar(30) NOT NULL,
  `estatus` int(11) NOT NULL,
  `fk_municipio` int(11) NOT NULL,
  PRIMARY KEY (`id_parroquia`,`fk_municipio`),
  KEY `fk_municipio` (`fk_municipio`),
  CONSTRAINT `t_parroquia_ibfk_1` FOREIGN KEY (`fk_municipio`) REFERENCES `t_municipio` (`id_municipio`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_parroquia`
INSERT INTO `t_parroquia` (`id_parroquia`,`parroquia`,`estatus`,`fk_municipio`) VALUES 
('1','Jaun de Villegas','1','1');


-- Estructura para tabla `t_pedidos`
DROP TABLE IF EXISTS `t_pedidos`;
CREATE TABLE `t_pedidos` (
  `id_pedidos` int(11) NOT NULL AUTO_INCREMENT,
  `fecha_pedido` datetime NOT NULL,
  `estatus` varchar(20) NOT NULL,
  `fk_cliente` int(11) NOT NULL,
  PRIMARY KEY (`id_pedidos`),
  KEY `fk_cliente` (`fk_cliente`),
  CONSTRAINT `t_pedidos_ibfk_1` FOREIGN KEY (`fk_cliente`) REFERENCES `t_cliente` (`id_cliente`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=102 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_pedidos`
INSERT INTO `t_pedidos` (`id_pedidos`,`fecha_pedido`,`estatus`,`fk_cliente`) VALUES 
('1','2025-05-15 19:54:33','En proceso','1'),
('2','2025-05-15 21:40:57','En proceso','1'),
('3','2025-05-20 18:01:19','En proceso','1'),
('4','2025-05-20 19:18:42','En proceso','1'),
('5','2025-05-20 20:30:44','En proceso','1'),
('6','2025-05-20 20:31:00','En proceso','1'),
('7','2025-05-21 00:57:41','En proceso','1'),
('8','2025-05-21 00:57:53','En proceso','1'),
('12','2025-05-21 09:37:49','En proceso','1'),
('13','2025-05-21 09:44:06','En proceso','1'),
('14','2025-05-21 09:44:11','En proceso','1'),
('16','2025-05-21 09:45:47','En proceso','1'),
('28','2025-05-24 23:32:03','En proceso','1'),
('29','2025-05-24 23:34:15','En proceso','1'),
('30','2025-05-24 23:46:29','En proceso','1'),
('31','2025-05-24 23:47:15','En proceso','1'),
('32','2025-05-24 23:47:46','En proceso','1'),
('33','2025-05-25 00:35:11','En proceso','1'),
('51','2025-05-26 23:57:22','En proceso','1'),
('58','2025-05-27 01:07:18','En proceso','1'),
('62','2025-05-27 01:11:14','En proceso','1'),
('63','2025-05-27 01:12:57','En proceso','1'),
('64','2025-05-27 01:13:22','En proceso','1'),
('65','2025-05-27 01:13:47','En proceso','1'),
('67','2025-05-27 01:14:25','En proceso','1'),
('68','2025-05-27 05:58:10','En proceso','1'),
('72','2025-05-27 06:09:07','En proceso','1'),
('76','2025-05-27 06:14:25','En proceso','1'),
('77','2025-05-27 06:24:21','En proceso','1'),
('78','2025-05-27 08:06:03','En proceso','1'),
('79','2025-05-27 08:07:14','En proceso','1'),
('80','2025-05-27 08:08:13','En proceso','1'),
('91','2025-05-27 14:53:36','En proceso','1'),
('92','2025-05-27 17:15:26','En proceso','1'),
('93','2025-05-28 16:54:45','En proceso','1'),
('94','2025-05-28 16:56:53','En proceso','1'),
('95','2025-05-28 17:02:00','En proceso','1'),
('96','2025-05-28 17:05:11','En proceso','1'),
('97','2025-05-28 17:08:05','En proceso','1'),
('98','2025-05-28 17:11:17','En proceso','1'),
('99','2025-05-28 17:13:44','En proceso','1'),
('100','2025-05-30 03:38:59','Pendiente','1'),
('101','2025-06-02 08:41:05','En proceso','1');


-- Estructura para tabla `t_producto`
DROP TABLE IF EXISTS `t_producto`;
CREATE TABLE `t_producto` (
  `id_producto` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `precio` float NOT NULL,
  `descripcion` varchar(150) NOT NULL,
  `stock` int(11) NOT NULL,
  `fecha_ingreso` datetime NOT NULL DEFAULT current_timestamp(),
  `fk_categoria` int(11) NOT NULL,
  `fk_emprendedor` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL,
  PRIMARY KEY (`id_producto`,`fk_categoria`,`fk_emprendedor`),
  KEY `fk_categoria` (`fk_categoria`),
  KEY `fk_emprendedor` (`fk_emprendedor`),
  CONSTRAINT `t_producto_ibfk_1` FOREIGN KEY (`fk_categoria`) REFERENCES `t_categoria` (`id_categoria`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `t_producto_ibfk_2` FOREIGN KEY (`fk_emprendedor`) REFERENCES `t_emprendedor` (`id_emprededor`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_producto`
INSERT INTO `t_producto` (`id_producto`,`nombre`,`precio`,`descripcion`,`stock`,`fecha_ingreso`,`fk_categoria`,`fk_emprendedor`,`status`) VALUES 
('2','Cesta de mimbre artesanal','45','Hermosa cesta tejida a mano con mimbre natura','0','2025-05-14 00:51:48','7','2','1'),
('3','Miel organica','100','Miel pura de abeja, recolectada de forma sostenible en los campos de PROJUMI.','77','2025-05-14 00:51:48','5','1','1'),
('25','Torta 3 leche','10','Consiste en un bizcocho bañado con tres tipos de lácteo: leche evaporada, media crema y leche condensada, que le dan su nombre.','68','2025-05-14 00:51:48','4','2','1');


-- Estructura para tabla `t_venta_evento`
DROP TABLE IF EXISTS `t_venta_evento`;
CREATE TABLE `t_venta_evento` (
  `id_venta_eventos` int(11) NOT NULL AUTO_INCREMENT,
  `cantidad` int(11) NOT NULL,
  `monto` int(11) NOT NULL,
  `fk_evento` int(11) NOT NULL,
  `fk_producto` int(11) NOT NULL,
  `fk_metodo_pago` int(11) NOT NULL,
  PRIMARY KEY (`id_venta_eventos`,`fk_evento`,`fk_producto`,`fk_metodo_pago`),
  KEY `fk_evento` (`fk_evento`),
  KEY `fk_producto` (`fk_producto`),
  KEY `fk_metodo_pago` (`fk_metodo_pago`),
  CONSTRAINT `t_venta_evento_ibfk_1` FOREIGN KEY (`fk_evento`) REFERENCES `t_evento` (`id_eventos`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `t_venta_evento_ibfk_2` FOREIGN KEY (`fk_producto`) REFERENCES `t_producto` (`id_producto`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `t_venta_evento_ibfk_3` FOREIGN KEY (`fk_metodo_pago`) REFERENCES `t_metodo_pago` (`id_metodo_pago`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

