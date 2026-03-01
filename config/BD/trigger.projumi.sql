USE `projumi`;
--
-- Disparadores `t_pagos`
--
DELIMITER $$
CREATE TRIGGER `al_actualizar_pago` AFTER UPDATE ON `t_pagos` FOR EACH ROW BEGIN
    DECLARE cedula_cliente INT;
    DECLARE id_usuario_receptor INT;
    DECLARE mensaje TEXT;

    -- Obtener la cédula del cliente desde el pedido
    SELECT c.cedula INTO cedula_cliente
    FROM t_pedidos p
    JOIN t_cliente c ON p.fk_cliente = c.id_cliente
    WHERE p.id_pedidos = NEW.fk_pedido;

    -- Buscar el ID de usuario asociado a esa cédula en la base seguridad
    SELECT u.id_usuario INTO id_usuario_receptor
    FROM seguridad.t_usuario u
    WHERE u.cedula = cedula_cliente;

    -- Verificar que se encontró un usuario válido (opcional)
    IF id_usuario_receptor IS NOT NULL THEN
        -- Construir el mensaje
        SET mensaje = CONCAT('Se ha ', NEW.estatus, ' el pago del pedido PED-', NEW.fk_pedido);

        -- Insertar la notificación
        INSERT INTO seguridad.t_notificacion (
            titulo,
            descripcion,
            fecha,
            fk_usuario_emisor,
            fk_usuario_receptor,
            status,
            ruta
        ) VALUES (
            'Verificación de pago',
            mensaje,
            NOW(),
            3, -- emisor (puedes cambiarlo si deseas hacerlo dinámico)
            id_usuario_receptor,
            0,
            '/pagos/index'
        );
    END IF;
END
$$
DELIMITER ;




--
-- Disparadores `t_envio`
--
DELIMITER $$
CREATE TRIGGER `al_actualizar_envio` AFTER UPDATE ON `t_envio` FOR EACH ROW BEGIN
    DECLARE cedula_cliente INT;
    DECLARE id_usuario_receptor INT;
    DECLARE mensaje TEXT;

    -- Obtener la cédula del cliente desde el pedido
    SELECT c.cedula INTO cedula_cliente
    FROM t_pedidos p
    JOIN t_cliente c ON p.fk_cliente = c.id_cliente
    WHERE p.id_pedidos = NEW.fk_pedido;

    -- Buscar el ID de usuario asociado a esa cédula en la base seguridad
    SELECT u.id_usuario INTO id_usuario_receptor
    FROM seguridad.t_usuario u
    WHERE u.cedula = cedula_cliente;

    -- Verificar que se encontró un usuario válido (opcional)
    IF id_usuario_receptor IS NOT NULL THEN
        -- Construir el mensaje
        SET mensaje = CONCAT('Se ha actualizado el envío del pedido PED-', NEW.fk_pedido);

        -- Insertar la notificación
        INSERT INTO seguridad.t_notificacion (
            titulo,
            descripcion,
            fecha,
            fk_usuario_emisor,
            fk_usuario_receptor,
            status,
            ruta
        ) VALUES (
            'Envío actualizado',
            mensaje,
            NOW(),
            3, -- emisor (puedes hacer esto dinámico luego)
            id_usuario_receptor,
            0,
            '/envios/index'
        );
    END IF;
END
$$
DELIMITER ;