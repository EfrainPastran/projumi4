$(document).ready(function() {
    // Función para validar al presionar tecla
    function validarkeypress(regex, e) {
        var key = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (!regex.test(key)) {
            e.preventDefault();
            return false;
        }
    }

    // Función para validar al soltar tecla
    function validarkeyup(regex, input, span, mensaje) {
        if (!regex.test(input.val())) {
            span.text(mensaje).css("color", "red").show();
            return false;
        } else {
            span.text("✓").css("color", "green").show();
            return true;
        }
    }

    // Validación para cédula (solo números)
    $("#ced").prop("maxlength", "12");
    $("#ced").on("keypress", function(e) {
        validarkeypress(/^[0-9\b]*$/, e);
    });
    $("#ced").on("keyup", function() {
        validarkeyup(/^[0-9]{6,12}$/, $(this), $("#scedula"), "La cédula debe tener 6-12 dígitos");
    });

    // Validación para contraseña
    $("#pass").prop("maxlength", "30");
    $("#pass").on("keypress", function(e) {
        validarkeypress(/^[a-zA-Z0-9@.$!%*?&\-\b]*$/, e);
    });
    $("#pass").on("keyup", function() {
        validarkeyup(/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@.$!%*?&-]{8,}$/, $(this), $("#spass"), "Mínimo 8 caracteres, al menos 1 letra y 1 número");
    });

    // Validar antes de enviar el formulario
    $("form").on("submit", function(e) {
        let cedulaValida = validarkeyup(/^[0-9]{6,12}$/, $("#ced"), $("#scedula"), "La cédula debe tener 6-12 dígitos");
        let passValida = validarkeyup(/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@.$!%*?&-]{8,}$/, $("#pass"), $("#spass"), "Mínimo 8 caracteres, al menos 1 letra y 1 número");
        
        if (!cedulaValida || !passValida) {
            e.preventDefault();
            // Opcional: Mostrar mensaje general de error
            alert("Por favor complete correctamente todos los campos");
        }
    });
});