import { mostrarAlerta } from './alertas.js';
import { API_CONFIG } from './config.js';
document.addEventListener('DOMContentLoaded', () => {
    const cedulaInput = document.getElementById('cedula');
    const mensajeDiv = document.getElementById('mensaje');

    let ultimaCedulaConsultada = ''; // para evitar repetir la misma petición

    cedulaInput.addEventListener('input', () => {
        const cedula = cedulaInput.value.trim();

        // Solo continuar si tiene al menos 6 caracteres numéricos
        if (cedula.length >= 6 && /^\d+$/.test(cedula)) {
            // Evitar peticiones duplicadas
            if (cedula === ultimaCedulaConsultada) return;

            ultimaCedulaConsultada = cedula;

            fetch(API_CONFIG + `/ventaspresencial/consultarCliente?cedula=${cedula}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        const data = result.data;
                        document.getElementById('nombre').value = data.nombre;
                        document.getElementById('apellido').value = data.apellido;
                        document.getElementById('correo').value = data.correo;
                        document.getElementById('telefono').value = data.telefono;
                        document.getElementById('direccion').value = data.direccion;
                        document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento;

                        mensajeDiv.innerHTML = `<div class="alert alert-success">Cliente encontrado y datos cargados correctamente.</div>`;
                    } else {
                        // Cliente no encontrado, limpiar campos y permitir ingreso
                        limpiarCampos();
                        mensajeDiv.innerHTML = `<div class="alert alert-warning">${result.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error en la consulta:', error);
                    mensajeDiv.innerHTML = `<div class="alert alert-danger">Error en la consulta. Intente nuevamente.</div>`;
                });
        } else {
            // Si borra o escribe menos de 6 caracteres, limpiamos el estado
            ultimaCedulaConsultada = '';
            mensajeDiv.innerHTML = '';
            limpiarCampos();
        }
    });

    function limpiarCampos() {
        document.getElementById('nombre').value = '';
        document.getElementById('apellido').value = '';
        document.getElementById('correo').value = '';
        document.getElementById('telefono').value = '';
        document.getElementById('direccion').value = '';
        document.getElementById('fecha_nacimiento').value = '';
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const cedulaInput = document.getElementById('cedula');
    const mensajeDiv = document.getElementById('mensaje');

    const campos = [
        'nombre',
        'apellido',
        'correo',
        'telefono',
        'direccion',
        'fecha_nacimiento'
    ];

    let ultimaCedulaConsultada = '';

    cedulaInput.addEventListener('input', () => {
        const cedula = cedulaInput.value.trim();

        if (cedula.length >= 6 && /^\d+$/.test(cedula)) {
            if (cedula === ultimaCedulaConsultada) return;

            ultimaCedulaConsultada = cedula;

            fetch(API_CONFIG + `/ventaspresencial/consultarCliente?cedula=${cedula}`)
                .then(response => response.json())
                .then(result => {
                    if (result.status === 'success') {
                        const data = result.data;
                        setCampos(data);
                        setCamposHabilitados(false); // ← desactiva los campos
                        mensajeDiv.innerHTML = `<div class="alert alert-success">Cliente encontrado y datos cargados correctamente.</div>`;
                    } else {
                        limpiarCampos();
                        setCamposHabilitados(true); // ← permite escribir
                        mensajeDiv.innerHTML = `<div class="alert alert-warning">${result.message}</div>`;
                    }
                })
                .catch(error => {
                    console.error('Error en la consulta:', error);
                    mensajeDiv.innerHTML = `<div class="alert alert-danger">Error en la consulta. Intente nuevamente.</div>`;
                    limpiarCampos();
                    setCamposHabilitados(true);
                });
        } else {
            ultimaCedulaConsultada = '';
            mensajeDiv.innerHTML = '';
            limpiarCampos();
            setCamposHabilitados(true);
        }
    });

    function setCampos(data) {
        document.getElementById('nombre').value = data.nombre;
        document.getElementById('apellido').value = data.apellido;
        document.getElementById('correo').value = data.correo;
        document.getElementById('telefono').value = data.telefono;
        document.getElementById('direccion').value = data.direccion;
        document.getElementById('fecha_nacimiento').value = data.fecha_nacimiento;
    }

    function limpiarCampos() {
        campos.forEach(id => {
            document.getElementById(id).value = '';
        });
    }

    function setCamposHabilitados(habilitado) {
        campos.forEach(id => {
            document.getElementById(id).disabled = !habilitado;
        });
    }
});


document.addEventListener('DOMContentLoaded', function () {
        let ventas = [];
    let ventasFiltradas = [];
    let paginaActual = 1;
    const ventasPorPagina = 10;

    cargarVentasPorEvento();

    // Filtro al escribir
    document.getElementById('busquedaTexto').addEventListener('input', aplicarFiltros);

    function cargarVentasPorEvento() {
        fetch( API_CONFIG + '/ventaspresencial/mostrarVentas', {
            credentials: 'include'
        })
        .then(res => res.json())
        .then(data => {
            ventas = data;
            aplicarFiltros();
        });
    }

    function aplicarFiltros() {
        const texto = document.getElementById('busquedaTexto').value.trim().toLowerCase();
        if (texto === "") {
            ventasFiltradas = ventas;
        } else {
            ventasFiltradas = ventas.filter(ev =>
                (ev.cliente && ev.cliente.toLowerCase().includes(texto)) ||
                (ev.fecha_pedido && ev.fecha_pedido.includes(texto)) ||
                (ev.total_comprado && ev.total_comprado.toString().includes(texto))
            );
        }
        paginaActual = 1;
        renderTablaVentas();
        renderPaginacion();
    }

    function renderTablaVentas() {
        let html = `
        <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Pedido</th>
                    <th>Fecha</th>
                    <th>Nombre del cliente</th>
                    <th>Total</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
        `;
        if (!ventasFiltradas || ventasFiltradas.length === 0) {
            html += `<tr><td colspan="6" class="text-center">No hay ventas registradas.</td></tr>`;
        } else {
            const inicio = (paginaActual - 1) * ventasPorPagina;
            const fin = inicio + ventasPorPagina;
            ventasFiltradas.slice(inicio, fin).forEach(ev => {
                html += `
                <tr>
                    <td>${'#PED-' + ev.id_pedidos}</td>
                    <td>${ev.fecha_pedido}</td>
                    <td>${ev.cliente}</td>
                    <td>$${parseFloat(ev.total_comprado).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm btn-ver-detalles" data-id="${ev.id_pedidos}">
                            <i class="fas fa-eye"></i> Ver detalles
                        </button>
                    </td>
                </tr>
                `;
            });
        }
        html += `</tbody></table></div>`;
        document.getElementById('tablaVentasContainer').innerHTML = html;

        // Asignar eventos a los botones de ver detalles
        document.querySelectorAll('.btn-ver-detalles').forEach(btn => {
            btn.addEventListener('click', function () {
                const idEvento = this.getAttribute('data-id');
                cargarDetalleEvento(idEvento);
            });
        });
    }

    function renderPaginacion() {
        const totalPaginas = Math.ceil((ventasFiltradas.length || 1) / ventasPorPagina);
        const paginacion = document.getElementById("ventasPagination");
        paginacion.innerHTML = "";

        // Botón anterior
        const prevLi = document.createElement("li");
        prevLi.className = `page-item${paginaActual === 1 ? " disabled" : ""}`;
        prevLi.innerHTML = `<a class="page-link" href="#">Anterior</a>`;
        prevLi.addEventListener("click", function (e) {
            e.preventDefault();
            if (paginaActual > 1) {
                paginaActual--;
                renderTablaVentas();
                renderPaginacion();
            }
        });
        paginacion.appendChild(prevLi);

        // Números de página
        for (let i = 1; i <= totalPaginas; i++) {
            const pageLi = document.createElement("li");
            pageLi.className = `page-item${paginaActual === i ? " active" : ""}`;
            pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageLi.addEventListener("click", function (e) {
                e.preventDefault();
                paginaActual = i;
                renderTablaVentas();
                renderPaginacion();
            });
            paginacion.appendChild(pageLi);
        }

        // Botón siguiente
        const nextLi = document.createElement("li");
        nextLi.className = `page-item${paginaActual === totalPaginas ? " disabled" : ""}`;
        nextLi.innerHTML = `<a class="page-link" href="#">Siguiente</a>`;
        nextLi.addEventListener("click", function (e) {
            e.preventDefault();
            if (paginaActual < totalPaginas) {
                paginaActual++;
                renderTablaVentas();
                renderPaginacion();
            }
        });
        paginacion.appendChild(nextLi);
    }

    function formatearFecha(fecha) {
        if (!fecha) return '';
        const [y, m, d] = fecha.split('-');
        return `${d}/${m}/${y}`;
    }

    function cargarDetalleEvento(idPedido) {
        fetch( API_CONFIG + '/ventaspresencial/detalleVenta', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_pedido: idPedido })
        })
        .then(res => res.json())
        .then(data => {
            mostrarModalDetalleEvento(data);
        });
    }

    function mostrarModalDetalleEvento(data) {
        let cliente = data.pedido.cliente;
        let html = `
            <h5>Cliente: ${cliente.nombre} ${cliente.apellido}</h5>
            <p><strong>Correo:</strong> ${cliente.correo}</p>
            <p><strong>Fecha del pedido:</strong> ${data.pedido.fecha}</p>
            <p><strong>Estatus:</strong> ${data.pedido.estatus}</p>
            <p><strong>Monto total:</strong> $${parseFloat(data.pedido.total).toFixed(2)}</p>
            <hr>
            <h6>Productos del pedido</h6>
            <table class="table table-sm table-bordered">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio unitario</th>
                        <th>Cantidad</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
        `;
    
        if (!data.detalle_productos || data.detalle_productos.length === 0) {
            html += `<tr><td colspan="4" class="text-center">Sin productos</td></tr>`;
        } else {
            data.detalle_productos.forEach(p => {
                html += `
                    <tr>
                        <td>${p.nombre_producto}</td>
                        <td>$${parseFloat(p.precio_unitario).toFixed(2)}</td>
                        <td>${p.cantidad}</td>
                        <td>$${parseFloat(p.total_producto).toFixed(2)}</td>
                    </tr>
                `;
            });
        }
    
        html += `</tbody></table>`;
    
        document.getElementById('detalleVentaBody').innerHTML = html;
        const modal = new bootstrap.Modal(document.getElementById('orderModal'));
        modal.show();
    }
    
  
    let productos = [];
    // Botón para agregar fila (fuera de la tabla)
    const btnAgregarGlobal = document.createElement('button');
    btnAgregarGlobal.type = 'button';
    btnAgregarGlobal.className = 'btn btn-success btn-sm my-2';
    btnAgregarGlobal.innerHTML = '<i class="fas fa-plus"></i> Agregar producto';
    btnAgregarGlobal.disabled = true; // Deshabilitado por defecto


    function agregarFilaProducto() {
        const tbody = document.getElementById('tablaProductosVendidos');
        const tr = document.createElement('tr');

        // 1. Select de productos con validación de Stock
        const tdProducto = document.createElement('td');
        const select = document.createElement('select');
        select.className = 'form-select producto-select';

        select.innerHTML = `<option value="">Seleccione...</option>` +
            productos.map(p => {
                const sinStock = p.stock <= 0;
                return `<option value="${p.id_producto}" 
                                data-precio="${p.precio}" 
                                data-stock="${p.stock}"
                                ${sinStock ? 'disabled style="color: #999;"' : ''}>
                            ${p.nombre} ${sinStock ? '(Agotado)' : ''}
                        </option>`;
            }).join('');

        tdProducto.appendChild(select);

        // 2. Cantidad con límites dinámicos
        const tdCantidad = document.createElement('td');
        const inputCantidad = document.createElement('input');
        inputCantidad.type = 'number';
        inputCantidad.min = 1;
        inputCantidad.value = 1;
        inputCantidad.className = 'form-control cantidad-input';
        inputCantidad.disabled = true; // Bloqueado hasta elegir producto
        tdCantidad.appendChild(inputCantidad);

        // 3. Precio unitario
        const tdPrecio = document.createElement('td');
        const inputPrecio = document.createElement('input');
        inputPrecio.type = 'text';
        inputPrecio.className = 'form-control precio-unitario-input';
        inputPrecio.readOnly = true;
        tdPrecio.appendChild(inputPrecio);

        // 4. Subtotal
        const tdSubtotal = document.createElement('td');
        const inputSubtotal = document.createElement('input');
        inputSubtotal.type = 'text';
        inputSubtotal.className = 'form-control subtotal-input';
        inputSubtotal.readOnly = true;
        tdSubtotal.appendChild(inputSubtotal);

        // 5. Botón eliminar
        const tdAccion = document.createElement('td');
        const btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.className = 'btn btn-danger btn-sm btn-remove-fila';
        btnEliminar.innerHTML = '<i class="fas fa-trash"></i>';
        tdAccion.appendChild(btnEliminar);

        // Ensamblaje de la fila
        tr.appendChild(tdProducto);
        tr.appendChild(tdCantidad);
        tr.appendChild(tdPrecio);
        tr.appendChild(tdSubtotal);
        tr.appendChild(tdAccion);
        tbody.appendChild(tr);

        // --- LÓGICA DE EVENTOS ---

        select.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            
            // Seguridad: evitar seleccionar deshabilitados
            if (selectedOption.disabled) {
                this.value = "";
                return;
            }

            const precio = parseFloat(selectedOption.getAttribute('data-precio')) || 0;
            const stock = parseInt(selectedOption.getAttribute('data-stock')) || 0;

            if (this.value !== "") {
                inputPrecio.value = precio;
                inputCantidad.disabled = false;
                inputCantidad.max = stock; // Definimos el límite máximo real
                
                // Si la cantidad previa era mayor al nuevo stock, ajustamos
                if (parseInt(inputCantidad.value) > stock) {
                    inputCantidad.value = stock;
                }
            } else {
                inputPrecio.value = '';
                inputCantidad.disabled = true;
                inputCantidad.value = 1;
            }

            calcularSubtotal();
            validarUltimaFilaProducto();
        });

        inputCantidad.addEventListener('input', function () {
            const maxStock = parseInt(this.max) || 0;
            const valorActual = parseInt(this.value) || 0;

            // Validamos que no escriban manualmente más del stock
            if (valorActual > maxStock) {
                this.value = maxStock;
                if (window.Alertas) {
                    Alertas.warning(`Solo hay ${maxStock} unidades en stock.`);
                }
            }
            
            // Validamos que no sea menor a 1
            if (valorActual < 1 && this.value !== "") {
                this.value = 1;
            }

            calcularSubtotal();
            validarUltimaFilaProducto();
        });

        function calcularSubtotal() {
            const cantidad = parseInt(inputCantidad.value) || 0;
            const precio = parseFloat(inputPrecio.value) || 0;
            const subtotal = cantidad * precio;
            inputSubtotal.value = subtotal ? subtotal.toFixed(2) : '';
            
            if (window.actualizarTotal) actualizarTotal();
        }

        btnEliminar.addEventListener('click', function () {
            tr.remove();
            if (window.actualizarTotal) actualizarTotal();
            validarUltimaFilaProducto();
        });

        validarUltimaFilaProducto();
    }

    // Valida si la última fila está completa para habilitar el botón global
    function validarUltimaFila() {
        const filas = document.querySelectorAll('#tablaProductosVendidos tr');
        if (filas.length === 0) {
            btnAgregarGlobal.disabled = false;
            return;
        }
        const ultima = filas[filas.length - 1];
        const select = ultima.querySelector('.producto-select');
        const cantidad = ultima.querySelector('.cantidad-input');
        const precio = ultima.querySelector('.precio-unitario-input');
        // Habilita solo si hay producto seleccionado y cantidad válida
        btnAgregarGlobal.disabled = !(select.value && cantidad.value && parseInt(cantidad.value) > 0 && precio.value);
    }

    btnAgregarGlobal.addEventListener('click', function () {
        agregarFilaProducto();
        validarUltimaFila();
    });

    function actualizarTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('totalPagarPedido').textContent = '$' + total.toFixed(2);
    }

    // --- PRODUCTOS VENDIDOS ---
    const btnAgregarProducto = document.createElement('button');
    btnAgregarProducto.type = 'button';
    btnAgregarProducto.className = 'btn btn-success btn-sm my-2';
    btnAgregarProducto.innerHTML = '<i class="fas fa-plus"></i> Agregar producto';
    btnAgregarProducto.disabled = true;

    document.getElementById('registrarVentaModal').addEventListener('show.bs.modal', function () {
        fetch( API_CONFIG + '/productos/mostrarProductosPorEmprendedor')
            .then(res => res.json())
            .then(data => {
                productos = data;
                limpiarTablaProductosVendidos();
                agregarFilaProducto();
                // Botón debajo de la tabla
                const tabla = document.querySelector('#tablaProductosVendidos').closest('table');
                if (tabla && !tabla.nextElementSibling?.isEqualNode(btnAgregarProducto)) {
                    tabla.parentNode.insertBefore(btnAgregarProducto, tabla.nextSibling);
                }
            });
        limpiarTablaDesglose();
        agregarFilaDesglose();
    });

    function limpiarTablaProductosVendidos() {
        document.getElementById('tablaProductosVendidos').innerHTML = '';
        actualizarTotal();
    }

    function validarUltimaFilaProducto() {
        const filas = document.querySelectorAll('#tablaProductosVendidos tr');
        if (filas.length === 0) {
            btnAgregarProducto.disabled = false;
            return;
        }
        const ultima = filas[filas.length - 1];
        const select = ultima.querySelector('.producto-select');
        const cantidad = ultima.querySelector('.cantidad-input');
        const precio = ultima.querySelector('.precio-unitario-input');
        btnAgregarProducto.disabled = !(select.value && cantidad.value && parseInt(cantidad.value) > 0 && precio.value);
    }

    btnAgregarProducto.addEventListener('click', function () {
        agregarFilaProducto();
        validarUltimaFilaProducto();
    });

    function actualizarTotal() {
        let total = 0;
        document.querySelectorAll('.subtotal-input').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('totalPagarPedido').textContent = '$' + total.toFixed(2);
        actualizarDesglose();
    }

    // --- DESGLOSE DE PAGO ---
    const btnAgregarDesglose = document.getElementById('btnAgregarDesglose');
    function limpiarTablaDesglose() {
        document.getElementById('desglosePagoBody').innerHTML = '';
        actualizarDesglose();
    }

function agregarFilaDesglose() {
    const tbody = document.getElementById('desglosePagoBody');
    const tr = document.createElement('tr');

    // Método de pago
    const tdMetodo = document.createElement('td');
    const selectMetodo = document.createElement('select');
    selectMetodo.className = 'form-select metodo-desglose';
    selectMetodo.name = 'metodo_pago[]';
    selectMetodo.required = true;
    selectMetodo.innerHTML = `<option value="">-- Selecciona método de pago --</option>` +
        (metodosPago && metodosPago.length ? metodosPago.map(m => `<option value="${m.id_metodo_pago}">${m.nombre}</option>`).join('') : '');
    tdMetodo.appendChild(selectMetodo);

    // Moneda
    const tdMoneda = document.createElement('td');
    const selectMoneda = document.createElement('select');
    selectMoneda.className = 'form-select moneda-desglose';
    selectMoneda.name = 'moneda[]';
    selectMoneda.required = true;
    selectMoneda.innerHTML = '<option value="">-- Selecciona moneda --</option>';
    tdMoneda.appendChild(selectMoneda);

    // Monto
    const tdMonto = document.createElement('td');
    const inputMonto = document.createElement('input');
    inputMonto.type = 'number';
    inputMonto.className = 'form-control monto-desglose';
    inputMonto.min = 0;
    inputMonto.step = '0.01';
    inputMonto.required = true;
    tdMonto.appendChild(inputMonto);

    // Referencia
    const tdReferencia = document.createElement('td');
    const inputReferencia = document.createElement('input');
    inputReferencia.type = 'text';
    inputReferencia.className = 'form-control referencia-desglose';
    inputReferencia.required = false;
    tdReferencia.appendChild(inputReferencia);

    // Botón eliminar
    const tdAccion = document.createElement('td');
    const btnEliminar = document.createElement('button');
    btnEliminar.type = 'button';
    btnEliminar.className = 'btn btn-danger btn-sm btn-remove-desglose';
    btnEliminar.innerHTML = '<i class="fas fa-trash"></i>';
    tdAccion.appendChild(btnEliminar);

    tr.appendChild(tdMetodo);
    tr.appendChild(tdMoneda);
    tr.appendChild(tdMonto);
    tr.appendChild(tdReferencia);
    tr.appendChild(tdAccion);

    tbody.appendChild(tr);

    // Evento para cargar monedas según método de pago
    selectMetodo.addEventListener('change', function () {
        const idMetodo = this.value;
        selectMoneda.innerHTML = '<option value="">Cargando...</option>';
        if (idMetodo) {
            fetch( API_CONFIG + `/pagos/monedasPorMetodo?idMetodo=${idMetodo}`, {
                credentials: 'include'
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === "success" && Array.isArray(data.data)) {
                    let options = '<option value="">-- Selecciona moneda --</option>';
                    data.data.forEach(moneda => {
                        options += `<option value="${moneda.id_detalle_pago}">${moneda.simbolo}</option>`;
                    });
                    selectMoneda.innerHTML = options;
                } else {
                    selectMoneda.innerHTML = '<option value="">Sin monedas</option>';
                }
            })
            .catch(() => {
                selectMoneda.innerHTML = '<option value="">Error al cargar</option>';
            });
        } else {
            selectMoneda.innerHTML = '<option value="">-- Selecciona moneda --</option>';
        }
        validarUltimaFilaDesglose();
    });

    selectMoneda.addEventListener('change', validarUltimaFilaDesglose);
    inputMonto.addEventListener('input', function () {
        actualizarDesglose();
        validarUltimaFilaDesglose();
    });

    btnEliminar.addEventListener('click', function () {
        tr.remove();
        actualizarDesglose();
        validarUltimaFilaDesglose();
    });

    validarUltimaFilaDesglose();
}

    function validarUltimaFilaDesglose() {
        const filas = document.querySelectorAll('#desglosePagoBody tr');
        if (filas.length === 0) {
            btnAgregarDesglose.disabled = false;
            return;
        }
        const ultima = filas[filas.length - 1];
        const metodo = ultima.querySelector('.metodo-desglose');
        const moneda = ultima.querySelector('.moneda-desglose');
        const monto = ultima.querySelector('.monto-desglose');
        btnAgregarDesglose.disabled = !(metodo.value && moneda.value && monto.value && parseFloat(monto.value) > 0);
    }

    btnAgregarDesglose.addEventListener('click', function () {
        agregarFilaDesglose();
        validarUltimaFilaDesglose();
    });

let valorDolar = 0;

function actualizarValorDolar() {
  fetch( API_CONFIG + '/tasa/consultarTasaBcv', {
    method: 'GET',
    headers: {
      'Accept': 'application/json'
    },
    credentials: 'include'
  })
  .then(response => response.json())
  .then(data => {
    if (data.success && data.data?.tasa_cambio) {
      valorDolar = parseFloat(data.data.tasa_cambio);
    } else {
      console.warn('No se pudo obtener la tasa de cambio');
    }
  })
  .catch(error => {
    console.error('Error al consultar la tasa de cambio:', error);
  });
}

actualizarValorDolar();   

function actualizarDesglose() {
    let totalDesglose = 0;
    document.querySelectorAll('#desglosePagoBody tr').forEach(tr => {
        const montoInput = tr.querySelector('.monto-desglose');
        const monedaSelect = tr.querySelector('.moneda-desglose');
        let monto = parseFloat(montoInput?.value) || 0;
        let simbolo = monedaSelect?.options[monedaSelect.selectedIndex]?.text?.trim() || '';

        // Si es Bs, convertir a dólares usando valorDolar
        if (simbolo === 'Bs' && valorDolar > 0) {
            monto = monto / valorDolar;
        }
        // Si es $, sumar directo
        // Si no hay moneda seleccionada, no sumar nada
        totalDesglose += monto;
    });

    document.getElementById('totalDesglosePago').textContent = '$' + totalDesglose.toFixed(2);

    // Comparar con el total de productos vendidos (en dólares)
    const totalProductos = parseFloat(document.getElementById('totalPagarPedido').textContent.replace('$', '')) || 0;
    const fondoFaltante = totalProductos - totalDesglose;
    const fondoFaltanteInput = document.getElementById('fondoFaltanteDolar');
    const fondoFaltanteLabel = document.querySelector('label[for="fondoFaltanteDolar"]');
    const fondofaltantebsInput = document.getElementById('fondoFaltantebs');
    const fondoFaltanteLabelbs = document.querySelector('label[for="fondoFaltantebs"]');
    fondofaltantebsInput.value = 'Bs ' + (Math.abs(fondoFaltante) * valorDolar).toFixed(2);
    fondoFaltanteInput.value = '$' + Math.abs(fondoFaltante).toFixed(2);

    // Mostrar advertencia solo si falta dinero
    const advertencia = document.getElementById('desgloseAdvertencia');
    const btnRegistrar = document.querySelector('#formRegistrarVenta button[type="submit"]');

    if (Math.abs(fondoFaltante) > 0.009) {
        btnRegistrar.disabled = true;
        
        if (fondoFaltante > 0) {
            // Falta dinero
            advertencia.style.display = '';
            fondoFaltanteInput.classList.add('is-invalid');
            fondoFaltanteInput.classList.remove('is-valid');
            fondoFaltanteLabel.textContent = 'Monto faltante';
            fondoFaltanteLabel.classList.add('text-danger');
            fondoFaltanteLabel.classList.remove('text-success');
            fondofaltantebsInput.classList.add('is-invalid');
            fondofaltantebsInput.classList.remove('is-valid');
            fondoFaltanteLabelbs.textContent = 'Monto faltante';
            fondoFaltanteLabelbs.classList.add('text-danger');
            fondoFaltanteLabelbs.classList.remove('text-success');
        } else {
            // Sobra dinero
            btnRegistrar.disabled = false;
            advertencia.style.display = 'none';
            fondoFaltanteInput.classList.add('is-valid');
            fondoFaltanteInput.classList.remove('is-invalid');
            fondoFaltanteLabel.textContent = 'Monto';
            fondoFaltanteLabel.classList.add('text-success');
            fondoFaltanteLabel.classList.remove('text-danger');
            fondofaltantebsInput.classList.add('is-valid');
            fondofaltantebsInput.classList.remove('is-invalid');
            fondoFaltanteLabelbs.textContent = 'Monto';
            fondoFaltanteLabelbs.classList.add('text-success');
            fondoFaltanteLabelbs.classList.remove('text-danger');
        }
    } else {
        advertencia.style.display = 'none';
        btnRegistrar.disabled = false;
        fondoFaltanteInput.classList.remove('is-invalid', 'is-valid');
        fondoFaltanteLabel.textContent = 'Monto cuadrado';
        fondoFaltanteLabel.classList.remove('text-danger', 'text-success');
        fondofaltantebsInput.classList.remove('is-invalid', 'is-valid');
        fondoFaltanteLabelbs.textContent = 'Monto cuadrado';
        fondoFaltanteLabelbs.classList.remove('text-danger', 'text-success');
    }
}
function obtenerProductosVendidos() {
    const productos = [];
    document.querySelectorAll('#tablaProductosVendidos tr').forEach(tr => {
        const select = tr.querySelector('.producto-select');
        const cantidad = tr.querySelector('.cantidad-input');
        const precio = tr.querySelector('.precio-unitario-input');
        const subtotal = tr.querySelector('.subtotal-input');
        if (select && cantidad && precio && subtotal && select.value && cantidad.value) {
            productos.push({
                id_producto: select.value,
                cantidad: cantidad.value,
                precio_unitario: precio.value,
                subtotal: subtotal.value
            });
        }
    });
    return productos;
}

function obtenerDesglosePago() {
    const desglose = [];
    document.querySelectorAll('#desglosePagoBody tr').forEach(tr => {
        const metodo = tr.querySelector('.metodo-desglose');
        const moneda = tr.querySelector('.moneda-desglose');
        const monto = tr.querySelector('.monto-desglose');
        const referencia = tr.querySelector('.referencia-desglose');
        if (metodo && moneda && monto && referencia && metodo.value && moneda.value && monto.value && referencia.value) {
            desglose.push({
                id_metodo_pago: metodo.value,
                id_moneda: moneda.value,
                monto: monto.value,
                referencia: referencia.value
            });
        }
    });
    return desglose;
}

document.getElementById('formRegistrarVenta').addEventListener('submit', function(e) {
    e.preventDefault();

    // Datos del cliente desde el formulario
    const cedula = document.getElementById('cedula').value.trim();
    const nombre = document.getElementById('nombre').value.trim();
    const apellido = document.getElementById('apellido').value.trim();
    const correo = document.getElementById('correo').value.trim();
    const telefono = document.getElementById('telefono').value.trim();
    const direccion = document.getElementById('direccion').value.trim();
    const fecha_nacimiento = document.getElementById('fecha_nacimiento').value.trim();

    // Productos vendidos y métodos de pago
    const productos = obtenerProductosVendidos();  // debe devolver un array válido
    const metodos_pago = obtenerDesglosePago();    // debe devolver al menos un método

    // Construimos el objeto del cliente
    const datos_cliente = {
        cedula,
        nombre,
        apellido,
        correo,
        telefono,
        direccion,
        fecha_nacimiento
    };

    // Payload completo
    const payload = {
        datos_cliente,
        productos,
        metodos_pago
    };


    fetch( API_CONFIG + '/ventaspresencial/registrarVenta', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Venta','Registrado correctamente','success');
            cargarVentasPorEvento();
            document.getElementById('cedula').value = '';
            const modal = bootstrap.Modal.getInstance(document.getElementById('registrarVentaModal'));
            modal.hide();
        } else {
            mostrarAlerta('Venta', data.message, 'warning');
        }
    })
    .catch(error => {
        console.error('Error en la solicitud:', error.message);
        alert('Ocurrió un error al conectar con el servidor.');
    });
});


});

document.getElementById('formRegistrarCliente').addEventListener('submit', function (e) {
    e.preventDefault();
  
    const form = e.target;
    const formData = new FormData(form);
  
    fetch(API_CONFIG + '/ventaspresencial/registrarUsuario', {
      method: 'POST',
      body: formData,
      credentials: 'include'
    })
    .then(async response => {
      const data = await response.json(); // Intentar parsear siempre
  
      if (response.ok) {
        // Registro exitoso
        mostrarAlerta('Cliente', 'Registrado correctamente', 'success');
        $('#registrarClienteModal').modal('hide');
        form.reset();
        setTimeout(() => location.reload(), 1000);
      } else {
        // Error desde el backend, pero con JSON válido
        mostrarAlerta('Error', data.message || 'Error en la solicitud', 'error');
      }
    })
    .catch(error => {
      // Error de red, servidor no responde o error inesperado
      console.error('Fetch error:', error);
      mostrarAlerta('Error', 'Ocurrió un error en la petición', 'error');
    });
  });

  //VALIDACION DE FORMULARIO
  //Para mostrar el error en el span
function showError(field, message) {
    //El contenido del mensaje se mostrará en el span que tenga el id field+Error (ejemplo nombreError)
    document.getElementById(field + "Error").textContent = message;
}

//Para limpiar el error en el span 
function clearError(field) {
    document.getElementById(field + "Error").textContent = "";
}

//Se evalua el campo y depende de eso se muestra o se limpia el error en el span correspondiente
//Se envia como parametros: el event, la expresion regular, el campo, y el mensaje de error que se mostrará
function restrictInput(event, regex, field, errorMsg) {
    const key = event.key;
    //Si se está recibiendo por teclado una tecla que no este en la exp reg, que no sea tecla de borrar ni tab    
    if (!regex.test(key) && key !== "Backspace" && key !== "Tab") {
        event.preventDefault();
        showError(field, errorMsg); // Muestra mensaje solo si el caracter es incorrecto
    } 
    //En caso que todas las teclas que se esten ingresando sean correctar
    else {
        clearError(field); // Limpia el mensaje si el caracter es permitido
    }
}
document.getElementById("cedula").addEventListener("keypress", function(event) {
    restrictInput(event, /^[0-9]$/, "cedula", "Solo se permiten números.");
});
document.getElementById("nombre").addEventListener("keypress", function(event) {
    restrictInput(event, /^[A-Za-z\s]$/, "nombre", "Solo se permiten letras.");
});

document.getElementById("apellido").addEventListener("keypress", function(event) {
    restrictInput(event, /^[A-Za-z\s]$/, "apellido", "Solo se permiten letras.");
});

document.getElementById("correo").addEventListener("input", function() {
    const correo = this.value;
    const correoRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!correoRegex.test(correo)) {
        showError("correo", "El correo debe incluir una direccion como @gmail.com, @hotmail.com, @yahoo.com, @outlook.com.");
    } else {
        clearError("correo");
    }
});

document.getElementById("direccion").addEventListener("input", function() {
    const direccion = this.value;
    const direccionRegex = /^[A-Za-z0-9#\-\.\s]+$/;
    if (!direccionRegex.test(direccion)) {
        showError("direccion", "Solo se aceptan numeros, letras, espacios, # y -.");
    } else {
        clearError("direccion");
    }
});


document.getElementById("telefono").addEventListener("keypress", function(event) {
    restrictInput(event, /^[0-9]$/, "telefono", "Solo se permiten números.");
});

function validateNombre() {
    const nombre_cliente = document.getElementById("nombre").value;
    const nombreRegex = /^[A-Za-z\s]{3,50}$/;
    if (!nombreRegex.test(nombre_cliente)) {
        showError("nombre", "El nombre debe tener entre 3 y 50 caracteres y solo letras.");
        return false;
    } else {
        clearError("nombre");
        return true;
    }
} 

function validateApellido() {
    const apellido = document.getElementById("apellido").value;
    const nombreRegex = /^[A-Za-z\s]{5,50}$/;
    if (!nombreRegex.test(apellido)) {
        showError("apellido", "El apellido debe tener entre 5 y 50 caracteres y solo letras.");
        return false;
    } else {
        clearError("apellido");
        return true;
    }
}


function validateCorreo() {
    const correo = document.getElementById("correo").value;
    const nombreRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!nombreRegex.test(correo)) {
        showError("correo", "El correo debe incluir una direccion como @gmail.com, @hotmail.com, @yahoo.com, @outlook.com");
     
        return false;
    } else {
        clearError("correo");
        return true;
    }
} 

function validateDireccion() {
    const direccion = document.getElementById("direccion").value;
    const nombreRegex = /^[A-Za-z0-9#\-\.\,\s]+$/ ;
    if (!nombreRegex.test(direccion)) {
        showError("direccion", "Ingrese su direccion.");
     
        return false;
    } else {
        clearError("direccion");
        return true;
    }
}

function validateCedula() {
    const cedula_rif = document.getElementById("cedula").value;
    const valor = parseFloat(cedula_rif);
    return !isNaN(valor) && valor > 0;
}

function validateTelefono() {
    const telefono= document.getElementById("telefono").value;
    const valor = parseFloat(telefono);
    return !isNaN(valor) && valor > 0;
}


// Validaciones completas en `input`, sin mensajes de error
function validatePassword() {
    const password_user = document.getElementById("password").value;
    const nombreRegex = /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*.,])[A-Za-z\d!@#$%^&*.,]{8,}$/;
    if (!nombreRegex.test(password_user)) {
        showError("password", "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial");
     
        return false;
    } else {
        clearError("password");
        return true;
    }
}


function enableSubmit_crear() {
    //Se validan en funciones que cumplan todas con las exp reg
    const isFormValid =
       
        validateCedula() &&
        validateNombre()&&
        validateApellido() &&
        validateCorreo()  &&
        validateDireccion() &&
        validateTelefono() && 
        validatePassword()&&   

        document.getElementById("cedula").value.trim() !== "" &&//Y aqui se validan que realmente tengan un valor estos campos
        document.getElementById("nombre").value.trim() !== "" &&    
        document.getElementById("correo").value.trim() !== "" &&   
        document.getElementById("apellido").value.trim() !== "" &&      
        document.getElementById("direccion").value.trim() !== "" &&   
         document.getElementById("password").value.trim() !== "" &&  
        document.getElementById("telefono").value.trim() !== "";       
        // Habilita o deshabilita el botón de "registrar" según el resultado de `isFormValid`
        document.getElementById("btn btn-primary").disabled = !isFormValid;
}

        document.getElementById("apellido").addEventListener("input", enableSubmit_crear);
        document.getElementById("nombre").addEventListener("input", enableSubmit_crear);
        document.getElementById("correo").addEventListener("input", enableSubmit_crear);
        document.getElementById("direccion").addEventListener("input", enableSubmit_crear);
        document.getElementById("telefono").addEventListener("input", enableSubmit_crear);
        document.getElementById("password").addEventListener("input", enableSubmit_crear);


  
