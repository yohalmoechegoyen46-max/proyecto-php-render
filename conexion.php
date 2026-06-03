<?php
// conexion.php

function obtenerConexion() {
    // Leer las variables inyectadas de forma individual desde Render
    $host = getenv('SUPABASE_HOST');
    $db   = getenv('SUPABASE_DB');
    $user = getenv('SUPABASE_USER');
    $pass = getenv('SUPABASE_PASSWORD');
    $port = getenv('SUPABASE_PORT');

    // Valores por defecto para pruebas locales (en tu computadora)
    if (!$host) {
        $host = 'aws-0-us-west-1.pooler.supabase.com'; 
        $port = '6543';
        $db   = 'postgres';
        $user = 'postgres.ojdtsvjavxyiwagomvih'; // Tu usuario con el ID del proyecto
        $pass = 'TU_CONTRASEÑA_REAL'; 
    }

    try {
        // Construimos el Data Source Name (DSN) estándar de PostgreSQL
        $dsn = "pgsql:host=$host;port=$port;dbname=$db;sslmode=require";
        
        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        
        return $pdo;
    } catch (PDOException $e) {
        header("Content-Type: application/json");
        echo json_encode([
            "status" => "error", 
            "message" => "Fallo crítico de conexión a la Base de Datos: " . $e->getMessage()
        ]);
        exit;
    }
}
?>