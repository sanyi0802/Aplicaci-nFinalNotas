<?php
include('conexion.php');
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Verificar si se ha enviado el formulario de edición de usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_usuario = $_POST['id_usuario'];
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $email = trim($_POST['email']);
    $tipo_usuario = trim($_POST['tipo_usuario']);

    // Validar datos de entrada
    if (empty($nombre_usuario) || empty($email) || empty($tipo_usuario)) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: admin_usuarios.php");
        exit;
    }

    // Validar formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Correo electrónico inválido.";
        header("Location: admin_usuarios.php");
        exit;
    }

    // Validar tipo de usuario
    $tipos_validos = ['estudiante', 'docente', 'administrador'];
    if (!in_array($tipo_usuario, $tipos_validos)) {
        $_SESSION['error'] = "Tipo de usuario inválido.";
        header("Location: admin_usuarios.php");
        exit;
    }

    // Verificar si el correo electrónico ya está en uso por otro usuario
    $sql = "SELECT id_usuario FROM usuarios WHERE email = ? AND id_usuario != ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $email, $id_usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $_SESSION['error'] = "El correo electrónico ya está en uso por otro usuario.";
        header("Location: admin_usuarios.php");
        exit;
    }

    $stmt->close();

    // Actualizar la información del usuario en la base de datos
    $sql_update = "UPDATE usuarios SET nombre_usuario = ?, email = ?, tipo_usuario = ? WHERE id_usuario = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("sssi", $nombre_usuario, $email, $tipo_usuario, $id_usuario);

    if ($stmt_update->execute()) {
        $stmt_update->close();
        $conn->close();
        $_SESSION['message'] = "Usuario actualizado con éxito.";
        header("Location: admin_usuarios.php");
        exit;
    } else {
        $stmt_update->close();
        $conn->close();
        $_SESSION['error'] = "Error al actualizar el usuario. Por favor, intenta de nuevo.";
        header("Location: admin_usuarios.php");
        exit;
    }
} else {
    // Si no se envió el formulario, redirigir a la gestión de usuarios
    header("Location: admin_usuarios.php");
    exit;
}
?>
