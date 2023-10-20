<?php
// Función para verificar el token
function verifyToken($conexion, $token)
{
    $stmt = $conexion->prepare('SELECT * FROM users WHERE token = :token');
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        // El token no coincide con ningún usuario
        return false;
    }

    return true;
}

// Verificar si se envió un token válido en el encabezado
function checkToken($conexion)
{
    $headers = getallheaders();

    if (isset($headers['token'])) {
        $token = $headers['token'];
        if (verifyToken($conexion, $token)) {
            return $token;
        }
    }

    return null;
}

// Configurar la conexión a la base de datos (ya está configurada en 'config.php')
// Asegúrate de que $conexion esté disponible desde 'config.php'
// Ejemplo: $conexion = new PDO($dsn, $usuario, $contrasena);

$token = checkToken($conexion);

if (!$token) {
    // No se envió un token válido en el encabezado
    http_response_code(401);
    echo json_encode(['message' => 'Unauthorized']);
    exit();
}

// Token válido, continuar con la ejecución normal del script
