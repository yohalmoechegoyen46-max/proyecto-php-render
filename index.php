<?php
// index.php

// Cabeceras CORS obligatorias para la arquitectura desacoplada o peticiones AJAX
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Content-Type");

// Detectamos el método HTTP (GET para ver la página, POST para guardar datos)
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    // Si entran directo desde el navegador, les entregamos el formulario visual en modo oscuro
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
            h2 { text-align: center; color: #a855f7; margin-bottom: 20px; font-size: 1.4rem; }
            .form-group { margin-bottom: 15px; }
            label { display: block; margin-bottom: 5px; font-size: 0.9rem; color: #aaaaaa; }
            input { width: 100%; padding: 10px; box-sizing: border-box; background-color: #2a2a2a; border: 1px solid #444444; border-radius: 4px; color: #ffffff; font-size: 1rem; }
            input:focus { border-color: #a855f7; outline: none; }
            button { width: 100%; padding: 12px; background-color: #a855f7; border: none; border-radius: 4px; color: #ffffff; font-weight: bold; cursor: pointer; font-size: 1rem; transition: background 0.3s; }
            button:hover { background-color: #9333ea; }
            #alert { margin-top: 15px; padding: 10px; border-radius: 4px; display: none; text-align: center; font-size: 0.9rem; font-weight: bold; }
            .success { background-color: #1b5e20; color: #b9f6ca; border: 1px solid #2e7d32; }
            .error { background-color: #b71c1c; color: #ff9e9e; border: 1px solid #c62828; }
        </style>
    </head>
    <body>

    <div class="container">
        <h2>Registro del Sistema (CI/CD)</h2>
        <form id="userForm">
            <div class="form-group">
                <label for="nombre">Nombre Completo:</label>
                <input type="text" id="nombre" required placeholder="Ej. Rodrigo Echegoyen">
            </div>
            <div class="form-group">
                <label for="correo">Correo Electrónico:</label>
                <input type="email" id="correo" required placeholder="ejemplo@gmail.com">
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

            alertDiv.style.display = 'none';

            try {
                // Envía la petición POST al mismo archivo index.php de forma dinámica
                const response = await fetch(window.location.origin + window.location.pathname, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nombre, correo })
                });

                const result = await response.json();
                alertDiv.style.display = 'block';

                if (result.status === 'success') {
                    alertDiv.className = 'alert success';
                    alertDiv.innerText = result.message;
                    document.getElementById('userForm').reset();
                } else {
                    alertDiv.className = 'alert error';
                    alertDiv.innerText = result.message;
                }
            } catch (error) {
                alertDiv.style.display = 'block';
                alertDiv.className = 'alert error';
                alertDiv.innerText = 'Hubo un problema de comunicación con el servidor.';
            }
        });
    </script>
    </body>
    </html>
    <?php
    exit; // Detiene la ejecución para que no interfiera con la respuesta JSON de abajo
}

// =========================================================================
// 2. PROCESAR LA PETICIÓN POST (INSERCIÓN DE DATOS Y AUTO-CREACIÓN DE TABLA)
// =========================================================================
header("Content-Type: application/json");
require_once 'conexion.php';

$input = json_decode(file_get_contents('php://input'), true);

if (isset($input['nombre']) && isset($input['correo'])) {
    try {
        $pdo = obtenerConexion();
        
        // BLOQUE ESTRATÉGICO PARA PLAN GRATUITO: Crea la tabla automáticamente si no existe en Render
        $tablaSQL = "CREATE TABLE IF NOT EXISTS usuarios (
            id SERIAL PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            correo VARCHAR(150) NOT NULL UNIQUE
        );";
        $pdo->exec($tablaSQL);

        // Preparamos la consulta limpia para evitar inyecciones SQL
        $sql = "INSERT INTO usuarios (nombre, correo) VALUES (:nombre, :correo)";
        $stmt = $pdo->prepare($sql);
        
        $stmt->execute([
            ':nombre' => htmlspecialchars($input['nombre']),
            ':correo' => htmlspecialchars($input['correo'])
        ]);

        echo json_encode([
            "status" => "success", 
            "message" => "¡Registro almacenado con éxito en la BD interna de Render!"
        ]);

    } catch (PDOException $e) {
        echo json_encode([
            "status" => "error", 
            "message" => "Error al ejecutar la operación en la Base de Datos: " . $e->getMessage()
        ]);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Datos de formulario incompletos."
    ]);
}
?>