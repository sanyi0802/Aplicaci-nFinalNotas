<?php
include('conexion.php');
include('header.php');

// Verificar si el usuario está logueado y es docente
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'docente') {
    header("Location: login.php");
    exit;
}

// Obtener la información de los cursos del docente
$user_id = $_SESSION['user_id'];
$sql = "SELECT c.id_curso, c.nombre_curso 
        FROM cursos c 
        JOIN usuarios_cursos uc ON c.id_curso = uc.id_curso 
        WHERE uc.id_usuario = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cursos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">Calificar Estudiantes</h3>
                    <?php if (empty($cursos)): ?>
                        <p class="text-center">No estás asignado a ningún curso.</p>
                    <?php else: ?>
                        <?php foreach ($cursos as $curso): ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5><?php echo htmlspecialchars($curso['nombre_curso']); ?></h5>
                                </div>
                                <div class="card-body">
                                    <?php
                                    // Obtener los estudiantes inscritos en el curso actual con sus notas
                                    $sql_estudiantes = "SELECT u.id_usuario, u.nombre_usuario, n.id_nota, n.nota
                                                        FROM usuarios u
                                                        JOIN usuarios_cursos uc ON u.id_usuario = uc.id_usuario
                                                        LEFT JOIN notas n ON uc.id_usuario_curso = n.id_usuario_curso
                                                        WHERE uc.id_curso = ? AND u.tipo_usuario = 'estudiante'";
                                    $stmt_estudiantes = $conn->prepare($sql_estudiantes);
                                    $stmt_estudiantes->bind_param("i", $curso['id_curso']);
                                    $stmt_estudiantes->execute();
                                    $result_estudiantes = $stmt_estudiantes->get_result();
                                    $estudiantes = $result_estudiantes->fetch_all(MYSQLI_ASSOC);
                                    $stmt_estudiantes->close();
                                    ?>

                                    <?php if (empty($estudiantes)): ?>
                                        <p>No hay estudiantes inscritos.</p>
                                    <?php else: ?>
                                        <form action="guardar_notas.php" method="post">
                                            <input type="hidden" name="id_curso" value="<?php echo $curso['id_curso']; ?>">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Estudiante</th>
                                                        <th>Nota</th>
                                                        <th>Acciones</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($estudiantes as $estudiante): ?>
                                                        <tr>
                                                            <td><?php echo htmlspecialchars($estudiante['nombre_usuario']); ?></td>
                                                            <td>
                                                                <?php if (!empty($estudiante['id_nota'])): ?>
                                                                    <input type="hidden" name="id_nota[]" value="<?php echo htmlspecialchars($estudiante['id_nota']); ?>">
                                                                    <input type="number" name="nota[]" class="form-control" step="0.01" min="0" max="10" value="<?php echo htmlspecialchars($estudiante['nota']); ?>">
                                                                <?php else: ?>
                                                                    <input type="hidden" name="id_nota[]" value="new">
                                                                    <input type="hidden" name="id_usuario[]" value="<?php echo $estudiante['id_usuario']; ?>">
                                                                    <input type="number" name="nota[]" class="form-control" step="0.01" min="0" max="10" placeholder="Agregar nota">
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($estudiante['id_nota'])): ?>
                                                                    <a href="eliminar_nota.php?id_nota=<?php echo $estudiante['id_nota']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                            <div class="text-right">
                                                <button type="submit" class="btn btn-primary">Guardar Notas</button>
                                            </div>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include('footer.php');
?>
