<?php
// Una vez cargado el expediente procedo a actualizarlo y si existe en la base de datos le envio la actualizacion

// Start en function lastMove
// Valido que el expediente exista en la tabla expedientes
// Si existe llamo a la function lastMoveUserExpediente y obtengo los movimientos del expediente
// Actualizo con function upExpAndMove tanto el expediente como los movimientos en las tablas asociadas al usuario



class last_move
{
  private $conexion;
  private $id_user;
  private $caseNumber;
  private $caseYear;
  private $dispatch;
  private $lastMove;

  public function __construct($conexion, $id_user, $caseNumber, $caseYear, $dispatch)
  {
    $this->conexion = $conexion;
    $this->id_user = $id_user;
    $this->caseNumber = $caseNumber;
    $this->caseYear = $caseYear;
    $this->dispatch = $dispatch;
  }

  public function lastMove()
  {
    try {
      // consulto en la tabla expedientes si el expediente ya existe
      $query = $this->conexion->prepare('SELECT * FROM expedientes WHERE numero_expediente = :numero_expediente AND anio_expediente = :anio_expediente AND dependencia = :dependencia');
      $query->execute([':numero_expediente' => $this->caseNumber, ':anio_expediente' => $this->caseYear, ':dependencia' => $this->dispatch]);
      $count = $query->rowCount();
      // si el expediente no existe en la tabla expedientes
      if ($count == 0) {
        // Devolver mensaje de "error" en json
        http_response_code(200);
        echo json_encode('El expediente aun no existe, no se envia correo con la ultima actualizacion');
        return;
      }
      // si el expediente existe en la tabla expedientes obtengo el id_expediente y se lo envio a la funcion lastMoveUserExpediente
      $expediente = $query->fetch();
      $id_expediente = $expediente['id_expediente'];
      $lastMoveUserExp = $this->lastMoveUserExpediente($id_expediente);
      // limpio el valor $lastMoveUserExp['texto'] y le quito &amp;nbsp;
      $lastMoveUserExp['texto'] = str_replace('&amp;nbsp;', '', $lastMoveUserExp['texto']);

      // valido que sea un obj
      if (is_object($lastMoveUserExp)) {
        // Devolver mensaje de "error" en json
        http_response_code(404);
        echo json_encode('el ultimo movimiento no se pudo consultar');
        return;
      }
    } catch (\Throwable $th) {
      // Devolver mensaje de error en json
      http_response_code(500);
      echo json_encode('Error al consultar la ultima actualizacion del expediente');
    }

    // envio un correo con el ultimo movimiento del expediente recien cargado y actualizado
    $this->sendMailLastMove($expediente, $lastMoveUserExp);


    // upExpAndMove
    $this->upExpAndMove($expediente);
  }

  public function lastMoveUserExpediente($id_expediente)
  {
    try {
      // consulto en la tabla movimientos por todos los movimientos del expediente
      $query = $this->conexion->prepare('SELECT * FROM movimientos WHERE id_expediente = :id_expediente ORDER BY fecha_movimiento DESC');
      $query->execute([':id_expediente' => $id_expediente]);
      $lastMove = $query->fetch();

      // usando el campo fecha_movimiento obtengo el ultimo movimiento del expediente
      $this->lastMove = $lastMove['fecha_movimiento'];
      // si el expediente no tiene movimientos
      if ($this->lastMove == null) {
        // Devolver mensaje de "error" en json
        http_response_code(200);
        echo json_encode('El expediente aun no tiene movimientos, no se envia correo con la ultima actualizacion');
        return;
      }

      // retorno todos los datos del ultimo movimiento
      return $lastMove;
    } catch (\Throwable $th) {
      // Devolver mensaje de error en json
      http_response_code(500);
      echo json_encode('Error al consultar la ultima actualizacion de los movimientos para el expediente');
    }
  }

  public function upExpAndMove($expediente)
  {
    // echo "expediente ->";
    try {
      // obtengo el id_exp de la tabla user_expedientes que tenga el $id_user, $caseNumber, $caseYear, $dispatch
      $query = $this->conexion->prepare('SELECT * FROM user_expedients WHERE id_user = :id_user AND numero_exp = :numero_exp AND anio_exp = :anio_exp AND dependencia = :dependencia');
      $query->execute([':id_user' => $this->id_user, ':numero_exp' => $expediente['numero_expediente'], ':anio_exp' => $expediente['anio_expediente'], ':dependencia' => $expediente['dependencia']]);
      $exp = $query->fetch();
    } catch (\Throwable $th) {
      // Devolver mensaje de error en json
      http_response_code(500);
      echo json_encode('Error al consultar el expediente del usuario');
    }

    try {
      //actualizo el expediente en la tabla user_expedientes id_exp	id_lista_despacho	numero_exp	anio_exp	caratula	reservado	dependencia	tipo_lista id_user, con la informacion de $expediente
      $query = $this->conexion->prepare('UPDATE user_expedients SET id_lista_despacho = :id_lista_despacho, caratula = :caratula, reservado = :reservado, tipo_lista = :tipo_lista WHERE id_exp = :id_exp');
      $query->execute([':id_lista_despacho' => $expediente['id_lista_despacho'], ':caratula' => $expediente['caratula'], ':reservado' => $expediente['reservado'], ':tipo_lista' => $expediente['tipo_lista'], ':id_exp' => $exp['id_exp']]);
    } catch (\Throwable $th) {
      // Devolver mensaje de error en json
      http_response_code(500);
      echo json_encode('Error al actualizar el expediente del usuario');
    }

    try {
      // echo json_encode($exp);

      // con el id_expediente obtengo todos los movimientos del expediente y los actualizo en la tabla user_movimientos
      $query = $this->conexion->prepare('SELECT * FROM movimientos WHERE id_expediente = :id_expediente');
      $query->execute([':id_expediente' => $expediente['id_expediente']]);
      $movements = $query->fetchAll();

      // echo json_encode($exp);
      foreach ($movements as $movement) {
        try {
          $query = $this->conexion->prepare('INSERT INTO user_exp_move (id_exp, fecha_movimiento, estado, texto, titulo, despacho) VALUES (:id_exp, :fecha_movimiento, :estado, :texto, :titulo, :despacho)');
          $query->execute([
            ':id_exp' => $exp['id_exp'], // Asegúrate de usar el parámetro correcto
            ':fecha_movimiento' => $movement['fecha_movimiento'], // Añadir los dos puntos aquí
            ':estado' => $movement['estado'],
            ':texto' => str_replace('&amp;nbsp;', '', $movement['texto']),
            ':titulo' => $movement['titulo'],
            ':despacho' => $movement['despacho']
          ]);
        } catch (\Throwable $th) {
          echo json_encode($th);
        }
      }
    } catch (\Throwable $th) {
      //throw $th;
    }
  }

  public function sendMailLastMove($expedient, $lastMovement)
  {
    // obtengo la informacion del usuario usando user/user_get.php
    require_once __DIR__ . '/../user/user_get.php';
    $userGet = new user_get($this->conexion, $this->id_user);
    $user = $userGet->getUsers();
    // Verifica que $user no sea null
    if ($user === null) {
      echo "Error: el usuario no fue encontrado.";
      return;
    }

    // echo json_encode($user);

    // Verifica que los índices existan en el array $user
    if (!isset($user['firstName']) || !isset($user['lastName']) || !isset($user['email'])) {
      echo "Error: faltan datos del usuario.";
      return;
    }

    // Imprime el contenido de $expedient y $lastMovement para depuración
    // echo json_encode($expedient);
    // echo json_encode($lastMovement);

    // Asegúrate de que los índices en $expedient y $lastMovement existan
    if (
      !isset($expedient['numero_expediente']) || !isset($expedient['anio_expediente']) || !isset($expedient['caratula']) || !isset($expedient['reservado']) || !isset($expedient['dependencia']) || !isset($expedient['tipo_lista']) ||
      !isset($lastMovement['fecha_movimiento']) || !isset($lastMovement['estado']) || !isset($lastMovement['texto']) || !isset($lastMovement['titulo']) || !isset($lastMovement['despacho'])
    ) {
      echo "Error: faltan datos del expediente o del último movimiento.";
      return;
    }

    // $newsBy es un array con id_user, name, email, un array de expedients que tiene un array de movimientos
    $newsBy = [
      [
        'id_user' => $this->id_user,
        'name' => $user['firstName'] . ' ' . $user['lastName'],
        'email' => $user['email'],
        'expedients' => [
          [
            'id_exp' => $expedient['id_expediente'], // Asegúrate de tener este valor en $expedient
            'numero_exp' => $expedient['numero_expediente'],
            'anio_exp' => $expedient['anio_expediente'],
            'caratula' => $expedient['caratula'],
            'reservado' => $expedient['reservado'],
            'dependencia' => $expedient['dependencia'],
            'tipo_lista' => $expedient['tipo_lista'],
            'movimientos' => [
              [
                'id_movimiento' => $lastMovement['id_movimiento'], // Asegúrate de tener este valor en $lastMovement
                'id_expediente' => $lastMovement['id_expediente'], // Asegúrate de tener este valor en $lastMovement
                'fecha_movimiento' => $lastMovement['fecha_movimiento'],
                'estado' => $lastMovement['estado'],
                'texto' => $lastMovement['texto'],
                'titulo' => $lastMovement['titulo'],
                'despacho' => $lastMovement['despacho']
              ]
            ]
          ]
        ]
      ]
    ];

    // envio el la informacion al correo
    require_once __DIR__ . '/../scrapper/write_mail.php';
    $writeMail = new write_mail($this->conexion, $newsBy);
    $writeMail->write();
  }
}
