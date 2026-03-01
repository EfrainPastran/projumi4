import { mostrarConfirmacion } from './alertas.js';
import { API_CONFIG } from './config.js';
// Función para calcular el tiempo transcurrido de forma simple
function calcularTiempo(fecha) {
    const fechaNoti = new Date(fecha);
    const ahora = new Date();
    const diffMs = ahora - fechaNoti; // diferencia en ms
    const minutos = Math.floor(diffMs / 60000);

    if (minutos < 1) return 'Justo ahora';
    if (minutos < 60) return `Hace ${minutos} minuto(s)`;
    const horas = Math.floor(minutos / 60);
    if (horas < 24) return `Hace ${horas} hora(s)`;
    const dias = Math.floor(horas / 24);
    return `Hace ${dias} día(s)`;
}

function formatearNotificaciones(notificaciones) {
    return notificaciones.map(notificacion => {

        // Notificación normal
        let icono = "fas fa-eye";
        let color = "text-muted";
        switch (notificacion.status) {
            case 0:
                color = "text-warning";
                break;
            case 1:
                color = "text-success";
                break;
            case 2:
                icono = "fas fa-exclamation-triangle";
                color = "text-danger";
                break;
        }

        return {
            id_notificacion: notificacion.id_notificacion,
            mensaje: notificacion.descripcion || notificacion.mensaje,
            icono: `${icono} ${color}`,
            tiempo: calcularTiempo(notificacion.fecha),
            url: API_CONFIG + (notificacion.ruta || notificacion.url || '/'),
            tipo: notificacion.tipo || 'normal', // nuevo campo
            status: notificacion.status
        };
        
    });
}
function mostrarNotificaciones(data) {
    const lista = document.getElementById('listaNotificaciones');
    const contador = document.getElementById('contadorNotificaciones');

    // Limpia todo antes de agregar
    lista.innerHTML = '';
    contador.style.display = 'none'; // oculta el contador por defecto

    // Si no hay datos, no muestra nada y sale
    if (!data || data.length === 0) {
        return;
    }

    // Encabezado
    const encabezado = document.createElement('li');
    encabezado.className = 'dropdown-header';
    encabezado.textContent = 'Notificaciones';
    lista.appendChild(encabezado);

    data.forEach(n => {
        const item = document.createElement('li');
        item.innerHTML = `
            <div class="dropdown-item d-flex align-items-start justify-content-between" style="white-space: normal; overflow-wrap: break-word;">
                <a href="${n.url}" class="d-flex" style="text-decoration: none; color: inherit; flex: 1;" data-id="${n.id_notificacion}">
                    <i class="${n.icono} me-2 mt-1" style="min-width: 24px; font-size: 18px; flex-shrink: 0; margin-top: 0.3rem;"></i>
                    <div style="flex: 1;">
                        <strong>${n.mensaje}</strong><br>
                        <small class="text-muted">${n.tiempo || n.fecha}</small>
                    </div>
                </a>
                ${n.tipo === 'normal' ? `
                    <button class="btn btn-sm btn-link text-danger eliminar-btn" title="Eliminar" data-id="${n.id_notificacion}">
                        <i class="fas fa-trash-alt"></i>
                    </button>` :
                    `<i class="fas fa-exclamation-circle text-warning ms-2" title="No puede eliminar esta notificación mientras que el stock sea menor a 5"></i>`
                }
            </div>
        `;

        lista.appendChild(item);
    });

    // Escuchar clics en las notificaciones para marcarlas como leídas
    lista.querySelectorAll('a[data-id]').forEach(a => {
        a.addEventListener('click', function (e) {
            const id = this.getAttribute('data-id');

            // Marcar como leída antes de redirigir
            fetch(API_CONFIG + '/notificacion/marcarLeida', {
                method: 'POST',
                credentials: 'include',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `id_notificacion=${id}`
            }).catch(err => console.error('Error al marcar notificación como leída:', err));
        });
    });

    // Escuchar clics en eliminar
    lista.querySelectorAll('.eliminar-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.stopPropagation(); // para evitar abrir enlace
            e.preventDefault();

            const id = this.getAttribute('data-id');

            mostrarConfirmacion(
                '¿Está seguro?',
                'Esta acción eliminará la notificación permanentemente.',
                function (confirmado) {
                    if (confirmado) {
                        fetch(API_CONFIG + '/notificacion/eliminar', {
                            method: 'POST',
                            credentials: 'include',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `id_notificacion=${id}`
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                cargarNotificaciones(); // refresca
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Eliminado',
                                    text: 'Notificación eliminada correctamente',
                                    timer: 1800,
                                    showConfirmButton: false
                                });
                            } else {
                                Swal.fire('Error', data.message || 'No se pudo eliminar', 'error');
                            }
                        })
                        .catch(err => {
                            console.error('Error al eliminar notificación:', err);
                            Swal.fire('Error', 'Ocurrió un error al eliminar la notificación', 'error');
                        });
                    }
                }
            );
        });
    });

    // Divider
    const hr = document.createElement('li');
    hr.innerHTML = `<hr class="dropdown-divider">`;
    lista.appendChild(hr);

    // Ver más
    const verMas = document.createElement('li');
    verMas.innerHTML = `
        <a class="dropdown-item text-center text-primary fw-semibold" href="#">
            Ver más <i class="fas fa-arrow-right ms-1"></i>
        </a>
    `;
    //lista.appendChild(verMas); PARA MOSTRAR TODAS LAS NOTIFICACIONES

    // 🔥 Actualizar contador (solo notificaciones con status != 1)
    const pendientes = data.filter(n => n.status !== 1).length;
    if (pendientes > 0) {
        contador.textContent = pendientes;
        contador.style.display = 'inline-block';
    } else {
        contador.style.display = 'none';
    }
}



function cargarNotificaciones() {
    fetch( API_CONFIG + '/notificacion/obtenerNotificaciones')
        .then(response => response.json())
        .then(data => {
            // Si data tiene la propiedad "error", no hacer nada
            if (data && data.error) {
                mostrarNotificaciones([]); // pasar array vacío para limpiar cualquier notificación previa
                return;
            }

            // Si data es un objeto solo, lo metemos en un array para poder usar map
            const notificaciones = Array.isArray(data) ? data : [data];
            const notificacionesFormateadas = formatearNotificaciones(notificaciones);
            mostrarNotificaciones(notificacionesFormateadas);
        })
        .catch(error => {
            console.error('Error al cargar notificaciones:', error);
        });
}

document.addEventListener('DOMContentLoaded', () => {
    cargarNotificaciones();
    setInterval(cargarNotificaciones, 5000); // refrescar cada 5 segundos (prueba)
});
