<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-12">
            <a href="<?php echo APP_URL; ?>/envios" class="btn btn-secondary mb-3">
                <i class="fas fa-arrow-left me-2"></i>Volver a envíos
            </a>
            <h2 class="fw-bold">Detalles del Envío #<?php echo $envio['id_envio']; ?></h2>
            <p class="text-muted">Número de seguimiento: <?php echo $envio['numero_seguimiento']; ?></p>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Información del Envío</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <p><strong>Estado actual:</strong> 
                                <span class="badge <?php echo [
                                    'pendiente' => 'bg-primary',
                                    'en_transito' => 'bg-warning text-dark',
                                    'entregado' => 'bg-success',
                                    'cancelado' => 'bg-danger'
                                ][$envio['estatus']] ?? 'bg-secondary'; ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $envio['estatus'])); ?>
                                </span>
                            </p>
                            
                            <?php if ($envio['estatus'] != 'entregado' && $envio['estatus'] != 'cancelado'): ?>
                                <form action="<?php echo APP_URL; ?>/envios/actualizarEstado" method="post" class="mb-4">
                                    <input type="hidden" name="id_envio" value="<?php echo $envio['id_envio']; ?>">
                                    <div class="mb-3">
                                        <label for="nuevo_estado" class="form-label">Cambiar estado:</label>
                                        <select name="nuevo_estado" id="nuevo_estado" class="form-select">
                                            <option value="en_transito">En tránsito</option>
                                            <option value="entregado">Entregado</option>
                                            <option value="cancelado">Cancelado</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="comentario" class="form-label">Comentario (opcional):</label>
                                        <textarea name="comentario" id="comentario" class="form-control" rows="2"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Actualizar estado</button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Fecha de envío:</strong> <?php echo date('d/m/Y H:i', strtotime($envio['fecha_envio'])); ?></p>
                            <p><strong>Empresa de envío:</strong> <?php echo htmlspecialchars($envio['empresa_envio']); ?></p>
                            <p><strong>Dirección de envío:</strong> <?php echo nl2br(htmlspecialchars($envio['direccion_envio'])); ?></p>
                        </div>
                        <div class="col-md-4">
                            <p><strong>Fecha del pedido:</strong> <?php echo date('d/m/Y H:i', strtotime($envio['fecha_pedido'])); ?></p>
                            <?php if ($envio['estatus'] == 'entregado'): ?>
                                <p><strong>Fecha de entrega:</strong> 
                                    <?php 
                                        $entrega = array_filter($historial, function($item) {
                                            return $item['estado_nuevo'] == 'entregado';
                                        });
                                        if (!empty($entrega)) {
                                            echo date('d/m/Y H:i', strtotime(reset($entrega)['fecha_cambio']));
                                        }
                                    ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Información del Producto</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="<?php echo APP_URL; ?>/public/img/productos/<?php echo $envio['id_producto']; ?>.jpg" 
                                 class="img-fluid rounded" alt="<?php echo htmlspecialchars($envio['nombre_producto']); ?>">
                        </div>
                        <div class="col-md-8">
                            <h4><?php echo htmlspecialchars($envio['nombre_producto']); ?></h4>
                            <p class="text-muted"><?php echo htmlspecialchars($envio['categoria_producto']); ?></p>
                            <p><?php echo htmlspecialchars($envio['descripcion']); ?></p>
                            <p><strong>Precio:</strong> $<?php echo number_format($envio['precio'], 2); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">Información del Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="me-3">
                            <i class="fas fa-user-circle fa-3x text-secondary"></i>
                        </div>
                        <div>
                            <h4 class="mb-0"><?php echo htmlspecialchars($envio['nombre_cliente']); ?></h4>
                            <p class="text-muted mb-0">Cliente</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <p><i class="fas fa-phone me-2"></i> <?php echo htmlspecialchars($envio['telefono_cliente'] ?? 'N/A'); ?></p>
                        <p><i class="fas fa-envelope me-2"></i> <?php echo htmlspecialchars($envio['email_cliente'] ?? 'N/A'); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Historial de Estados</h5>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <?php foreach ($historial as $evento): ?>
                            <div class="timeline-item">
                                <div class="timeline-item-marker">
                                    <div class="timeline-item-marker-indicator 
                                        <?php echo [
                                            'pendiente' => 'bg-primary',
                                            'en_transito' => 'bg-warning',
                                            'entregado' => 'bg-success',
                                            'cancelado' => 'bg-danger'
                                        ][$evento['estado_nuevo']] ?? 'bg-secondary'; ?>">
                                    </div>
                                </div>
                                <div class="timeline-item-content">
                                    <div class="d-flex justify-content-between">
                                        <span class="fw-bold">
                                            <?php echo ucfirst(str_replace('_', ' ', $evento['estado_nuevo'])); ?>
                                        </span>
                                        <small class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($evento['fecha_cambio'])); ?>
                                        </small>
                                    </div>
                                    <?php if (!empty($evento['estado_anterior'])): ?>
                                        <p class="mb-1">
                                            <small>Estado anterior: 
                                                <?php echo ucfirst(str_replace('_', ' ', $evento['estado_anterior'])); ?>
                                            </small>
                                        </p>
                                    <?php endif; ?>
                                    <?php if (!empty($evento['comentario'])): ?>
                                        <p class="mb-0"><?php echo htmlspecialchars($evento['comentario']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>