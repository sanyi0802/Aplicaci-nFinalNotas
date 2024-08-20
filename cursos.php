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
                    <h3 class="card-title text-center">Mis Cursos</h3>
                    <?php if (empty($cursos)): ?>
                        <p class="text-center">No estás asignado a ningún curso.</p>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <th>Estudiantes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cursos as $curso): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($curso['nombre_curso']); ?></td>
                                        <td>
                                            <?php
                                            // Obtener los estudiantes inscritos en el curso actual
                                            $sql_estudiantes = "SELECT u.nombre_usuario 
                                                                FROM usuarios u 
                                                                JOIN usuarios_cursos uc ON u.id_usuario = uc.id_usuario 
                                                                WHERE uc.id_curso = ? AND u.tipo_usuario = 'estudiante'";
                                            $stmt_estudiantes = $conn->prepare($sql_estudiantes);
                                            $stmt_estudiantes->bind_param("i", $curso['id_curso']);
                                            $stmt_estudiantes->execute();
                                            $result_estudiantes = $stmt_estudiantes->get_result();
                                            $estudiantes = $result_estudiantes->fetch_all(MYSQLI_ASSOC);
                                            $stmt_estudiantes->close();

                                            if (empty($estudiantes)): ?>
                                                <p>No hay estudiantes inscritos.</p>
                                            <?php else: ?>
                                                <ul>
                                                    <?php foreach ($estudiantes as $estudiante): ?>
                                                        <li><?php echo htmlspecialchars($estudiante['nombre_usuario']); ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
include('footer.php');
?>
