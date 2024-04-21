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
      $query = $this->conexion->prepare('INSERT INTO mercado_pago (user_id, preapproval_id, collector_id, application_id, reason, status, date_created, last_modified, init_point, frequency, frequency_type, transaction_amount, currency_id, repetitions, billing_day, billing_day_proportional) VALUES (:user_id, :preapproval_id, :collector_id, :application_id, :reason, :status, :date_created, :last_modified, :init_point, :frequency, :frequency_type, :transaction_amount, :currency_id, :repetitions, :billing_day, :billing_day_proportional)');
      $query->execute([
        ':user_id' => $this->conexion->lastInsertId(),
        ':preapproval_id' => $this->mp_data['id'],
        ':collector_id' => $this->mp_data['collector_id'],
        ':application_id' => $this->mp_data['application_id'],
        ':reason' => $this->mp_data['reason'],
        ':status' => $this->mp_data['status'],
        ':date_created' => $this->mp_data['date_created'],
        ':last_modified' => $this->mp_data['last_modified'],
        ':init_point' => $this->mp_data['init_point'],
        ':frequency' => $this->mp_data['auto_recurring']['frequency'],
        ':frequency_type' => $this->mp_data['auto_recurring']['frequency_type'],
        ':transaction_amount' => $this->mp_data['auto_recurring']['transaction_amount'],
        ':currency_id' => $this->mp_data['auto_recurring']['currency_id'],
        ':repetitions' => $this->mp_data['auto_recurring']['repetitions'],
        ':billing_day' => $this->mp_data['auto_recurring']['billing_day'],
        ':billing_day_proportional' => $this->mp_data['auto_recurring']['billing_day_proportional'],
      ]);
    } catch (\Throwable $th) {
      // Devuelve mensaje de error en JSON
      echo json_encode([
        'status' => 500,
        'message' => 'Error al asociar el plan de suscripción al usuario: ' . $th->getMessage()
      ]);
      return;
    }

    //devolver un mensaje de éxito en json con mp_data['init_point']
    echo json_encode([
      'status' => 200,
      'message' => trim($this->mp_data['init_point'])
    ]);
  }
}
