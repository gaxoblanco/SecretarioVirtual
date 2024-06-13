<?php
// Función para verificar el token
function verifyToken($conexion, $token, $userId)
{
  $query = $conexion->prepare("SELECT * FROM users WHERE id = :id AND token = :token");
  $query->bindParam(':id', $userId);
  $query->bindParam(':token', $token);
  $query->execute();
  $user = $query->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    // El token no coincide con ningún usuario
    return json_encode(['message' => 'Unauthorized - no se envio encabezado con token']);
  }

  return true;
}
