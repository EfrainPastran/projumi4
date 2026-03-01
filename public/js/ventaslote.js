import { mostrarAlerta } from './alertas.js';
import { API_CONFIG } from './config.js';
document.addEventListener('DOMContentLoaded', function () {
        let ventas = [];
    let ventasFiltradas = [];
    let paginaActual = 1;
    const ventasPorPagina = 10;

    cargarVentasPorEvento();

    // Filtro al escribir
    document.getElementById('busquedaTexto').addEventListener('input', aplicarFiltros);

    function cargarVentasPorEvento() {
        fetch( API_CONFIG + '/ventaslote/mostrarVentas', {
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
                (ev.nombre_evento && ev.nombre_evento.toLowerCase().includes(texto)) ||
                (ev.direccion && ev.direccion.toLowerCase().includes(texto)) ||
                (ev.fecha_inicio && ev.fecha_inicio.includes(texto)) ||
                (ev.fecha_fin && ev.fecha_fin.includes(texto)) ||
                (ev.monto_total && ev.monto_total.toString().includes(texto))
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
                    <th>Evento</th>
                    <th>Dirección</th>
                    <th>Fecha inicio</th>
                    <th>Fecha fin</th>
                    <th>Monto total</th>
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
                    <td>${ev.nombre_evento}</td>
                    <td>${ev.direccion}</td>
                    <td>${formatearFecha(ev.fecha_inicio)}</td>
                    <td>${formatearFecha(ev.fecha_fin)}</td>
                    <td>$${parseFloat(ev.monto_total).toFixed(2)}</td>
                    <td>
                        <button class="btn btn-outline-primary btn-sm btn-ver-detalles" data-id="${ev.id_evento}">
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

    function cargarDetalleEvento(idEvento) {
        fetch( API_CONFIG + '/ventaslote/detalleVentaEvento', {
            method: 'POST',
            credentials: 'include',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id_evento: idEvento })
        })
        .then(res => res.json())
        .then(data => {
            mostrarModalDetalleEvento(data);
        });
    }

    function mostrarModalDetalleEvento(data) {
        let html = `
            <h5>Evento: ${data.evento.nombre_evento}</h5>
            <p><strong>Dirección:</strong> ${data.evento.direccion}</p>
            <p><strong>Fecha:</strong> ${formatearFecha(data.evento.fecha_inicio)} al ${formatearFecha(data.evento.fecha_fin)}</p>
            <p><strong>Monto total:</strong> $${parseFloat(data.evento.monto_total).toFixed(2)}</p>
            <hr>
            <h6>Productos vendidos</h6>
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
                        <td>$${parseFloat(p.precio).toFixed(2)}</td>
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

        // Select de productos
        const tdProducto = document.createElement('td');
        const select = document.createElement('select');
        select.className = 'form-select producto-select';
        select.innerHTML = `<option value="">Seleccione...</option>` +
            productos.map(p => `<option value="${p.id_producto}" data-precio="${p.precio}">${p.nombre}</option>`).join('');
        tdProducto.appendChild(select);

        // Cantidad
        const tdCantidad = document.createElement('td');
        const inputCantidad = document.createElement('input');
        inputCantidad.type = 'number';
        inputCantidad.min = 1;
        inputCantidad.className = 'form-control cantidad-input';
        tdCantidad.appendChild(inputCantidad);

        // Precio unitario
        const tdPrecio = document.createElement('td');
        const inputPrecio = document.createElement('input');
        inputPrecio.type = 'text';
        inputPrecio.className = 'form-control precio-unitario-input';
        inputPrecio.readOnly = true;
        tdPrecio.appendChild(inputPrecio);

        // Subtotal
        const tdSubtotal = document.createElement('td');
        const inputSubtotal = document.createElement('input');
        inputSubtotal.type = 'text';
        inputSubtotal.className = 'form-control subtotal-input';
        inputSubtotal.readOnly = true;
        tdSubtotal.appendChild(inputSubtotal);

        // Botón eliminar
        const tdAccion = document.createElement('td');
        const btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.className = 'btn btn-danger btn-sm btn-remove-fila';
        btnEliminar.innerHTML = '<i class="fas fa-trash"></i>';
        tdAccion.appendChild(btnEliminar);

        tr.appendChild(tdProducto);
        tr.appendChild(tdCantidad);
        tr.appendChild(tdPrecio);
        tr.appendChild(tdSubtotal);
        tr.appendChild(tdAccion);

        tbody.appendChild(tr);

        // Eventos
        select.addEventListener('change', function () {
            const precio = this.selectedOptions[0].getAttribute('data-precio') || 0;
            inputPrecio.value = precio;
            calcularSubtotal();
            validarUltimaFila();
        });

        inputCantidad.addEventListener('input', function () {
            calcularSubtotal();
            validarUltimaFila();
        });

        function calcularSubtotal() {
            const cantidad = parseInt(inputCantidad.value) || 0;
            const precio = parseFloat(inputPrecio.value) || 0;
            const subtotal = cantidad * precio;
            inputSubtotal.value = subtotal ? subtotal.toFixed(2) : '';
            actualizarTotal();
        }

        btnEliminar.addEventListener('click', function () {
            tr.remove();
            actualizarTotal();
            validarUltimaFila();
        });

        validarUltimaFila();
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

    function agregarFilaProducto() {
        const tbody = document.getElementById('tablaProductosVendidos');
        const tr = document.createElement('tr');

        // Select de productos
        const tdProducto = document.createElement('td');
        const select = document.createElement('select');
        select.className = 'form-select producto-select';
        select.innerHTML = `<option value="">Seleccione...</option>` +
            productos.map(p => `<option value="${p.id_producto}" data-precio="${p.precio}">${p.nombre}</option>`).join('');
        tdProducto.appendChild(select);

        // Cantidad
        const tdCantidad = document.createElement('td');
        const inputCantidad = document.createElement('input');
        inputCantidad.type = 'number';
        inputCantidad.min = 1;
        inputCantidad.className = 'form-control cantidad-input';
        tdCantidad.appendChild(inputCantidad);

        // Precio unitario
        const tdPrecio = document.createElement('td');
        const inputPrecio = document.createElement('input');
        inputPrecio.type = 'text';
        inputPrecio.className = 'form-control precio-unitario-input';
        inputPrecio.readOnly = true;
        tdPrecio.appendChild(inputPrecio);

        // Subtotal
        const tdSubtotal = document.createElement('td');
        const inputSubtotal = document.createElement('input');
        inputSubtotal.type = 'text';
        inputSubtotal.className = 'form-control subtotal-input';
        inputSubtotal.readOnly = true;
        tdSubtotal.appendChild(inputSubtotal);

        // Botón eliminar
        const tdAccion = document.createElement('td');
        const btnEliminar = document.createElement('button');
        btnEliminar.type = 'button';
        btnEliminar.className = 'btn btn-danger btn-sm btn-remove-fila';
        btnEliminar.innerHTML = '<i class="fas fa-trash"></i>';
        tdAccion.appendChild(btnEliminar);

        tr.appendChild(tdProducto);
        tr.appendChild(tdCantidad);
        tr.appendChild(tdPrecio);
        tr.appendChild(tdSubtotal);
        tr.appendChild(tdAccion);

        tbody.appendChild(tr);

        // Eventos
        select.addEventListener('change', function () {
            const precio = this.selectedOptions[0].getAttribute('data-precio') || 0;
            inputPrecio.value = precio;
            calcularSubtotal();
            validarUltimaFilaProducto();
        });

        inputCantidad.addEventListener('input', function () {
            calcularSubtotal();
            validarUltimaFilaProducto();
        });

        function calcularSubtotal() {
            const cantidad = parseInt(inputCantidad.value) || 0;
            const precio = parseFloat(inputPrecio.value) || 0;
            const subtotal = cantidad * precio;
            inputSubtotal.value = subtotal ? subtotal.toFixed(2) : '';
            actualizarTotal();
        }

        btnEliminar.addEventListener('click', function () {
            tr.remove();
            actualizarTotal();
            validarUltimaFilaProducto();
        });

        validarUltimaFilaProducto();
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
            fondoFaltanteLabel.textContent = 'Monto sobrante';
            fondoFaltanteLabel.classList.add('text-success');
            fondoFaltanteLabel.classList.remove('text-danger');
            fondofaltantebsInput.classList.add('is-valid');
            fondofaltantebsInput.classList.remove('is-invalid');
            fondoFaltanteLabelbs.textContent = 'Monto sobrante';
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
        if (metodo && moneda && monto && metodo.value && moneda.value && monto.value) {
            desglose.push({
                id_metodo_pago: metodo.value,
                id_moneda: moneda.value,
                monto: monto.value
            });
        }
    });
    return desglose;
}

document.getElementById('formRegistrarVenta').addEventListener('submit', function(e) {
    e.preventDefault();

    const selectEvento = document.getElementById('selectEvento').value;
    const productos = obtenerProductosVendidos();  // debe devolver un array válido
    const desglose = obtenerDesglosePago();        // debe devolver un array con al menos un método de pago

    const payload = { selectEvento, productos, desglose };

    console.log(payload); // opcional: para verificar lo que se enviará

    fetch( API_CONFIG + '/ventaslote/registrarVentaEvento', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            mostrarAlerta('Venta','Registrado correctamente','success');
            cargarVentasPorEvento();
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