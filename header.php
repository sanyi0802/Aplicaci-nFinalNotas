<?php
session_start();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institución Educativa</title>
    <!-- Enlace a Bootstrap para la barra de navegación -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Enlace al archivo CSS personalizado -->
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="index.php">Educación</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Inicio</a>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <?php if ($_SESSION['tipo_usuario'] == 'estudiante'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="notas.php">Ver Notas</a>
                            </li>
                        <?php elseif ($_SESSION['tipo_usuario'] == 'docente'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="cursos.php">Gestionar Cursos</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="notas_docente.php">Calificar Estudiantes</a>
                            </li>
                        <?php elseif ($_SESSION['tipo_usuario'] == 'administrador'): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin_usuarios.php">Gestionar Usuarios</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="admin_cursos.php">Gestionar Cursos</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="perfil.php">Perfil</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Iniciar Sesión</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="registro.php">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Enlace a jQuery, Popper.js, y Bootstrap JS desde el CDN -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js" integrity="sha384-X49UqPb6z+8XK0FlFA9nlgRDyX+qOmbpZ9z6ZtSAtS5bFFM5VWWHNyXJ58p5Nx4K" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgjC07dHFsmCl4yhbZ+Y5nwFpH9WgFPxNwb3amwRy3A1mfbyDX5" crossorigin="anonymous"></script>

    <!-- Enlace al archivo JS personalizado -->
    <script src="scripts.js"></script>
</body>

</html>
