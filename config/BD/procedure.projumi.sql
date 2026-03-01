USE `projumi` ;

-- -----------------------------------------------------
-- procedure mostrarPedidosPorEmprendedor
-- -----------------------------------------------------

DELIMITER $$
USE `projumi`$$
DROP PROCEDURE IF EXISTS `mostrarPedidosPorEmprendedor`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `mostrarPedidosPorEmprendedor`(
    IN id_emprendedor_param VARCHAR(20),
    IN dateFrom_param DATE,
    IN dateTo_param DATE
)
BEGIN
    SELECT 
        p.id_pedidos, 
        p.fecha_pedido, 
        p.estatus, 
        CONCAT(u_e.nombre, ' ', u_e.apellido) AS emprendedor_nombre, 
        CONCAT(u_c.nombre, ' ', u_c.apellido) AS cliente_nombre, 
        SUM(d.cantidad * d.precio_unitario) AS total_pedido
    FROM t_pedidos p
    INNER JOIN t_cliente c ON p.fk_cliente = c.id_cliente
    INNER JOIN seguridad.t_usuario u_c ON u_c.cedula = c.cedula

    INNER JOIN t_detalle_pedido d ON p.id_pedidos = d.pedidos_ID_PEDIDO
    INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
    INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
    INNER JOIN seguridad.t_usuario u_e ON u_e.cedula = e.cedula

    WHERE e.cedula = id_emprendedor_param
      AND (dateFrom_param IS NULL OR DATE(p.fecha_pedido) >= dateFrom_param)
      AND (dateTo_param IS NULL OR DATE(p.fecha_pedido) <= dateTo_param)

    GROUP BY 
        p.id_pedidos, 
        p.fecha_pedido, 
        p.estatus, 
        u_e.nombre, u_e.apellido, 
        u_c.nombre, u_c.apellido

    ORDER BY p.fecha_pedido DESC;
END$$
DELIMITER ;


-- -----------------------------------------------------
-- procedure obtenerProductosMasVendidos
-- -----------------------------------------------------

DELIMITER $$
USE `projumi`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `obtenerProductosMasVendidos`(IN `cedula_emprendedor_param` VARCHAR(20))
BEGIN
    SELECT 
        pr.nombre AS producto,
        SUM(d.cantidad) AS total_vendidos
    FROM t_pedidos p
    INNER JOIN t_detalle_pedido d ON d.pedidos_ID_PEDIDO = p.id_pedidos
    INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
    INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
    WHERE e.cedula = cedula_emprendedor_param
    GROUP BY pr.nombre
    ORDER BY total_vendidos DESC;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure sp_obtener_envios_mensuales
-- -----------------------------------------------------

DELIMITER $$
USE `projumi`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_envios_mensuales`(IN `cedula_emprendedor` VARCHAR(20))
BEGIN
    SELECT 
        MONTH(p.fecha_pedido) AS mes,
        COUNT(DISTINCT e.id_envio) AS cantidad_envios
    FROM t_envio e
    INNER JOIN t_pedidos p ON p.id_pedidos = e.fk_pedido
    INNER JOIN t_detalle_pedido dp ON dp.pedidos_ID_PEDIDO = p.id_pedidos
    INNER JOIN t_producto pr ON pr.id_producto = dp.producto_ID_PRODUCTO
    INNER JOIN t_emprendedor em ON em.id_emprededor = pr.fk_emprendedor
    WHERE em.cedula = cedula_emprendedor
    GROUP BY MONTH(p.fecha_pedido)
    ORDER BY mes ASC;
END$$

DELIMITER ;

-- -----------------------------------------------------
-- procedure sp_obtener_ventas_mensuales
-- -----------------------------------------------------

DELIMITER $$
USE `projumi`$$
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_obtener_ventas_mensuales`(IN `cedula_emprendedor` VARCHAR(20))
BEGIN
    SELECT 
        MONTH(p.fecha_pedido) AS mes,
        SUM(d.cantidad * d.precio_unitario) AS total_dolares
    FROM t_pedidos p
    INNER JOIN t_detalle_pedido d ON d.pedidos_ID_PEDIDO = p.id_pedidos
    INNER JOIN t_producto pr ON pr.id_producto = d.producto_ID_PRODUCTO
    INNER JOIN t_emprendedor e ON e.id_emprededor = pr.fk_emprendedor
    WHERE e.cedula = cedula_emprendedor
    GROUP BY MONTH(p.fecha_pedido)
    ORDER BY mes ASC;
END$$

DELIMITER ;