-- Backup de la base de datos MAIN
-- Generado el: 2025-06-03 18:39:37



-- Estructura para tabla `t_bitacora`
DROP TABLE IF EXISTS `t_bitacora`;
CREATE TABLE `t_bitacora` (
  `id_bitacora` int(11) NOT NULL AUTO_INCREMENT,
  `modulo_accionado` varchar(45) NOT NULL,
  `descripcion_accion` varchar(45) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `fk_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_bitacora`,`fk_usuario`),
  KEY `fk_usuario` (`fk_usuario`),
  CONSTRAINT `t_bitacora_ibfk_1` FOREIGN KEY (`fk_usuario`) REFERENCES `t_usuario` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_bitacora`
INSERT INTO `t_bitacora` (`id_bitacora`,`modulo_accionado`,`descripcion_accion`,`fecha_registro`,`fk_usuario`) VALUES 
('1','dfgdfg','dfgdfg','2025-05-29 22:42:04','1'),
('2','Registro de producto','El usuario ha registrado un nuevo producto: v','2025-05-30 00:22:00','1'),
('3','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:20:16','1'),
('4','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:20:34','1'),
('5','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:20:49','1'),
('6','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:22:32','1'),
('7','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:23:26','1'),
('8','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:24:44','1'),
('9','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:25:00','1'),
('10','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:25:03','1'),
('11','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:25:33','1'),
('12','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:27:40','1'),
('13','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:41:11','1'),
('14','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-02 12:44:25','1'),
('15','Registro de producto','El usuario ha registrado un nuevo producto: e','2025-06-02 20:41:27','2'),
('16','Registro de producto','El usuario ha registrado un nuevo producto: p','2025-06-02 21:03:41','2'),
('17','Registro de producto','El usuario ha registrado un nuevo producto: p','2025-06-02 21:05:53','2'),
('18','Registro de producto','El usuario ha registrado un nuevo producto: p','2025-06-02 21:06:26','1'),
('19','Registro de producto','El usuario ha registrado un nuevo producto: p','2025-06-02 23:41:59','1'),
('20','Registro de producto','El usuario ha registrado un nuevo producto: p','2025-06-02 23:42:25','1'),
('21','Registro de producto','El usuario ha registrado un nuevo producto: M','2025-06-03 18:12:29','1'),
('22','Registro de producto','El usuario ha registrado un nuevo producto: P','2025-06-03 18:14:34','1'),
('23','Registro de producto','El usuario ha registrado un nuevo producto: s','2025-06-03 18:15:27','1');


-- Estructura para tabla `t_mantenimiento`
DROP TABLE IF EXISTS `t_mantenimiento`;
CREATE TABLE `t_mantenimiento` (
  `id_mantenimiento` int(11) NOT NULL AUTO_INCREMENT,
  `fecha` datetime NOT NULL,
  `url` varchar(45) NOT NULL,
  `status` int(11) NOT NULL,
  `fk_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_mantenimiento`,`fk_usuario`),
  KEY `fk_usuario` (`fk_usuario`),
  CONSTRAINT `t_mantenimiento_ibfk_1` FOREIGN KEY (`fk_usuario`) REFERENCES `t_usuario` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Estructura para tabla `t_modulo`
DROP TABLE IF EXISTS `t_modulo`;
CREATE TABLE `t_modulo` (
  `id_modulo` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_modulo`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_modulo`
INSERT INTO `t_modulo` (`id_modulo`,`nombre`,`estatus`) VALUES 
('1','Pedido','1'),
('2','Pago','1');


-- Estructura para tabla `t_notificacion`
DROP TABLE IF EXISTS `t_notificacion`;
CREATE TABLE `t_notificacion` (
  `id_notificacion` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(100) NOT NULL,
  `descripcion` text NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `fk_usuario_emisor` int(11) NOT NULL,
  `fk_usuario_receptor` int(11) NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 0,
  `ruta` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_notificacion`),
  KEY `fk_usuario_emisor` (`fk_usuario_emisor`),
  KEY `fk_usuario_receptor` (`fk_usuario_receptor`),
  CONSTRAINT `t_notificacion_ibfk_1` FOREIGN KEY (`fk_usuario_emisor`) REFERENCES `t_usuario` (`id_usuario`) ON DELETE CASCADE,
  CONSTRAINT `t_notificacion_ibfk_2` FOREIGN KEY (`fk_usuario_receptor`) REFERENCES `t_usuario` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Estructura para tabla `t_permiso_rol_modulo`
DROP TABLE IF EXISTS `t_permiso_rol_modulo`;
CREATE TABLE `t_permiso_rol_modulo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fk_rol` int(11) NOT NULL,
  `fk_modulo` int(11) NOT NULL,
  `fk_permiso` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_rol` (`fk_rol`),
  KEY `fk_modulo` (`fk_modulo`),
  KEY `fk_permiso` (`fk_permiso`),
  CONSTRAINT `t_permiso_rol_modulo_ibfk_1` FOREIGN KEY (`fk_rol`) REFERENCES `t_rol` (`id_rol`) ON DELETE CASCADE,
  CONSTRAINT `t_permiso_rol_modulo_ibfk_2` FOREIGN KEY (`fk_modulo`) REFERENCES `t_modulo` (`id_modulo`) ON DELETE CASCADE,
  CONSTRAINT `t_permiso_rol_modulo_ibfk_3` FOREIGN KEY (`fk_permiso`) REFERENCES `t_permisos` (`id_permisos`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_permiso_rol_modulo`
INSERT INTO `t_permiso_rol_modulo` (`id`,`fk_rol`,`fk_modulo`,`fk_permiso`) VALUES 
('100','2','2','1'),
('101','2','2','2'),
('103','2','2','4'),
('109','1','1','1'),
('110','1','1','2'),
('111','1','1','4'),
('112','1','2','1'),
('113','1','2','4');


-- Estructura para tabla `t_permisos`
DROP TABLE IF EXISTS `t_permisos`;
CREATE TABLE `t_permisos` (
  `id_permisos` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descriccion de permiso` varchar(45) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_permisos`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='t';

-- Datos para la tabla `t_permisos`
INSERT INTO `t_permisos` (`id_permisos`,`nombre`,`descriccion de permiso`,`estatus`) VALUES 
('1','consultar','','1'),
('2','registrar','','1'),
('4','actualizar','','1'),
('5','eliminar','','1');


-- Estructura para tabla `t_personal`
DROP TABLE IF EXISTS `t_personal`;
CREATE TABLE `t_personal` (
  `id_personal` int(11) NOT NULL AUTO_INCREMENT,
  `cargo` varchar(45) NOT NULL,
  `departamento` varchar(45) NOT NULL,
  `estatus` varchar(20) NOT NULL,
  `fk_usuario` int(11) NOT NULL,
  PRIMARY KEY (`id_personal`,`fk_usuario`),
  KEY `fk_usuario` (`fk_usuario`),
  CONSTRAINT `t_personal_ibfk_1` FOREIGN KEY (`fk_usuario`) REFERENCES `t_usuario` (`id_usuario`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



-- Estructura para tabla `t_rol`
DROP TABLE IF EXISTS `t_rol`;
CREATE TABLE `t_rol` (
  `id_rol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `descripcion_rol` varchar(45) NOT NULL,
  `estatus` int(11) NOT NULL,
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_rol`
INSERT INTO `t_rol` (`id_rol`,`nombre`,`descripcion_rol`,`estatus`) VALUES 
('1','Administrador','Podras publicar todos lo que necesitas vender','1'),
('2','Super Usuario','Soporte tecnico','1'),
('3','Usuario','Solo para comprar','1');


-- Estructura para tabla `t_usuario`
DROP TABLE IF EXISTS `t_usuario`;
CREATE TABLE `t_usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `cedula` int(11) NOT NULL,
  `nombre` varchar(45) NOT NULL,
  `apellido` varchar(45) NOT NULL,
  `correo` varchar(45) NOT NULL,
  `password` varchar(160) NOT NULL,
  `direccion` varchar(60) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `fecha_registro` datetime NOT NULL,
  `fecha_nacimiento` date NOT NULL,
  `estatus` int(11) NOT NULL,
  `fk_rol` int(11) NOT NULL,
  PRIMARY KEY (`id_usuario`,`fk_rol`),
  KEY `fk_rol` (`fk_rol`),
  CONSTRAINT `t_usuario_ibfk_1` FOREIGN KEY (`fk_rol`) REFERENCES `t_rol` (`id_rol`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Datos para la tabla `t_usuario`
INSERT INTO `t_usuario` (`id_usuario`,`cedula`,`nombre`,`apellido`,`correo`,`password`,`direccion`,`telefono`,`fecha_registro`,`fecha_nacimiento`,`estatus`,`fk_rol`) VALUES 
('1','27759045','Efrain','Pastran','efrain_pastran@gmail.com','$2y$10$QP4NnioumUvUW5d2dZKJ9O3zoGgFyy0zCJQfoLYSUlrE8TWvKvs7G','avenida tuya','04165567448','2025-05-13 23:24:57','1997-05-01','1','3'),
('2','28123456','Rosmery','Mejia','','$2y$10$QP4NnioumUvUW5d2dZKJ9O3zoGgFyy0zCJQfoLYSUlrE8TWvKvs7G','','','2025-05-17 01:13:58','1997-05-01','1','3'),
('3','26123456','Jenny','Colmenarez','jenny@gmail.com','$2y$10$QP4NnioumUvUW5d2dZKJ9O3zoGgFyy0zCJQfoLYSUlrE8TWvKvs7G','','','2025-05-22 13:15:35','1997-05-01','1','3'),
('4','25123456','Jeanette Thais','Lucena Alvarez','jeanette@gmail.com','$2y$10$QP4NnioumUvUW5d2dZKJ9O3zoGgFyy0zCJQfoLYSUlrE8TWvKvs7G','','','2025-05-22 13:17:21','1997-05-01','1','3'),
('5','26502663','Luis','Sivira','luissivira669@gmail.com','$2y$10$QP4NnioumUvUW5d2dZKJ9O3zoGgFyy0zCJQfoLYSUlrE8TWvKvs7G','','','2025-05-22 10:36:05','1997-05-01','1','3'),
('7','20123456','Jesus','Rivas','jesusrivas@gmail.com','$2y$10$QP4NnioumUvUW5d2dZKJ9O3zoGgFyy0zCJQfoLYSUlrE8TWvKvs7G','','','2025-05-22 11:08:59','1997-05-01','1','1'),
('9','10123456','Laura','Mendez','laura@gmail.com','$2y$10$QP4NnioumUvUW5d2dZKJ9O3zoGgFyy0zCJQfoLYSUlrE8TWvKvs7G','','','2025-05-28 19:23:57','0000-00-00','1','3');
