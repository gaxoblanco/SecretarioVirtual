<?php
// Este archivo se encarga de crear un nuevo secretario en la base de datos.
// Recibe los datos del secretario a crear por medio de un formulario en la página user_create_secretary.html
// y los inserta en la base de datos.

require_once './subscription.php';

class user_create_secretary
{
  private $id_user;
  private $firstName;
  private $Semail;
  private $Spass;

  private $conexion;

  public function __construct($conexion, $id_user, $firstName, $Semail, $Spass)
  {
    $this->conexion = $conexion;
    $this->id_user = $id_user;
    $this->firstName = $firstName;
    $this->Semail = $Semail;
    $this->Spass = $Spass;
  }

  public function createSecretary()
  {
    //Obtengo la informacion del tipo de suscripcion del usuario
    $subscription = new subscription($this->conexion, $this->id_user);
    $subscriptionInfo = $subscription->subscriptionUser();

    // creo la variable Nsecretaries que es el numero de secretarios que puede tener el usuario
    $Nsec = $subscriptionInfo['num_secretary'];
    // echo json_encode($Nsec);

    //Limitacion del numero de secretarios
    try {
      // verifica cuantos secretarios ya tiene el usuario
      $query = $this->conexion->prepare('SELECT COUNT(*) FROM secretaries WHERE id_users = :id_user');
      $query->execute([':id_user' => $this->id_user]);
      $secretariesCount = $query->fetchColumn();
    } catch (PDOException $e) {
      // Devolver mensaje de error en json
      echo json_encode([
        'status' => 500,
        'message' => 'Error al consultar el numero de secretarios para crear uno nuevo'
      ]);
    }

    if ($secretariesCount >= $Nsec) {
      // Devolver mensaje de error en json si ya tiene el maximo de secretarios
      echo json_encode([
        'status' => 400,
        'message' => 'El usuario ya tiene el maximo de secretarios. No se puede agregar más.'
      ]);
      return;
    }

    try {
      // Verificar si el correo electrónico ya está registrado en algun secreatario
      $query = $this->conexion->prepare('SELECT COUNT(*) FROM secretaries WHERE Semail = :Semail');
      $query->execute([':Semail' => $this->Semail]);
      $count = $query->fetchColumn();

      if ($count > 0) {
        // Devolver mensaje de error en json
        echo json_encode([
          'status' => 400,
          'message' => 'El correo electrónico ya existe'
        ]);
        return;
      }
      // Generar el hash de la contraseña
      $hashedSpass = password_hash($this->Spass, PASSWORD_DEFAULT);


      // Insertar el nuevo secretario en la tabla secretarylist
      $query = $this->conexion->prepare('INSERT INTO secretaries (firstName, Semail, Spass, id_users)
                            VALUES (:firstName, :Semail, :Spass, :id_user)');
      $query->execute([
        ':firstName' => $this->firstName,
        ':Semail' => $this->Semail,
        ':Spass' => $hashedSpass,
        ':id_user' => $this->id_user
      ]);

      //devuelve mensaje de éxito en json
      echo json_encode([
        'status' => 200,
        'message' => 'Secretario creado correctamente'
      ]);
    } catch (PDOException $e) {
      // Devolver mensaje de error en json
      echo json_encode([
        'status' => 500,
        'message' => 'Error al crear el secretario: ' . $e->getMessage()
      ]);
    }
  }
}
