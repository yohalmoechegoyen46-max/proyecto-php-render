<?php
// conexion.php

function obtenerConexion() {
    // 1. Intentar leer la URI directa inyectada por Render
    $db_uri = getenv('SUPABASE_DATABASE_URL');

    if (!$db_uri) {
        // Plan B Local: Por si haces pruebas en tu computadora
        // Reemplaza "TU_CONTRASEÑA" por la contraseña real de tu base de datos
        $db_uri = "postgresql://postgres:TU_CONTRASEÑA_REAL@aws-0-us-west-1.pooler.supabase.com:6543/postgres";
    }

    try {
        // Formateamos el Data Source Name (DSN) usando la URL directa
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



            "message" => "Fallo crítico de conexión a la Base de Datos: " . $e->getMessage()
        ]);
        exit;
    }
}
?>