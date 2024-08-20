<?php
include('conexion.php');
include('header.php');

// Verificar si el formulario de inicio de sesión fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Consultar en la base de datos
    $sql = "SELECT id_usuario, password, tipo_usuario FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // Verificar si el usuario existe
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_usuario, $hashed_password, $tipo_usuario);
        $stmt->fetch();

        // Verificar la contraseña
        if (password_verify($password, $hashed_password)) {
            // Iniciar sesión y almacenar información en la sesión
            session_start();
            $_SESSION['user_id'] = $id_usuario;
            $_SESSION['tipo_usuario'] = $tipo_usuario; // Almacenar el tipo de usuario en la sesión
            header("Location: perfil.php");
            exit;
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "No existe una cuenta con ese correo electrónico.";
    }

    $stmt->close();
    $conn->close();
}
?>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-center">Iniciar Sesión</h3>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <form action="login.php" method="post">
                        <div class="form-group">
                            <label for="email">Correo Electrónico</label>
                            <input type="email" name="email" id="email" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña</label>
                            <input type="password" name="password" id="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="registro.php">¿No tienes una cuenta? Regístrate aquí</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include('footer.php');
?>
