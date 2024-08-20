<?php
include('conexion.php');
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Verificar si se ha enviado el formulario de asignación de usuarios
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_curso = $_POST['id_curso'];
    $usuarios = $_POST['usuarios'];

    // Validar datos de entrada
    if (empty($id_curso) || empty($usuarios)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: admin_cursos.php");
        exit;
    }

    // Insertar nuevos estudiantes en el curso
    $sql_insert = "INSERT INTO usuarios_cursos (id_usuario, id_curso) VALUES (?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);

    foreach ($usuarios as $id_usuario) {
        // Verificar si el usuario ya está asignado al curso
        $sql_check = "SELECT id_usuario_curso FROM usuarios_cursos WHERE id_usuario = ? AND id_curso = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ii", $id_usuario, $id_curso);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows == 0) {
            // Si no está asignado, realizar la asignación
            $stmt_insert->bind_param("ii", $id_usuario, $id_curso);
            $stmt_insert->execute();
        }

        $stmt_check->close();
    }

    $stmt_insert->close();
    $conn->close();

    $_SESSION['message'] = "Usuarios asignados al curso con éxito.";
    header("Location: admin_cursos.php");
    exit;
} else {
    // Redirigir si no se envió el formulario
    header("Location: admin_cursos.php");
    exit;
}
?>
