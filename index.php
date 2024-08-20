<?php
include('conexion.php');
include('header.php');
?>

<main class="container mt-5 background-index">
    <div class="jumbotron text-center">
        <h1 class="display-4">Bienvenidos a la Plataforma Educativa</h1>
        <p class="lead">Accede a tus cursos, revisa tus notas y mucho más.</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="login.php" class="btn btn-primary btn-lg">Iniciar Sesión</a>
            <a href="registro.php" class="btn btn-secondary btn-lg">Registrarse</a>
        <?php else: ?>
            <a href="perfil.php" class="btn btn-primary btn-lg">Ver Perfil</a>
        <?php endif; ?>
    </div>

    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Gestión de Cursos</h5>
                    <p class="card-text">Accede a toda la información de tus cursos en un solo lugar.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Calificaciones en Línea</h5>
                    <p class="card-text">Revisa tus notas y calificaciones de manera sencilla y rápida.</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Interfaz Amigable</h5>
                    <p class="card-text">Navega de manera intuitiva por la plataforma.</p>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include('footer.php');
?>
