<?php
// index.php

// Cabeceras CORS obligatorias para la arquitectura desacoplada
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// 1. DETECTAR EL TIPO DE PETICIÓN (GET o POST)
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    // Si entran directo desde el navegador, les entregamos el formulario visual de inmediato
    header("Content-Type: text/html; charset=UTF-8");
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Registro del Sistema - ITCA</title>
        <style>
            body { background-color: #121212; color: #ffffff; font-family: 'Verdana', sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
            .container { background-color: #1e1e1e; padding: 30px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); width: 100%; max-width: 400px; }
            h2 { text-align: center; color: #00ff88; margin-bottom: 20px; font-size: 1.2rem; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; font-size: 0.9rem; color: #aaaaaa; }
            input { width: 100%; padding: 10px; box-sizing: border-box; background-color: #2a2a2a; border: 1px solid #444444; border-radius: 4px; color: #ffffff; }
            button { width: 100%; padding: 12px; background-color: #00ff88; border: none; border-radius: 4px; color: #121212; font-weight: bold; cursor: pointer; font-size: 1rem; }
            button:hover { background-color: #00cc6e; }
            #alert { margin-top: 15px; padding: 10px; border-radius: 4px; display: none; text-align: center; font-size: 0.9rem; }
            .success { background-color: #1b5e20; color: #b9f6ca; }
            .error { background-color: #b71c1c; color: #ff9e9e; }
        </style>
    </head>
    <body>

    <div class="container">
        <h2>Registro del Sistema (CI/CD)</h2>
        <form id="userForm">
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" required placeholder="Ej. Rodrigo Ramos">
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" required placeholder="ejemplo@correo.com">
            </div>
            <button type="submit">Enviar Registro a Render</button>
        </form>
        <div id="alert"></div>
    </div>

    <script>
        document.getElementById('userForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const nombre = document.getElementById('nombre').value;
            const correo = document.getElementById('correo').value;
            const alertDiv = document.getElementById('alert');

            // El script se envía de forma asíncrona a este mismo archivo index.php por POST
            const URL_API = 'window.location.href'; 

            try {
                const response = await fetch(window.location.origin + window.location.pathname, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nombre, correo })
                });

                const result = await response.json();
                alertDiv.style.display = 'block';

                if (result.status === 'success') {
                    alertDiv.className = 'success';
                    alertDiv.innerText = result.message;
                    document.getElementById('userForm').reset();
                } else {
                    alertDiv.className = 'error';
                    alertDiv.innerText = result.message;
                }
            } catch (error) {
                alertDiv.style.display = 'block';
                alertDiv.className = 'error';
                alertDiv.innerText = 'Hubo un problema en la respuesta del servidor.';
            }
        });
    </script>
    </body>
    </html>
    <?php
    exit; // Detiene la ejecución aquí para que no mande el JSON abajo
}

// 2. PROCESAR LA PETICIÓN POST (INSERCIÓN DE DATOS)
header("Content-Type: application/json");
require_once 'conexion.php';

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['nombre']) && isset($input['correo'])) {
    try {
        $pdo = obtenerConexion();
        
        $sql = "INSERT INTO usuarios (nombre, correo) VALUES (:nombre, :correo)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':nombre' => htmlspecialchars($input['nombre']),
            ':correo' => htmlspecialchars($input['correo'])
        ]);

        echo json_encode([
            "status" => "success", 
            "message" => "¡Registro almacenado con éxito en Supabase!"
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error", 
            "message" => "Error al ejecutar la inserción: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Datos de formulario incompletos."
    ]);
} // <-- Aquí cerramos la llave del else correctamente
?>