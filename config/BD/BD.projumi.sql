SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Base de datos projumi
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `projumi` DEFAULT CHARACTER SET utf8mb4 ;

-- -----------------------------------------------------
-- Base de datos seguridad
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `seguridad` DEFAULT CHARACTER SET utf8mb4 ;

USE `projumi` ;

-- -----------------------------------------------------
-- Tabla `projumi`.`t_cambio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_cambio` (
  `id_cambio` INT(11) NOT NULL AUTO_INCREMENT,
  `tasa_cambio` DECIMAL(10,2) NOT NULL,
  `fecha_cambio` DATETIME NOT NULL,
  `estatus` INT(11) NOT NULL,
  PRIMARY KEY (`id_cambio`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_categoria` (
  `id_categoria` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(35) NOT NULL,
  `descripcion` VARCHAR(100) NOT NULL,
  `estatus` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id_categoria`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_cliente`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_cliente` (
  `id_cliente` INT(11) NOT NULL AUTO_INCREMENT,
  `cedula` INT(11) NOT NULL,
  `fecha_registro` DATE NOT NULL,
  `estatus` INT(11) NOT NULL,
  UNIQUE INDEX `client_cedula_UNIQUE` (`cedula` ASC),
  PRIMARY KEY (`id_cliente`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_municipio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_municipio` (
  `id_municipio` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(35) NOT NULL,
  `estatus` INT(11) NOT NULL,
  PRIMARY KEY (`id_municipio`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_parroquia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_parroquia` (
  `id_parroquia` INT(11) NOT NULL AUTO_INCREMENT,
  `parroquia` VARCHAR(30) NOT NULL,
  `estatus` INT(11) NOT NULL,
  `fk_municipio` INT(11) NOT NULL,
  PRIMARY KEY (`id_parroquia`),
  INDEX `fk_municipio` (`fk_municipio` ASC),
  CONSTRAINT `t_parroquia_ibfk_1`
    FOREIGN KEY (`fk_municipio`)
    REFERENCES `projumi`.`t_municipio` (`id_municipio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_emprendedor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_emprendedor` (
  `id_emprededor` INT(11) NOT NULL AUTO_INCREMENT,
  `cedula` INT(11) NOT NULL,
  `lugar_nacimiento` VARCHAR(45) NOT NULL,
  `estado_civil` VARCHAR(20) NOT NULL,
  `nacionalidad` VARCHAR(20) NOT NULL,
  `rif` VARCHAR(30) NULL DEFAULT NULL,
  `sexo` VARCHAR(20) NOT NULL,
  `alergia_medicamento` VARCHAR(45) NULL DEFAULT NULL,
  `alergia_alimento` VARCHAR(45) NULL DEFAULT NULL,
  `operado` VARCHAR(45) NULL DEFAULT NULL,
  `sacramento` VARCHAR(45) NULL DEFAULT NULL,
  `grupo_sangre` VARCHAR(20) NOT NULL,
  `religion` VARCHAR(45) NULL DEFAULT NULL,
  `grupo_activo` VARCHAR(45) NULL DEFAULT NULL,
  `cantidad_hijos` INT(11) NULL DEFAULT NULL,
  `carga_familiar` INT(11) NULL DEFAULT NULL,
  `casa_propia` VARCHAR(10) NULL DEFAULT NULL,
  `alquiler` VARCHAR(10) NULL DEFAULT NULL,
  `titulo_academico` VARCHAR(45) NULL DEFAULT NULL,
  `profesion` VARCHAR(45) NULL DEFAULT NULL,
  `oficio` VARCHAR(45) NULL DEFAULT NULL,
  `hobby` VARCHAR(45) NULL DEFAULT NULL,
  `conocimiento_projumi` VARCHAR(60) NULL DEFAULT NULL,
  `motivo_projumi` VARCHAR(60) NULL DEFAULT NULL,
  `aporte_projumi` VARCHAR(60) NULL DEFAULT NULL,
  `imagen` VARCHAR(100) NOT NULL,
  `emprendimiento` VARCHAR(50) NOT NULL,
  `estatus` INT(11) NOT NULL,
  `fk_parroquia` INT(11) NOT NULL,
  PRIMARY KEY (`id_emprededor`),
  INDEX `fk_parroquia` (`fk_parroquia` ASC),
  UNIQUE INDEX `empren_cedula_UNIQUE` (`cedula` ASC),
  CONSTRAINT `t_emprendedor_ibfk_1`
    FOREIGN KEY (`fk_parroquia`)
    REFERENCES `projumi`.`t_parroquia` (`id_parroquia`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_metodo_pago`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_metodo_pago` (
  `id_metodo_pago` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(50) NOT NULL,
  `descripcion` VARCHAR(100) NOT NULL,
  `estatus` INT(11) NOT NULL,
  PRIMARY KEY (`id_metodo_pago`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_datos_cuenta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_datos_cuenta` (
  `id_datos_cuenta` INT(11) NOT NULL AUTO_INCREMENT,
  `telefono` VARCHAR(11) NOT NULL,
  `banco` VARCHAR(45) NOT NULL,
  `correo` VARCHAR(45) NOT NULL,
  `numero_cuenta` VARCHAR(20) NOT NULL,
  `fk_emprendedor` INT(11) NOT NULL,
  `fk_metodo_pago` INT(11) NOT NULL,
  PRIMARY KEY (`id_datos_cuenta`),
  INDEX `fk_emprendedor` (`fk_emprendedor` ASC),
  INDEX `fk_metodo_pago` (`fk_metodo_pago` ASC),
  CONSTRAINT `t_datos_cuenta_ibfk_1`
    FOREIGN KEY (`fk_emprendedor`)
    REFERENCES `projumi`.`t_emprendedor` (`id_emprededor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `t_datos_cuenta_ibfk_2`
    FOREIGN KEY (`fk_metodo_pago`)
    REFERENCES `projumi`.`t_metodo_pago` (`id_metodo_pago`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_pedidos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_pedidos` (
  `id_pedidos` INT(11) NOT NULL AUTO_INCREMENT,
  `fecha_pedido` DATETIME NOT NULL,
  `estatus` VARCHAR(20) NOT NULL,
  `fk_cliente` INT(11) NOT NULL,
  PRIMARY KEY (`id_pedidos`),
  INDEX `fk_cliente` (`fk_cliente` ASC),
  CONSTRAINT `t_pedidos_ibfk_1`
    FOREIGN KEY (`fk_cliente`)
    REFERENCES `projumi`.`t_cliente` (`id_cliente`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_delivery`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_delivery` (
  `id_delivery` INT(11) NOT NULL AUTO_INCREMENT,
  `direccion_exacta` VARCHAR(60) NOT NULL,
  `destinatario` VARCHAR(45) NOT NULL,
  `telefono_destinatario` VARCHAR(45) NOT NULL,
  `correo_destinatario` VARCHAR(45) NULL DEFAULT NULL,
  `telefono_delivery` VARCHAR(45) NULL DEFAULT NULL,
  `fk_pedido` INT(11) NOT NULL,
  `estatus` VARCHAR(30) NOT NULL,
  PRIMARY KEY (`id_delivery`),
  INDEX `fk_pedido` (`fk_pedido` ASC),
  CONSTRAINT `t_delivery_ibfk_1`
    FOREIGN KEY (`fk_pedido`)
    REFERENCES `projumi`.`t_pedidos` (`id_pedidos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_moneda`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_moneda` (
  `id_moneda` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(15) NOT NULL,
  `simbolo` VARCHAR(5) NOT NULL,
  `pais` VARCHAR(45) NOT NULL,
  `estatus` INT(11) NOT NULL,
  `fk_cambio` INT(11) NOT NULL,
  PRIMARY KEY (`id_moneda`),
  INDEX `fk_cambio` (`fk_cambio` ASC),
  CONSTRAINT `t_moneda_ibfk_1`
    FOREIGN KEY (`fk_cambio`)
    REFERENCES `projumi`.`t_cambio` (`id_cambio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_detalle_metodo_pago`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_detalle_metodo_pago` (
  `id_detalle_metodo_pago` INT(11) NOT NULL AUTO_INCREMENT,
  `fk_metodo_pago` INT(11) NOT NULL,
  `fk_moneda` INT(11) NOT NULL,
  PRIMARY KEY (`id_detalle_metodo_pago`),
  INDEX `fk_metodo_pago` (`fk_metodo_pago` ASC),
  INDEX `fk_moneda` (`fk_moneda` ASC),
  CONSTRAINT `t_detalle_metodo_pago_ibfk_1`
    FOREIGN KEY (`fk_metodo_pago`)
    REFERENCES `projumi`.`t_metodo_pago` (`id_metodo_pago`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `t_detalle_metodo_pago_ibfk_2`
    FOREIGN KEY (`fk_moneda`)
    REFERENCES `projumi`.`t_moneda` (`id_moneda`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_pagos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_pagos` (
  `id_pagos` INT(11) NOT NULL AUTO_INCREMENT,
  `estatus` VARCHAR(45) NOT NULL,
  `fk_pedido` INT(11) NOT NULL,
  `fecha_pago` DATETIME NULL DEFAULT NULL,
  PRIMARY KEY (`id_pagos`),
  INDEX `fk_pedido` (`fk_pedido` ASC),
  CONSTRAINT `t_pagos_ibfk_1`
    FOREIGN KEY (`fk_pedido`)
    REFERENCES `projumi`.`t_pedidos` (`id_pedidos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_detalle_pago`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_detalle_pago` (
  `id_detalle_pago` INT(11) NOT NULL AUTO_INCREMENT,
  `fk_pago` INT(11) NOT NULL,
  `fk_detalle_metodo_pago` INT(11) NOT NULL,
  `monto` DECIMAL(10,2) NOT NULL,
  `referencia` VARCHAR(20) NULL DEFAULT NULL,
  `comprobante` VARCHAR(100) NOT NULL,
  PRIMARY KEY (`id_detalle_pago`),
  INDEX `fk_detalle_metodo_pago` (`fk_detalle_metodo_pago` ASC),
  INDEX `fk_pago` (`fk_pago` ASC),
  CONSTRAINT `t_detalle_pago_ibfk_1`
    FOREIGN KEY (`fk_detalle_metodo_pago`)
    REFERENCES `projumi`.`t_detalle_metodo_pago` (`id_detalle_metodo_pago`)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `t_detalle_pago_ibfk_2`
    FOREIGN KEY (`fk_pago`)
    REFERENCES `projumi`.`t_pagos` (`id_pagos`)
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_producto`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_producto` (
  `id_producto` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `precio` FLOAT NOT NULL,
  `descripcion` VARCHAR(150) NOT NULL,
  `stock` INT(11) NOT NULL,
  `fecha_ingreso` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `fk_categoria` INT(11) NOT NULL,
  `fk_emprendedor` INT(11) NOT NULL,
  `status` TINYINT(1) NOT NULL,
  PRIMARY KEY (`id_producto`),
  INDEX `fk_categoria` (`fk_categoria` ASC),
  INDEX `fk_emprendedor` (`fk_emprendedor` ASC),
  CONSTRAINT `t_producto_ibfk_1`
    FOREIGN KEY (`fk_categoria`)
    REFERENCES `projumi`.`t_categoria` (`id_categoria`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `t_producto_ibfk_2`
    FOREIGN KEY (`fk_emprendedor`)
    REFERENCES `projumi`.`t_emprendedor` (`id_emprededor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_detalle_pedido`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_detalle_pedido` (
  `id_detalle_pedido` INT(11) NOT NULL AUTO_INCREMENT,
  `producto_id_producto` INT(11) NOT NULL,
  `pedidos_id_pedido` INT(11) NOT NULL,
  `cantidad` INT(11) NOT NULL,
  `precio_unitario` INT(11) NOT NULL,
  PRIMARY KEY (`id_detalle_pedido`),
  INDEX `pedidos_id_pedido` (`pedidos_id_pedido` ASC),
  INDEX `producto_id_producto` (`producto_id_producto` ASC),
  CONSTRAINT `t_detalle_pedido_ibfk_1`
    FOREIGN KEY (`producto_id_producto`)
    REFERENCES `projumi`.`t_producto` (`id_producto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `t_detalle_pedido_ibfk_2`
    FOREIGN KEY (`pedidos_id_pedido`)
    REFERENCES `projumi`.`t_pedidos` (`id_pedidos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_empresa_envio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_empresa_envio` (
  `id_empresa_envio` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `telefono` VARCHAR(45) NOT NULL,
  `direccion` VARCHAR(100) NOT NULL,
  `estatus` INT(11) NOT NULL,
  PRIMARY KEY (`id_empresa_envio`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_envio`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_envio` (
  `id_envio` INT(11) NOT NULL AUTO_INCREMENT,
  `direccion_envio` VARCHAR(45) NOT NULL,
  `estatus` VARCHAR(45) NOT NULL,
  `numero_seguimiento` VARCHAR(45) NOT NULL,
  `fk_empresa_envio` INT(11) NOT NULL,
  `fk_pedido` INT(11) NOT NULL,
  PRIMARY KEY (`id_envio`),
  INDEX `fk_empresa_envio` (`fk_empresa_envio` ASC),
  INDEX `fk_pedido` (`fk_pedido` ASC),
  CONSTRAINT `t_envio_ibfk_1`
    FOREIGN KEY (`fk_empresa_envio`)
    REFERENCES `projumi`.`t_empresa_envio` (`id_empresa_envio`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `t_envio_ibfk_2`
    FOREIGN KEY (`fk_pedido`)
    REFERENCES `projumi`.`t_pedidos` (`id_pedidos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_evento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_evento` (
  `id_eventos` INT(11) NOT NULL AUTO_INCREMENT,
  `fecha_inicio` DATE NOT NULL,
  `fecha_fin` DATE NULL DEFAULT NULL,
  `nombre` VARCHAR(45) NOT NULL,
  `direccion` VARCHAR(100) NOT NULL,
  `status` INT(11) NOT NULL,
  PRIMARY KEY (`id_eventos`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_galeria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_galeria` (
  `id_galeria` INT(11) NOT NULL AUTO_INCREMENT,
  `fk_producto` INT(11) NOT NULL,
  `ruta` VARCHAR(160) NOT NULL,
  PRIMARY KEY (`id_galeria`),
  INDEX `fk_producto` (`fk_producto` ASC),
  CONSTRAINT `t_galeria_ibfk_1`
    FOREIGN KEY (`fk_producto`)
    REFERENCES `projumi`.`t_producto` (`id_producto`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `projumi`.`t_venta_evento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`t_venta_evento` (
  `id_venta_eventos` INT(11) NOT NULL AUTO_INCREMENT,
  `cantidad` INT(11) NOT NULL,
  `monto` INT(11) NOT NULL,
  `fk_evento` INT(11) NOT NULL,
  `fk_producto` INT(11) NOT NULL,
  `fk_metodo_pago` INT(11) NOT NULL,
  PRIMARY KEY (`id_venta_eventos`),
  INDEX `fk_evento` (`fk_evento` ASC),
  INDEX `fk_producto` (`fk_producto` ASC),
  INDEX `fk_metodo_pago` (`fk_metodo_pago` ASC),
  CONSTRAINT `t_venta_evento_ibfk_1`
    FOREIGN KEY (`fk_evento`)
    REFERENCES `projumi`.`t_evento` (`id_eventos`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `t_venta_evento_ibfk_2`
    FOREIGN KEY (`fk_producto`)
    REFERENCES `projumi`.`t_producto` (`id_producto`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `t_venta_evento_ibfk_3`
    FOREIGN KEY (`fk_metodo_pago`)
    REFERENCES `projumi`.`t_metodo_pago` (`id_metodo_pago`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;

USE `seguridad` ;

-- -----------------------------------------------------
-- Tabla `seguridad`.`t_rol`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `seguridad`.`t_rol` (
  `id_rol` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `descripcion_rol` VARCHAR(45) NOT NULL,
  `estatus` INT(11) NOT NULL,
  PRIMARY KEY (`id_rol`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `seguridad`.`t_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `seguridad`.`t_usuario` (
  `id_usuario` INT(11) NOT NULL AUTO_INCREMENT,
  `cedula` VARCHAR(8) NOT NULL,
  `nombre` VARCHAR(45) NOT NULL,
  `apellido` VARCHAR(45) NOT NULL,
  `correo` VARCHAR(45) NOT NULL,
  `password` VARCHAR(160) NOT NULL,
  `imgperfil` VARCHAR(100) NOT NULL DEFAULT '/public/img/default_profile.png',
  `direccion` VARCHAR(60) NOT NULL,
  `telefono` VARCHAR(11) NOT NULL,
  `fecha_registro` DATETIME NOT NULL,
  `fecha_nacimiento` DATE NOT NULL,
  `estatus` INT(11) NOT NULL,
  `fk_rol` INT(11) NOT NULL,
  PRIMARY KEY (`id_usuario`),
  INDEX `fk_rol` (`fk_rol` ASC),
  UNIQUE INDEX `user_cedula_UNIQUE` (`cedula` ASC),
  CONSTRAINT `t_usuario_ibfk_1`
    FOREIGN KEY (`fk_rol`)
    REFERENCES `seguridad`.`t_rol` (`id_rol`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `seguridad`.`t_bitacora`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `seguridad`.`t_bitacora` (
  `id_bitacora` INT(11) NOT NULL AUTO_INCREMENT,
  `modulo_accionado` VARCHAR(45) NOT NULL,
  `descripcion_accion` VARCHAR(45) NOT NULL,
  `fecha_registro` DATETIME NOT NULL,
  `fk_usuario` INT(11) NOT NULL,
  PRIMARY KEY (`id_bitacora`),
  INDEX `fk_usuario` (`fk_usuario` ASC),
  CONSTRAINT `t_bitacora_ibfk_1`
    FOREIGN KEY (`fk_usuario`)
    REFERENCES `seguridad`.`t_usuario` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `seguridad`.`t_mantenimiento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `seguridad`.`t_mantenimiento` (
  `id_mantenimiento` INT(11) NOT NULL AUTO_INCREMENT,
  `fecha` DATETIME NOT NULL,
  `url` VARCHAR(45) NOT NULL,
  `status` INT(11) NOT NULL,
  `fk_usuario` INT(11) NOT NULL,
  PRIMARY KEY (`id_mantenimiento`),
  INDEX `fk_usuario` (`fk_usuario` ASC),
  CONSTRAINT `t_mantenimiento_ibfk_1`
    FOREIGN KEY (`fk_usuario`)
    REFERENCES `seguridad`.`t_usuario` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `seguridad`.`t_modulo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `seguridad`.`t_modulo` (
  `id_modulo` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `ruta` VARCHAR(100) NULL,
  `icono` VARCHAR(50) NULL,
  `menu_padre` INT NULL, 
  `orden` INT DEFAULT 0,
  `estatus` INT(11) NOT NULL,
  PRIMARY KEY (`id_modulo`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `seguridad`.`t_notificacion`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `seguridad`.`t_notificacion` (
  `id_notificacion` INT(11) NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(100) NOT NULL,
  `descripcion` TEXT NOT NULL,
  `fecha` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `fk_usuario_emisor` INT(11) NOT NULL,
  `fk_usuario_receptor` INT(11) NOT NULL,
  `status` TINYINT(1) NOT NULL DEFAULT 0,
  `ruta` VARCHAR(255) NULL DEFAULT NULL,
  PRIMARY KEY (`id_notificacion`),
  INDEX `fk_usuario_emisor` (`fk_usuario_emisor` ASC),
  INDEX `fk_usuario_receptor` (`fk_usuario_receptor` ASC),
  CONSTRAINT `t_notificacion_ibfk_1`
    FOREIGN KEY (`fk_usuario_emisor`)
    REFERENCES `seguridad`.`t_usuario` (`id_usuario`)
    ON DELETE CASCADE,
  CONSTRAINT `t_notificacion_ibfk_2`
    FOREIGN KEY (`fk_usuario_receptor`)
    REFERENCES `seguridad`.`t_usuario` (`id_usuario`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `seguridad`.`t_permisos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `seguridad`.`t_permisos` (
  `id_permisos` INT(11) NOT NULL AUTO_INCREMENT,
  `nombre` VARCHAR(45) NOT NULL,
  `descriccion_de_permiso` VARCHAR(45) NOT NULL,
  `estatus` INT(11) NOT NULL,
  PRIMARY KEY (`id_permisos`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COMMENT = 't';


-- -----------------------------------------------------
-- Tabla `seguridad`.`t_permiso_rol_modulo`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `seguridad`.`t_permiso_rol_modulo` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `fk_rol` INT(11) NOT NULL,
  `fk_modulo` INT(11) NOT NULL,
  `fk_permiso` INT(11) NOT NULL,
  `estatus` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `fk_rol` (`fk_rol` ASC),
  INDEX `fk_modulo` (`fk_modulo` ASC),
  INDEX `fk_permiso` (`fk_permiso` ASC),
  CONSTRAINT `t_permiso_rol_modulo_ibfk_1`
    FOREIGN KEY (`fk_rol`)
    REFERENCES `seguridad`.`t_rol` (`id_rol`)
    ON DELETE CASCADE,
  CONSTRAINT `t_permiso_rol_modulo_ibfk_2`
    FOREIGN KEY (`fk_modulo`)
    REFERENCES `seguridad`.`t_modulo` (`id_modulo`)
    ON DELETE CASCADE,
  CONSTRAINT `t_permiso_rol_modulo_ibfk_3`
    FOREIGN KEY (`fk_permiso`)
    REFERENCES `seguridad`.`t_permisos` (`id_permisos`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;


-- -----------------------------------------------------
-- Tabla `seguridad`.`t_personal`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `seguridad`.`t_personal` (
  `id_personal` INT(11) NOT NULL AUTO_INCREMENT,
  `cargo` VARCHAR(45) NOT NULL,
  `departamento` VARCHAR(45) NOT NULL,
  `estatus` VARCHAR(20) NOT NULL,
  `fk_usuario` INT(11) NOT NULL,
  PRIMARY KEY (`id_personal`),
  INDEX `fk_usuario` (`fk_usuario` ASC),
  CONSTRAINT `t_personal_ibfk_1`
    FOREIGN KEY (`fk_usuario`)
    REFERENCES `seguridad`.`t_usuario` (`id_usuario`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
