<?php
include('conexion.php');
include('header.php');

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Obtener la lista de todos los usuarios
$sql = "SELECT id_usuario, nombre_usuario, email, tipo_usuario FROM usuarios";
$result = $conn->query($sql);
$usuarios = $result->fetch_all(MYSQLI_ASSOC);

$conn->close();
?>
<link rel="stylesheet" href="styles.css">
<script src="scripts.js" defer></script>


<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">Gestión de Usuarios</h3>
                    <div class="text-right mb-3">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#crearUsuarioModal">
                            Crear Usuario
                        </button>
                    </div>
                    <?php if (empty($usuarios)): ?>
                        <p class="text-center">No hay usuarios registrados.</p>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Tipo de Usuario</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($usuario['id_usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['nombre_usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                                        <td><?php echo htmlspecialchars($usuario['tipo_usuario']); ?></td>
                                        <td class="text-right">
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editarUsuarioModal<?php echo $usuario['id_usuario']; ?>">
                                                Editar
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#eliminarUsuarioModal<?php echo $usuario['id_usuario']; ?>">
                                                Eliminar
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal de edición de usuario -->
                                    <div class="modal fade" id="editarUsuarioModal<?php echo $usuario['id_usuario']; ?>" tabindex="-1" aria-labelledby="editarUsuarioModalLabel<?php echo $usuario['id_usuario']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editarUsuarioModalLabel<?php echo $usuario['id_usuario']; ?>">Editar Usuario</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="editar_usuario.php" method="post">
                                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                                        <div class="form-group">
                                                            <label for="nombre_usuario">Nombre Completo</label>
                                                            <input type="text" name="nombre_usuario" class="form-control" value="<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="email">Correo Electrónico</label>
                                                            <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($usuario['email']); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="tipo_usuario">Tipo de Usuario</label>
                                                            <select name="tipo_usuario" class="form-control" required>
                                                                <option value="estudiante" <?php echo $usuario['tipo_usuario'] == 'estudiante' ? 'selected' : ''; ?>>Estudiante</option>
                                                                <option value="docente" <?php echo $usuario['tipo_usuario'] == 'docente' ? 'selected' : ''; ?>>Docente</option>
                                                                <option value="administrador" <?php echo $usuario['tipo_usuario'] == 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de eliminación de usuario -->
                                    <div class="modal fade" id="eliminarUsuarioModal<?php echo $usuario['id_usuario']; ?>" tabindex="-1" aria-labelledby="eliminarUsuarioModalLabel<?php echo $usuario['id_usuario']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="eliminarUsuarioModalLabel<?php echo $usuario['id_usuario']; ?>">Eliminar Usuario</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Está seguro de que desea eliminar al usuario "<?php echo htmlspecialchars($usuario['nombre_usuario']); ?>"?</p>
                                                    <form action="eliminar_usuario.php" method="post">
                                                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal de creación de usuario -->
<div class="modal fade" id="crearUsuarioModal" tabindex="-1" aria-labelledby="crearUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearUsuarioModalLabel">Crear Usuario</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="crear_usuario.php" method="post">
                    <div class="form-group">
                        <label for="nombre_usuario">Nombre Completo</label>
                        <input type="text" name="nombre_usuario" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="tipo_usuario">Tipo de Usuario</label>
                        <select name="tipo_usuario" class="form-control" required>
                            <option value="estudiante">Estudiante</option>
                            <option value="docente">Docente</option>
                            <option value="administrador">Administrador</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Crear Usuario</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
include('footer.php');
?>
