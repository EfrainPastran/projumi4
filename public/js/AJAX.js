$('#editForm').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        success: function(response) {
            // Recargar solo la tabla o mostrar mensaje
            location.reload();
        },
        error: function(xhr) {
            alert('Error al actualizar: ' + xhr.responseText);
        }
    });
});