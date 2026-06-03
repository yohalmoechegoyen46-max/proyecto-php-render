<?php
// conexion.php

function obtenerConexion() {
    // Render inyecta esta variable automáticamente al hacer el "Linked Database"
    $db_uri = getenv('DATABASE_URL');

    if (!$db_uri) {
        // Plan B Local: Por si realizas pruebas directamente en tu computadora (XAMPP/Docker)
        $db_uri = "postgresql://postgres:postgres@localhost:5432/postgres";
    }

    try {
        // El controlador 'pdo_pgsql' requiere el prefijo 'pgsql:' antes de la cadena URI completa
        $dsn = "pgsql:" . $db_uri;
        
        $pdo = new PDO($dsn, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        return $pdo;
    } catch (PDOException $e) {
        header("Content-Type: application/json");
        echo json_encode([
            "status" => "error", 
            "message" => "Fallo crítico al conectar con la base de datos interna de Render: " . $e->getMessage()
        ]);
        exit;
    }
}
?>