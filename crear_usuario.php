<?php
include('conexion.php');
session_start();

// Verificar si el usuario está logueado y es administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] != 'administrador') {
    header("Location: login.php");
    exit;
}

// Verificar si se ha enviado el formulario de creación de usuario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = trim($_POST['nombre_usuario']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $tipo_usuario = trim($_POST['tipo_usuario']);

    // Validar datos de entrada
    if (empty($nombre_usuario) || empty($email) || empty($password) || empty($tipo_usuario)) {
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

    // Verificar si el correo electrónico ya está en uso
    $sql = "SELECT id_usuario FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        $_SESSION['error'] = "El correo electrónico ya está en uso.";
        header("Location: admin_usuarios.php");
        exit;
    }

    $stmt->close();

    // Encriptar la contraseña
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Insertar el nuevo usuario en la base de datos
    $sql_insert = "INSERT INTO usuarios (nombre_usuario, email, password, tipo_usuario) VALUES (?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("ssss", $nombre_usuario, $email, $password_hash, $tipo_usuario);

    if ($stmt_insert->execute()) {
        $stmt_insert->close();
        $conn->close();
        $_SESSION['message'] = "Usuario creado con éxito.";
        header("Location: admin_usuarios.php");
        exit;
    } else {
        $stmt_insert->close();
        $conn->close();
        $_SESSION['error'] = "Error al crear el usuario. Por favor, intenta de nuevo.";
        header("Location: admin_usuarios.php");
        exit;
    }
} else {
    // Si no se envió el formulario, redirigir a la gestión de usuarios
    header("Location: admin_usuarios.php");
    exit;
}
?>
