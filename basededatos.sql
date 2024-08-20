DROP DATABASE IF EXISTS educacion;
CREATE DATABASE IF NOT EXISTS educacion;

USE educacion;

CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('administrador', 'docente', 'estudiante') NOT NULL
);

INSERT INTO usuarios (nombre_usuario, email, password, tipo_usuario)
VALUES ('admin', 'admin@gmail.com', '$2y$10$Q8x8oR1AEej8/abTtyh3mOGX1JUmy/z.9PCBqJNcpf4EwgGRROooK', 'administrador');#1234


select * from usuarios;
CREATE TABLE cursos (
    id_curso INT AUTO_INCREMENT PRIMARY KEY,
    nombre_curso VARCHAR(255) NOT NULL
);

CREATE TABLE usuarios_cursos (
    id_usuario_curso INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    id_curso INT NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario),
    FOREIGN KEY (id_curso) REFERENCES cursos(id_curso)
);

CREATE TABLE notas (
    id_nota INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario_curso INT NOT NULL,
    nota FLOAT NOT NULL,
    FOREIGN KEY (id_usuario_curso) REFERENCES usuarios_cursos(id_usuario_curso)
);
