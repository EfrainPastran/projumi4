USE `projumi` ;

-- -----------------------------------------------------
-- Placeholder table for view `projumi`.`vista_productos_mas_vendidos_global`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`vista_productos_mas_vendidos_global` (`emprendimiento` INT, `total_vendidos` INT);

-- -----------------------------------------------------
-- Placeholder table for view `projumi`.`vista_todas_las_ventas`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `projumi`.`vista_todas_las_ventas` (`id_pedidos` INT, `fecha_pedido` INT, `estatus` INT, `emprendedor_nombre` INT, `cliente_nombre` INT, `total_pedido` INT);

-- -----------------------------------------------------
-- View `projumi`.`vista_productos_mas_vendidos_global`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projumi`.`vista_productos_mas_vendidos_global`;
USE `projumi`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `projumi`.`vista_productos_mas_vendidos_global` AS select `em`.`emprendimiento` AS `emprendimiento`,sum(`d`.`cantidad`) AS `total_vendidos` from (((`projumi`.`t_pedidos` `p` join `projumi`.`t_detalle_pedido` `d` on(`d`.`pedidos_ID_PEDIDO` = `p`.`id_pedidos`)) join `projumi`.`t_producto` `pr` on(`pr`.`id_producto` = `d`.`producto_ID_PRODUCTO`)) join `projumi`.`t_emprendedor` `em` on(`em`.`id_emprededor` = `pr`.`fk_emprendedor`)) group by `em`.`emprendimiento` order by sum(`d`.`cantidad`) desc;

-- -----------------------------------------------------
-- View `projumi`.`vista_todas_las_ventas`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `projumi`.`vista_todas_las_ventas`;
USE `projumi`;
CREATE  OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `projumi`.`vista_todas_las_ventas` AS select `p`.`id_pedidos` AS `id_pedidos`,`p`.`fecha_pedido` AS `fecha_pedido`,`p`.`estatus` AS `estatus`,concat(`u_e`.`nombre`,' ',`u_e`.`apellido`) AS `emprendedor_nombre`,concat(`u_c`.`nombre`,' ',`u_c`.`apellido`) AS `cliente_nombre`,sum(`d`.`cantidad` * `d`.`precio_unitario`) AS `total_pedido` from ((((((`projumi`.`t_pedidos` `p` join `projumi`.`t_cliente` `c` on(`p`.`fk_cliente` = `c`.`id_cliente`)) join `seguridad`.`t_usuario` `u_c` on(`u_c`.`cedula` = `c`.`cedula`)) join `projumi`.`t_detalle_pedido` `d` on(`p`.`id_pedidos` = `d`.`pedidos_ID_PEDIDO`)) join `projumi`.`t_producto` `pr` on(`pr`.`id_producto` = `d`.`producto_ID_PRODUCTO`)) join `projumi`.`t_emprendedor` `e` on(`e`.`id_emprededor` = `pr`.`fk_emprendedor`)) join `seguridad`.`t_usuario` `u_e` on(`u_e`.`cedula` = `e`.`cedula`)) group by `p`.`id_pedidos`,`p`.`fecha_pedido`,`p`.`estatus`,`u_e`.`nombre`,`u_e`.`apellido`,`u_c`.`nombre`,`u_c`.`apellido` order by `p`.`fecha_pedido` desc;
