<?php
function verifyToken($conexion, $token, $userId)
{
  $query = $conexion->prepare("SELECT * FROM users WHERE id_user = :id AND token = :token");
  $query->bindParam(':id', $userId);
  $query->bindParam(':token', $token);
  $query->execute();
  $user = $query->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    // El token no coincide con ningún usuario
    http_response_code(401);  // No autorizado
    header('Content-Type: application/json');
    echo json_encode(['message' => 'Unauthorized - no se envio encabezado con token']);
    exit;
  }

  return true;
}
