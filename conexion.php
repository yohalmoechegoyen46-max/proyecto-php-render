<?php
// conexion.php

function obtenerConexion() {
    // Lee las variables de entorno de forma segura
    $host = getenv('db.fswzxwkfcaflojleqvfn.supabase.co');
    $db   = getenv('proyecto-php-render');
    $user = getenv('postgres');
    $pass = getenv('SUPABASE_PASSWORD');
    $port = "5432"; // Puerto estándar de PostgreSQL

    try {
        $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
        // Inicializa la conexión PDO
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        return $pdo;
    } catch (PDOException $e) {
        // Si hay error de conexión, detiene todo y responde en JSON
        header("Content-Type: application/json");
        echo json_encode([
            "status" => "error", 
            "message" => "Fallo de conexión a la BD: " . $e->getMessage()
        ]);
        exit;
    }
}
?>