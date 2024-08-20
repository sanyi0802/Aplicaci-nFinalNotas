<?php
include('conexion.php');
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Verificar si se ha enviado el formulario de eliminación de curso
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_curso = $_POST['id_curso'];

    // Validar que el id_curso no esté vacío
    if (empty($id_curso)) {
        $_SESSION['error'] = "ID de curso inválido.";
        header("Location: admin_cursos.php");
        exit;
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Eliminar las asignaciones de usuarios (tanto estudiantes como docentes) al curso
        $sql_delete_usuarios = "DELETE FROM usuarios_cursos WHERE id_curso = ?";
        $stmt_delete_usuarios = $conn->prepare($sql_delete_usuarios);
        $stmt_delete_usuarios->bind_param("i", $id_curso);
        $stmt_delete_usuarios->execute();
        $stmt_delete_usuarios->close();

        // Eliminar el curso de la base de datos
        $sql_delete_curso = "DELETE FROM cursos WHERE id_curso = ?";
        $stmt_delete_curso = $conn->prepare($sql_delete_curso);
        $stmt_delete_curso->bind_param("i", $id_curso);
        $stmt_delete_curso->execute();
        $stmt_delete_curso->close();

        // Confirmar transacción
        $conn->commit();

        $_SESSION['message'] = "Curso y asignaciones eliminados con éxito.";
        header("Location: admin_cursos.php");
        exit;
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();

        $_SESSION['error'] = "Error al eliminar el curso. Por favor, intenta de nuevo.";
        header("Location: admin_cursos.php");
        exit;
    }
} else {
    // Redirigir si no se envió el formulario
    header("Location: admin_cursos.php");
    exit;
}
?>
