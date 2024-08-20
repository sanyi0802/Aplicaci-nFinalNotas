<?php
include('conexion.php');
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Verificar si se ha enviado el formulario de creación de curso
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_curso = trim($_POST['nombre_curso']);
    $id_docente = $_POST['id_docente'];

    // Validar datos de entrada
    if (empty($nombre_curso) || empty($id_docente)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: admin_cursos.php");
        exit;
    }

    // Verificar si el docente es válido
    $sql_docente = "SELECT id_usuario FROM usuarios WHERE id_usuario = ? AND tipo_usuario = 'docente'";
    $stmt_docente = $conn->prepare($sql_docente);
    $stmt_docente->bind_param("i", $id_docente);
    $stmt_docente->execute();
    $stmt_docente->store_result();

    if ($stmt_docente->num_rows == 0) {
        $stmt_docente->close();
        $_SESSION['error'] = "El docente seleccionado no es válido.";
        header("Location: admin_cursos.php");
        exit;
    }

    $stmt_docente->close();

    // Insertar el nuevo curso en la base de datos
    $sql_insert_curso = "INSERT INTO cursos (nombre_curso) VALUES (?)";
    $stmt_insert_curso = $conn->prepare($sql_insert_curso);
    $stmt_insert_curso->bind_param("s", $nombre_curso);

    if ($stmt_insert_curso->execute()) {
        // Obtener el ID del curso insertado
        $id_curso = $stmt_insert_curso->insert_id;
        $stmt_insert_curso->close();

        // Asignar el docente al curso
        $sql_asignar_docente = "INSERT INTO usuarios_cursos (id_usuario, id_curso) VALUES (?, ?)";
        $stmt_asignar_docente = $conn->prepare($sql_asignar_docente);
        $stmt_asignar_docente->bind_param("ii", $id_docente, $id_curso);

        if ($stmt_asignar_docente->execute()) {
            $stmt_asignar_docente->close();
            $conn->close();
            $_SESSION['message'] = "Curso creado y docente asignado con éxito.";
            header("Location: admin_cursos.php");
            exit;
        } else {
            // En caso de error al asignar el docente, eliminar el curso
            $stmt_asignar_docente->close();
            $conn->query("DELETE FROM cursos WHERE id_curso = $id_curso");
            $conn->close();
            $_SESSION['error'] = "Error al asignar el docente al curso. Por favor, intenta de nuevo.";
            header("Location: admin_cursos.php");
            exit;
        }
    } else {
        $stmt_insert_curso->close();
        $conn->close();
        $_SESSION['error'] = "Error al crear el curso. Por favor, intenta de nuevo.";
        header("Location: admin_cursos.php");
        exit;
    }
} else {
    // Si no se envió el formulario, redirigir a la gestión de cursos
    header("Location: admin_cursos.php");
    exit;
}
?>
