<?php

include('conexion.php');
include('header.php');

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Obtener la lista de todos los cursos
$sql = "SELECT c.id_curso, c.nombre_curso FROM cursos c";
$result = $conn->query($sql);

if ($result === false) {
    die("Error en la consulta de cursos: " . $conn->error);
}

$cursos = $result->fetch_all(MYSQLI_ASSOC);

// Obtener la lista de todos los usuarios (docentes y estudiantes)
$sql_usuarios = "SELECT id_usuario, nombre_usuario, tipo_usuario FROM usuarios";
$result_usuarios = $conn->query($sql_usuarios);

if ($result_usuarios === false) {
    die("Error en la consulta de usuarios: " . $conn->error);
}

$usuarios = $result_usuarios->fetch_all(MYSQLI_ASSOC);

?>
<link rel="stylesheet" href="styles.css">
<script src="scripts.js" defer></script>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">Gestión de Cursos</h3>
                    <div class="text-right mb-3">
                        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#crearCursoModal">
                            Crear Curso
                        </button>
                    </div>
                    <?php if (empty($cursos)): ?>
                        <p class="text-center">No hay cursos registrados.</p>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Curso</th>
                                    <th>Docente Asignado</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cursos as $curso): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($curso['id_curso']); ?></td>
                                        <td><?php echo htmlspecialchars($curso['nombre_curso']); ?></td>
                                        <td>
                                            <?php
                                            // Obtener el docente asignado al curso actual
                                            $sql_docente = "SELECT u.nombre_usuario, u.id_usuario
                                                            FROM usuarios u
                                                            JOIN usuarios_cursos uc ON u.id_usuario = uc.id_usuario
                                                            WHERE uc.id_curso = ? AND u.tipo_usuario = 'docente'";
                                            $stmt_docente = $conn->prepare($sql_docente);
                                            $stmt_docente->bind_param("i", $curso['id_curso']);
                                            $stmt_docente->execute();
                                            $stmt_docente->bind_result($nombre_docente, $id_docente);
                                            $stmt_docente->fetch();
                                            $stmt_docente->close();
                                            echo htmlspecialchars($nombre_docente);
                                            ?>
                                        </td>
                                        <td class="text-right">
                                            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#editarCursoModal<?php echo $curso['id_curso']; ?>">
                                                Editar
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#eliminarCursoModal<?php echo $curso['id_curso']; ?>">
                                                Eliminar
                                            </button>
                                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#asignarUsuariosModal<?php echo $curso['id_curso']; ?>">
                                                Asignar Usuarios
                                            </button>
                                            <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#mostrarEstudiantesModal<?php echo $curso['id_curso']; ?>">
                                                Mostrar Estudiantes
                                            </button>
                                        </td>
                                    </tr>

                                    <!-- Modal de edición de curso -->
                                    <div class="modal fade" id="editarCursoModal<?php echo $curso['id_curso']; ?>" tabindex="-1" aria-labelledby="editarCursoModalLabel<?php echo $curso['id_curso']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editarCursoModalLabel<?php echo $curso['id_curso']; ?>">Editar Curso</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="editar_curso.php" method="post">
                                                        <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">
                                                        <div class="form-group">
                                                            <label for="nombre_curso">Nombre del Curso</label>
                                                            <input type="text" name="nombre_curso" class="form-control" value="<?php echo htmlspecialchars($curso['nombre_curso']); ?>" required>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="id_docente">Docente Asignado</label>
                                                            <select name="id_docente" class="form-control" required>
                                                                <option value="">-- Seleccionar Docente --</option>
                                                                <?php foreach ($usuarios as $usuario): ?>
                                                                    <?php if ($usuario['tipo_usuario'] == 'docente'): ?>
                                                                        <option value="<?php echo $usuario['id_usuario']; ?>" <?php echo $usuario['id_usuario'] == $id_docente ? 'selected' : ''; ?>>
                                                                            <?php echo htmlspecialchars($usuario['nombre_usuario']); ?>
                                                                        </option>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de eliminación de curso -->
                                    <div class="modal fade" id="eliminarCursoModal<?php echo $curso['id_curso']; ?>" tabindex="-1" aria-labelledby="eliminarCursoModalLabel<?php echo $curso['id_curso']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="eliminarCursoModalLabel<?php echo $curso['id_curso']; ?>">Eliminar Curso</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>¿Está seguro de que desea eliminar el curso "<?php echo htmlspecialchars($curso['nombre_curso']); ?>"?</p>
                                                    <form action="eliminar_curso.php" method="post">
                                                        <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">
                                                        <button type="submit" class="btn btn-danger">Eliminar</button>
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de asignación de usuarios -->
                                    <div class="modal fade" id="asignarUsuariosModal<?php echo $curso['id_curso']; ?>" tabindex="-1" aria-labelledby="asignarUsuariosModalLabel<?php echo $curso['id_curso']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="asignarUsuariosModalLabel<?php echo $curso['id_curso']; ?>">Asignar Usuarios al Curso</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="asignar_usuarios.php" method="post">
                                                        <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">
                                                        <div class="form-group">
                                                            <label for="usuarios">Seleccionar Usuarios</label>
                                                            <select multiple class="form-control" name="usuarios[]" required>
                                                                <?php
                                                                // Obtener los estudiantes no asignados al curso
                                                                $sql_estudiantes_disponibles = "SELECT u.id_usuario, u.nombre_usuario
                                                                                                FROM usuarios u
                                                                                                LEFT JOIN usuarios_cursos uc ON u.id_usuario = uc.id_usuario AND uc.id_curso = ?
                                                                                                WHERE u.tipo_usuario = 'estudiante' AND uc.id_usuario IS NULL";
                                                                $stmt_estudiantes_disponibles = $conn->prepare($sql_estudiantes_disponibles);
                                                                $stmt_estudiantes_disponibles->bind_param("i", $curso['id_curso']);
                                                                $stmt_estudiantes_disponibles->execute();
                                                                $stmt_estudiantes_disponibles->bind_result($id_usuario_disponible, $nombre_usuario_disponible);
                                                                while ($stmt_estudiantes_disponibles->fetch()) {
                                                                    echo "<option value='" . htmlspecialchars($id_usuario_disponible) . "'>" . htmlspecialchars($nombre_usuario_disponible) . "</option>";
                                                                }
                                                                $stmt_estudiantes_disponibles->close();
                                                                ?>
                                                            </select>
                                                        </div>
                                                        <button type="submit" class="btn btn-primary">Asignar Usuarios</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal de mostrar estudiantes -->
                                    <div class="modal fade" id="mostrarEstudiantesModal<?php echo $curso['id_curso']; ?>" tabindex="-1" aria-labelledby="mostrarEstudiantesModalLabel<?php echo $curso['id_curso']; ?>" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="mostrarEstudiantesModalLabel<?php echo $curso['id_curso']; ?>">Estudiantes en el Curso</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <ul>
                                                        <?php
                                                        // Obtener los estudiantes inscritos en el curso actual
                                                        $sql_estudiantes = "SELECT u.nombre_usuario 
                                                                            FROM usuarios u 
                                                                            JOIN usuarios_cursos uc ON u.id_usuario = uc.id_usuario 
                                                                            WHERE uc.id_curso = ? AND u.tipo_usuario = 'estudiante'";
                                                        $stmt_estudiantes = $conn->prepare($sql_estudiantes);
                                                        $stmt_estudiantes->bind_param("i", $curso['id_curso']);
                                                        $stmt_estudiantes->execute();
                                                        $stmt_estudiantes->bind_result($nombre_estudiante);
                                                        while ($stmt_estudiantes->fetch()) {
                                                            echo "<li>" . htmlspecialchars($nombre_estudiante) . "</li>";
                                                        }
                                                        $stmt_estudiantes->close();
                                                        ?>
                                                    </ul>
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

<!-- Modal de creación de curso -->
<div class="modal fade" id="crearCursoModal" tabindex="-1" aria-labelledby="crearCursoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="crearCursoModalLabel">Crear Curso</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="crear_curso.php" method="post">
                    <div class="form-group">
                        <label for="nombre_curso">Nombre del Curso</label>
                        <input type="text" name="nombre_curso" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="id_docente">Asignar Docente</label>
                        <select name="id_docente" class="form-control" required>
                            <option value="">-- Seleccionar Docente --</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <?php if ($usuario['tipo_usuario'] == 'docente'): ?>
                                    <option value="<?php echo $usuario['id_usuario']; ?>">
                                        <?php echo htmlspecialchars($usuario['nombre_usuario']); ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success">Crear Curso</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
// Cerrar la conexión a la base de datos al final del script
$conn->close();
include('footer.php');
?>
