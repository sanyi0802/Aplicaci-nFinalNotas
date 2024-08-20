<?php
include('conexion.php');
include('header.php');

// Verificar si el usuario está logueado
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Obtener la información del usuario
$user_id = $_SESSION['user_id'];
$sql = "SELECT nombre_usuario, email, tipo_usuario FROM usuarios WHERE id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($nombre_usuario, $email, $tipo_usuario);
$stmt->fetch();
$stmt->close();

// Verificar si el formulario de edición fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_nombre = $_POST['nombre_usuario'];
    $nuevo_email = $_POST['email'];
    $nuevo_tipo = $tipo_usuario; // Mantener el tipo de usuario actual por defecto
    $nueva_password = $_POST['password'];

    // Si el usuario es administrador, permitir cambiar el tipo de usuario
    if ($_SESSION['tipo_usuario'] == 'administrador' && isset($_POST['tipo_usuario'])) {
        $nuevo_tipo = $_POST['tipo_usuario'];
    }

    // Validar los datos recibidos
    if (!empty($nuevo_nombre) && !empty($nuevo_email)) {
        if (!empty($nueva_password)) {
            $nueva_password = password_hash($nueva_password, PASSWORD_DEFAULT);
            $sql_update = "UPDATE usuarios SET nombre_usuario = ?, email = ?, tipo_usuario = ?, password = ? WHERE id_usuario = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("ssssi", $nuevo_nombre, $nuevo_email, $nuevo_tipo, $nueva_password, $user_id);
        } else {
            $sql_update = "UPDATE usuarios SET nombre_usuario = ?, email = ?, tipo_usuario = ? WHERE id_usuario = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("sssi", $nuevo_nombre, $nuevo_email, $nuevo_tipo, $user_id);
        }

        if ($stmt_update->execute()) {
            $success = "Perfil actualizado con éxito.";
            // Actualizar variables de sesión
            $_SESSION['nombre_usuario'] = $nuevo_nombre;
            $_SESSION['email'] = $nuevo_email;
            $_SESSION['tipo_usuario'] = $nuevo_tipo;
            // Refrescar la página para ver los cambios
            header("Refresh:0");
        } else {
            $error = "Error al actualizar el perfil. Por favor, intenta de nuevo. " . $stmt_update->error;
        }

        $stmt_update->close();
    } else {
        $error = "Por favor, complete todos los campos obligatorios.";
    }
    $conn->close();
}
?>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">Perfil del Usuario</h3>
                    <?php if (isset($success)): ?>
                        <div class="alert alert-success">
                            <?php echo $success; ?>
                        </div>
                    <?php elseif (isset($error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <div class="mb-3">
                        <span class="text-label">Nombre Completo: </span>
                        <span class="text-value"><?php echo htmlspecialchars($nombre_usuario); ?></span>
                    </div>
                    <div class="mb-3">
                        <span class="text-label">Correo Electrónico: </span>
                        <span class="text-value"><?php echo htmlspecialchars($email); ?></span>
                    </div>
                    <div class="mb-3">
                        <span class="text-label">Tipo de Usuario: </span>
                        <span class="text-value"><?php echo htmlspecialchars($tipo_usuario); ?></span>
                    </div>
                    <div class="text-center">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editarPerfilModal">
                            Editar Perfil
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal de edición de perfil -->
<div class="modal fade" id="editarPerfilModal" tabindex="-1" aria-labelledby="editarPerfilModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editarPerfilModalLabel">Editar Perfil</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="perfil.php" method="post">
                    <div class="form-group">
                        <label for="nombre_usuario">Nombre Completo</label>
                        <input type="text" name="nombre_usuario" id="nombre_usuario" class="form-control" value="<?php echo htmlspecialchars($nombre_usuario); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <?php if ($_SESSION['tipo_usuario'] == 'administrador'): ?>
                        <div class="form-group">
                            <label for="tipo_usuario">Tipo de Usuario</label>
                            <select name="tipo_usuario" id="tipo_usuario" class="form-control" required>
                                <option value="estudiante" <?php echo $tipo_usuario == 'estudiante' ? 'selected' : ''; ?>>Estudiante</option>
                                <option value="docente" <?php echo $tipo_usuario == 'docente' ? 'selected' : ''; ?>>Docente</option>
                                <option value="administrador" <?php echo $tipo_usuario == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="password">Nueva Contraseña (dejar en blanco para no cambiar)</label>
                        <input type="password" name="password" id="password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .text-label {
        color: #6c757d; /* Color gris para el texto descriptivo */
        font-weight: bold;
    }
    .text-value {
        color: #343a40; /* Color oscuro para el valor */
    }
</style>

<?php
include('footer.php');
?>
