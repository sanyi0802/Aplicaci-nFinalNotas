<?php
include('conexion.php');
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Verificar si se ha enviado el formulario de edición de curso
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_curso = $_POST['id_curso'];
    $nombre_curso = trim($_POST['nombre_curso']);
    $id_docente = $_POST['id_docente'];

    // Validar datos de entrada
    if (empty($nombre_curso) || empty($id_docente)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: admin_cursos.php");
        exit;
    }

    // Actualizar la información del curso en la base de datos
    $sql_update = "UPDATE cursos SET nombre_curso = ? WHERE id_curso = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("si", $nombre_curso, $id_curso);

    if ($stmt_update->execute()) {
        $stmt_update->close();

        // Actualizar la asignación del docente
        // Eliminar la asignación previa si existe
        $sql_delete_docente = "DELETE FROM usuarios_cursos WHERE id_curso = ? AND id_usuario = ? AND id_usuario IN (SELECT id_usuario FROM usuarios WHERE tipo_usuario = 'docente')";
        $stmt_delete_docente = $conn->prepare($sql_delete_docente);
        $stmt_delete_docente->bind_param("ii", $id_curso, $id_docente);
        $stmt_delete_docente->execute();
        $stmt_delete_docente->close();

        // Asignar el nuevo docente al curso
        $sql_asignar_docente = "INSERT INTO usuarios_cursos (id_usuario, id_curso) VALUES (?, ?)";
        $stmt_asignar_docente = $conn->prepare($sql_asignar_docente);
        $stmt_asignar_docente->bind_param("ii", $id_docente, $id_curso);

        if ($stmt_asignar_docente->execute()) {
            $stmt_asignar_docente->close();
            $conn->close();
            $_SESSION['message'] = "Curso actualizado con éxito.";
            header("Location: admin_cursos.php");
            exit;
        } else {
            $stmt_asignar_docente->close();
            $conn->close();
            $_SESSION['error'] = "Error al asignar el docente. Por favor, intenta de nuevo.";
            header("Location: admin_cursos.php");
            exit;
        }
    } else {
        $stmt_update->close();
        $conn->close();
        $_SESSION['error'] = "Error al actualizar el curso. Por favor, intenta de nuevo.";
        header("Location: admin_cursos.php");
        exit;
    }
} else {
    // Redirigir si no se envió el formulario
    header("Location: admin_cursos.php");
    exit;
}
?>
