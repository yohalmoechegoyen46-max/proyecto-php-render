<?php
// conexion.php

function obtenerConexion() {
    $host = getenv('SUPABASE_HOST');
    $db   = getenv('SUPABASE_DB');
    $user = getenv('SUPABASE_USER');
    $pass = getenv('SUPABASE_PASSWORD');
    $port = "6543"; // Puerto predeterminado de PostgreSQL

    try {


    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
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