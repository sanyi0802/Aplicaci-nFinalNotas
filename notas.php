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
$tipo_usuario = $_SESSION['tipo_usuario'];

// Consulta para obtener las notas de un estudiante o los cursos que enseña un docente
if ($tipo_usuario == 'estudiante') {
    $sql = "SELECT c.nombre_curso, n.nota 
            FROM cursos c 
            JOIN usuarios_cursos uc ON c.id_curso = uc.id_curso 
            JOIN notas n ON uc.id_usuario_curso = n.id_usuario_curso 
            WHERE uc.id_usuario = ?";
} else if ($tipo_usuario == 'docente') {
    $sql = "SELECT c.nombre_curso 
            FROM cursos c 
            JOIN usuarios_cursos uc ON c.id_curso = uc.id_curso 
            WHERE uc.id_usuario = ? AND uc.tipo_usuario = 'docente'";
} else {
    // Para otros tipos de usuario, puedes manejarlo de acuerdo a tus necesidades
    $sql = "";
}

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$cursos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();
?>

<main class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title text-center">Notas</h3>
                    <?php if (empty($cursos)): ?>
                        <p class="text-center">No se encontraron cursos o notas.</p>
                    <?php else: ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Curso</th>
                                    <?php if ($tipo_usuario == 'estudiante'): ?>
                                        <th>Nota</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cursos as $curso): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($curso['nombre_curso']); ?></td>
                                        <?php if ($tipo_usuario == 'estudiante'): ?>
                                            <td><?php echo htmlspecialchars($curso['nota']); ?></td>
                                        <?php endif; ?>
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
