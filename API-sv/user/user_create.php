<?php
class user_create
{
  private $firstName;
  private $lastName;
  private $email;
  private $password;
  private $mp_data;
  private $conexion;

  public function __construct($conexion, $firstName, $lastName, $email, $password, $mp_data)
  {
    $this->conexion = $conexion;
    $this->firstName = $firstName;
    $this->lastName = $lastName;
    $this->email = $email;
    $this->password = $password;
    $this->mp_data = $mp_data;
  }

  public function createUser()
  {
    // Verificar si el correo electrónico ya existe en la base de datos
    $query = $this->conexion->prepare('SELECT email FROM users WHERE email = :email');
    $query->execute([':email' => $this->email]);
    $existingUser = $query->fetch(PDO::FETCH_ASSOC);

    if ($existingUser) {
      //devuelve mensaje de error en json
      echo json_encode([
        'status' => 400,
        'message' => 'El correo electrónico ya existe'
      ]);
      return;
    }
    // Generar el hash de la contraseña
    $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);

    // Guardar el nuevo usuario en la db con los datos recibidos del formulario
    $query = $this->conexion->prepare('INSERT INTO users (firstName, lastName, email, password, id_subscription) VALUES (:firstName, :lastName, :email, :password, :id_subscription)');
    $query->execute([
      ':firstName' => $this->firstName,
      ':lastName' => $this->lastName,
      ':email' => $this->email,
      ':password' => $hashedPassword,
      ':id_subscription' => 2
    ]);

    // en la tabla mercado_pago asociando con el usuario guardo el id_subscription en id_subscription
    try {
      $query = $this->conexion->prepare('INSERT INTO mercado_pago (user_id, id_subscription, init_point, date_created, last_modified, status) VALUES (:user_id, :id_subscription, :init_point, :date_created, :last_modified, :status)');
      $query->execute([
        ':user_id' => $this->conexion->lastInsertId(),
        ':id_subscription' => $this->mp_data['id'],
        ':init_point' => $this->mp_data['init_point'],
        ':date_created' => $this->mp_data['date_created'],
        ':last_modified' => $this->mp_data['last_modified'],
        ':status' => $this->mp_data['status']
      ]);
    } catch (\Throwable $th) {
      //devuelve mensaje de error en json
      echo json_encode([
        'status' => 500,
        'message' => 'Error al asociar el plan de suscripción al usuario: ' . $th->getMessage()
      ]);
      return;
    }

    //devolver un mensaje de éxito en json
    echo json_encode([
      'status' => 200,
      'message' => 'Usuario creado correctamente'
    ]);
    error_log('Mensaje de depuración: Algo sucede aquí', 0);
  }
}
