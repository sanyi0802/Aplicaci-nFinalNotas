<?php
include('conexion.php');
include('header.php');

// Verificar si el formulario de registro fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = $_POST['nombre_usuario'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $tipo_usuario = $_POST['tipo_usuario'];

    // Validación del código de docente
    if ($tipo_usuario == 'docente' && $_POST['codigo_docente'] != '1010') {
        $error = "Código de docente incorrecto.";
    } else {
        // Insertar en la base de datos
        $sql = "INSERT INTO usuarios (nombre_usuario, email, password, tipo_usuario) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre_usuario, $email, $password, $tipo_usuario);

        if ($stmt->execute()) {
            $success = "Registro exitoso. Puedes iniciar sesión.";
        } else {
            $error = "Error en el registro. Por favor, intenta de nuevo.";
        }

        $stmt->close();
        $conn->close();
    }
}
?>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">Registrarse</h3>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php elseif (isset($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>
                    <form action="registro.php" method="post">
                        <div class="form-group">
                            <label for="nombre_usuario">Nombre Completo</label>
                            <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="tipo_usuario">Tipo de Usuario</label>
                            <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
                                <option value="estudiante">Estudiante</option>
                                <option value="docente">Docente</option>
                            </select>
                        </div>
                        <div class="form-group" id="codigo-docente-group" style="display:none;">
                            <label for="codigo_docente">Código de Docente</label>
                            <input type="text" name="codigo_docente" id="codigo_docente" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="login.php">¿Ya tienes una cuenta? Inicia sesión aquí</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include('footer.php');
?>
