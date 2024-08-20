<?php
include('conexion.php');
session_start();

// Verificar si el usuario está logueado y es docente
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'docente') {
    header("Location: login.php");
    exit;
}

// Verificar si se ha enviado el formulario de calificaciones
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $notas = $_POST['nota'];
    $id_curso = $_POST['id_curso'];
    $id_notas = $_POST['id_nota'];
    $id_usuarios = isset($_POST['id_usuario']) ? $_POST['id_usuario'] : [];

    // Validar que el ID del curso no esté vacío
    if (empty($id_curso)) {
        $_SESSION['error'] = "ID de curso inválido.";
        header("Location: notas_docente.php");
        exit;
    }

    // Iniciar transacción
    $conn->begin_transaction();

    try {
        // Recorrer todas las notas enviadas
        foreach ($notas as $index => $nota) {
            $nota = filter_var($nota, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
            $id_nota = $id_notas[$index];
            
            // Validar la nota recibida
            if ($nota === false || $nota < 0 || $nota > 10) {
                $_SESSION['error'] = "Calificación inválida.";
                $conn->rollback();
                header("Location: notas_docente.php");
                exit;
            }

            if ($id_nota == 'new') {
                // Insertar nueva calificación
                $id_usuario = $id_usuarios[$index];
                // Obtener id_usuario_curso
                $sql_get_uc = "SELECT id_usuario_curso FROM usuarios_cursos WHERE id_usuario = ? AND id_curso = ?";
                $stmt_get_uc = $conn->prepare($sql_get_uc);
                $stmt_get_uc->bind_param("ii", $id_usuario, $id_curso);
                $stmt_get_uc->execute();
                $stmt_get_uc->bind_result($id_usuario_curso);
                $stmt_get_uc->fetch();
                $stmt_get_uc->close();
                
                $sql_insert = "INSERT INTO notas (id_usuario_curso, nota) VALUES (?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("id", $id_usuario_curso, $nota);
                $stmt_insert->execute();
                $stmt_insert->close();
            } else {
                // Actualizar calificación existente
                $sql_update = "UPDATE notas SET nota = ? WHERE id_nota = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("di", $nota, $id_nota);
                $stmt_update->execute();
                $stmt_update->close();
            }
        }

        // Confirmar transacción
        $conn->commit();

        $_SESSION['message'] = "Calificaciones guardadas con éxito.";
        header("Location: notas_docente.php");
        exit;
    } catch (Exception $e) {
        // Revertir transacción en caso de error
        $conn->rollback();

        $_SESSION['error'] = "Error al guardar las calificaciones. Por favor, intenta de nuevo.";
        header("Location: notas_docente.php");
        exit;
    }
} else {
    // Redirigir si no se envió el formulario
    header("Location: notas_docente.php");
    exit;
}
?>
