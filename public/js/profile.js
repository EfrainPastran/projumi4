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

    // Validación para contraseña
   // $("#pass").prop("maxlength", "30");
  //  $("#pass").on("keypress", function(e) {
 //       validarkeypress(/^[a-zA-Z0-9@.$!%*?&\-\b]*$/, e);
//    });
 //   $("#pass").on("keyup", function() {
 //       validarkeyup(/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@.$!%*?&-]{6,}$/, $(this), $("#spass"), "Escriba su contraseña correcta");
 //   });

    $("#nuevopass").prop("maxlength", "30");
    $("#nuevopass").on("keypress", function(e) {
        validarkeypress(/^[a-zA-Z0-9@.$!%*?&\-\b]*$/, e);
    });
    $("#nuevopass").on("keyup", function() {
        validarkeyup(/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@.$!%*?&-]{8,}$/, $(this), $("#snuevopass"), "Mínimo 8 caracteres, al menos 1 letra y 1 número");
    });

    $("#confirmpass").prop("maxlength", "30");
    $("#confirmpass").on("keypress", function(e) {
        validarkeypress(/^[a-zA-Z0-9@.$!%*?&\-\b]*$/, e);
    });
    $("#confirmpass").on("keyup", function() {
        validarkeyup(/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@.$!%*?&-]{8,}$/, $(this), $("#sconfirmpass"), "Mínimo 8 caracteres, al menos 1 letra y 1 número");
    });

    // Validar antes de enviar el formulario
    $("form").on("submit", function(e) {
      //  let passValida = validarkeyup(/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@.$!%*?&-]{8,}$/, $("#pass"), $("#spass"), "Mínimo 8 caracteres, al menos 1 letra y 1 número");
        let nuevopassValida = validarkeyup(/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@.$!%*?&-]{8,}$/, $("#nuevopass"), $("#snuevopass"), "Mínimo 8 caracteres, al menos 1 letra y 1 número");
        let confirmpassValida = validarkeyup(/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@.$!%*?&-]{8,}$/, $("#confirmpass"), $("#sconfirmpass"), "Mínimo 8 caracteres, al menos 1 letra y 1 número");
        
        if (!nuevopassValida || !confirmpassValida) {
            e.preventDefault();
            // Opcional: Mostrar mensaje general de error
            alert("Por favor complete correctamente todos los campos");
        }
    });
});