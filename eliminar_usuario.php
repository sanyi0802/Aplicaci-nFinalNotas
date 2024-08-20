<?php
include('conexion.php');
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Verificar si se ha recibido el ID del usuario a eliminar
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_usuario'])) {
    $id_usuario = $_POST['id_usuario'];

    // Preparar la declaración para eliminar el usuario
    $sql = "DELETE FROM usuarios WHERE id_usuario = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_usuario);

    // Intentar ejecutar la eliminación
    if ($stmt->execute()) {
        // Éxito en la eliminación
        $stmt->close();
        $conn->close();
        $_SESSION['message'] = "Usuario eliminado con éxito.";
        header("Location: admin_usuarios.php");
        exit;
    } else {
        // Error en la eliminación
        $stmt->close();
        $conn->close();
        $_SESSION['error'] = "Error al eliminar el usuario. Por favor, intenta de nuevo.";
        header("Location: admin_usuarios.php");
        exit;
    }
} else {
    // Redirigir si no se recibió un ID de usuario válido
    $_SESSION['error'] = "Acción no válida.";
    header("Location: admin_usuarios.php");
    exit;
}
?>
