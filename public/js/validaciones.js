/**
 * VALIDACIONES GENERALES PROJUMI - VERSIÓN MEJORADA CON FECHAS
 */
const Validaciones = {
    patterns: {
        soloLetras: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, //Expresiones regulares para solo letras
        soloNumeros: /^[0-9]+$/ //Expresiones regulares para solo números
    },

    init: function(formSelector) {
        const form = document.querySelector(formSelector);
        if (!form) return;

        const inputs = form.querySelectorAll('input, textarea, select');

        inputs.forEach(input => {
            // 1. Bloqueo de teclas inválidas + Mensaje instantáneo
            input.addEventListener('keypress', (e) => {
                const tipo = input.dataset.tipo;
                const char = String.fromCharCode(e.keyCode);
                
                if (tipo === 'letras' && !this.patterns.soloLetras.test(char) && e.keyCode !== 8) {
                    e.preventDefault();
                    this.mostrarMensaje(input, "Solo se permiten letras");
                    return;
                }
                
                if (tipo === 'numeros' && !this.patterns.soloNumeros.test(char) && e.keyCode !== 8) {
                    e.preventDefault();
                    this.mostrarMensaje(input, "Solo se permiten números");
                    return;
                }
            });

            // 2. Control de Rango Máximo e Input en tiempo real
            input.addEventListener('input', () => {
                const max = input.dataset.max;
                if (max && input.value.length > max) {
                    input.value = input.value.slice(0, max);
                    this.mostrarMensaje(input, `Límite máximo de ${max} caracteres alcanzado`);
                } else {
                    this.validarCampo(input);
                }
                
                // Si es una fecha, validar la pareja (inicio/fin) al cambiar
                if (input.type === 'date') {
                    const form = input.closest('form');
                    const inputInicio = form.querySelector('[name="fecha_inicio"]');
                    const inputFin = form.querySelector('[name="fecha_fin"]');
                    if (inputInicio && inputFin) {
                        this.validarFechas(inputInicio, inputFin);
                    }
                }
            });

            // 3. Validación al salir del campo
            input.addEventListener('blur', () => this.validarCampo(input));
        });

        // 4. Validación final al enviar
        form.addEventListener('submit', (e) => {
            let esValido = true;
            inputs.forEach(input => {
                if (!this.validarCampo(input)) esValido = false;
            });

            if (!esValido) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'Formulario incompleto',
                    text: 'Por favor revise los campos marcados en rojo.',
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    },

    validarFechas: function(inputInicio, inputFin) {
        if (inputInicio.value && inputFin.value) {
            const inicio = new Date(inputInicio.value);
            const fin = new Date(inputFin.value);

            if (fin < inicio) {
                this.mostrarMensaje(inputFin, "La fecha fin no puede ser menor a la de inicio");
                return false;
            } else {
                // Si las fechas son correctas, limpiamos el error del campo fin
                this.mostrarMensaje(inputFin, "");
                return true;
            }
        }
        return true;
    },

    validarCampo: function(input) {
        let mensaje = "";
        const valor = input.value.trim();
        const min = input.dataset.min;
        const tipo = input.dataset.tipo;
        const form = input.closest('form');

        // Validación de Requerido
        if (input.hasAttribute('required') && valor === "") {
            mensaje = "Este campo es obligatorio";
        } 
        // Validación de Minimo
        else if (min && valor.length > 0 && valor.length < min) {
            mensaje = `Debe tener al menos ${min} caracteres`;
        } 
        // Validación de Tipo Letras
        else if (tipo === 'letras' && valor !== "" && !this.patterns.soloLetras.test(valor)) {
            mensaje = "Formato inválido: Solo letras";
        } 
        // Validación de Tipo Números
        else if (tipo === 'numeros' && valor !== "" && !this.patterns.soloNumeros.test(valor)) {
            mensaje = "Formato inválido: Solo números";
        }
        // Validación Lógica de Fechas
        else if (input.name === 'fecha_fin') {
            const inputInicio = form.querySelector('[name="fecha_inicio"]');
            if (inputInicio && inputInicio.value && valor) {
                const inicio = new Date(inputInicio.value);
                const fin = new Date(valor);
                if (fin < inicio) {
                    mensaje = "La fecha fin no puede ser menor a la de inicio";
                }
            }
        }
        else if (input.type === 'date' && input.id === 'fecha_nacimiento') {
            const fechaSeleccionada = new Date(input.value);
            const hoy = new Date();
            const edad = hoy.getFullYear() - fechaSeleccionada.getFullYear();
            const mes = hoy.getMonth() - fechaSeleccionada.getMonth();

            // Ajuste por si aún no ha cumplido años este mes
            if (mes < 0 || (mes === 0 && hoy.getDate() < fechaSeleccionada.getDate())) {
                edad--;
            }

            if (edad < 18) {
                mensaje = "Debe ser mayor de 18 años";
            } else if (edad > 110) {
                mensaje = "Edad no válida";
            }
        }

        this.mostrarMensaje(input, mensaje);
        return mensaje === "";
    },

    mostrarMensaje: function(input, mensaje) {
        let container = input.parentElement; 
        let errorSpan = container.querySelector('.error-msg');

        if (mensaje) {
            if (!errorSpan) {
                errorSpan = document.createElement('span');
                errorSpan.className = 'error-msg text-danger small fw-bold mt-1 d-block';
                input.insertAdjacentElement('afterend', errorSpan);
            }
            input.classList.add('is-invalid');
            input.classList.remove('is-valid');
            errorSpan.innerText = mensaje;
            errorSpan.style.display = 'block';
        } else {
            input.classList.remove('is-invalid');
            if (input.value.trim() !== "") {
                input.classList.add('is-valid');
            }
            if (errorSpan) {
                errorSpan.innerText = "";
                errorSpan.style.display = 'none';
            }
        }
    },

    limitarCalendario: function(selector) {
            const inputFecha = document.querySelector(selector);
            if (inputFecha) {
                const hoy = new Date();
                const maxFecha = new Date(hoy.getFullYear() - 15, hoy.getMonth(), hoy.getDate());
                // Formato YYYY-MM-DD para el atributo 'max' del input date
                inputFecha.max = maxFecha.toISOString().split("T")[0];
            }
    }
};