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
    return json_encode(['message' => 'Unauthorized - no se envio encabezado con token']);
  }

  return true;
}
