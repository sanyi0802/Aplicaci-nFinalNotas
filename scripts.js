
document.addEventListener('DOMContentLoaded', function() {
    var tipoUsuarioSelect = document.getElementById('tipo_usuario');
    var codigoDocenteGroup = document.getElementById('codigo-docente-group');

    tipoUsuarioSelect.addEventListener('change', function () {
        if (this.value === 'docente') {
            codigoDocenteGroup.style.display = 'block';
        } else {
            codigoDocenteGroup.style.display = 'none';
        }
    });
});

// Cargar jQuery
document.write('<script src="https://code.jquery.com/jquery-3.5.1.min.js"><\/script>');

// Cargar Popper.js
document.write('<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"><\/script>');

// Cargar Bootstrap JS
document.write('<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"><\/script>');
