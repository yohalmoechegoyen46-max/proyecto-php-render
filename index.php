<?php
// index.php

// Cabeceras CORS obligatorias para la arquitectura desacoplada
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Importamos el módulo de conexión de forma ordenada
require_once 'conexion.php';

// Capturamos la petición HTTP POST asíncrona
$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['nombre']) && isset($input['correo'])) {
    try {
        // Obtenemos la conexión PDO activa
        $pdo = obtenerConexion();
        
        // Preparamos la consulta SQL estructurada para prevenir Inyecciones SQL
        $sql = "INSERT INTO usuarios (nombre, correo) VALUES (:nombre, :correo)";
        $stmt = $pdo->prepare($sql);
        
        // Ejecutamos pasando los parámetros limpios
        $stmt->execute([
            ':nombre' => htmlspecialchars($input['nombre']),
            ':correo' => htmlspecialchars($input['correo'])
        ]);

        echo json_encode([
            "status" => "success", 
            "message" => "¡Datos guardados con éxito y de forma ordenada en Supabase!"
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error", 
            "message" => "Error al insertar en la tabla: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Datos de formulario incompletos."
    ]);
}
?>