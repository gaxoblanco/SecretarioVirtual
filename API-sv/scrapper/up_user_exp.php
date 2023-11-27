<?php
//  teniendo los datos de todos los usuarios  // es decir toda la db de los expedientes que desean los usuarios
//  tablas:
//  1- user_expedients: id_exp 	id_lista_despacho 	numero_exp 	id_user 	anio_exp 	caratula 	reservado 	dependencia 	tipo_lista
//  2- user_exp_move:  id_move 	fecha_movimiento 	estado 	titulo 	texto 	despacho 	id_exp
//  3- expedientes:  id_expediente 	id_lista_despacho 	numero_expediente 	anio_expediente 	caratula 	reservado 	dependencia 	tipo_lista
//  4- movimientos:  id_expediente 	id_lista_despacho 	numero_expediente 	anio_expediente

class up_user_exp
{
  private $conexion;
  private $users;
  private $newsBy;

  public function __construct($conexion, $users)
  {
    $this->conexion = $conexion;
    $this->users = $users;
    $this->newsBy = [];
  }

  // consulta en la tabla expedientes, por cada expediente que tenga el campo dependencia == null, por un expediente que tenga numero_expediente == numero_exp y anio_expediente == anio_exp
  // si no existe, vevuelve mensaje " expediente no encontrado"
  public function getExpedient()
  {
    //itera por el array users y por cada user itera por el array expedients
    foreach ($this->users as &$user) {
      foreach ($user['expedients'] as &$expedient) {

        // si dependencia == null
        if ($expedient['tipo_lista'] == null) {
          // echo "el expediente del usuario: " . $user['id_user'] . " no TENIA dependencia: " . $expedient['numero_exp'] . '/' . $expedient['anio_exp'] . " - id_exp " . $expedient['id_exp'] . " : ";

          // llamar a la funcion firstUp que recibe el numero_exp y anio_exp
          $ifExisted = $this->firstUp($expedient['numero_exp'], $expedient['anio_exp'], $user['id_user'], $expedient['id_exp'], $expedient['dependencia']);
          // echo "firstUp data: " . var_dump($ifExisted) . " : ";

          // Si $ifExisted devuelve datos los guardamos en el indice expdients del newsBy
          if ($ifExisted) {
            //---- array constructor
            // Verificamos si el usuario ya está en $this->newsBy
            $userIndex = $this->findUserIndex($user['id_user']);

            if ($userIndex === null) {
              $this->newsBy[] = [
                'id_user' => $user['id_user'], // Agregamos el id del usuario
                'name' => isset($user['name']) ? $user['name'] : '',
                'email' => isset($user['email']) ? $user['email'] : '',
                'expedients' => [] // Inicializamos el array de expedientes
              ];
              $userIndex = count($this->newsBy) - 1; // El índice del usuario que acabamos de agregar
            }
            // borro el "id_expediente" del newsBy->expedients
            unset($ifExisted[0]['id_expediente']);
            // inserta el id_exp = $expedient['id_exp']
            $ifExisted[0]['id_exp'] = $expedient['id_exp'];
            // cambio el nombre de numero_expediente por numero_exp y conservo el valor
            $ifExisted[0]['numero_exp'] = $ifExisted[0]['numero_expediente'];
            // cambio el nombre de anio_expediente por anio_exp
            $ifExisted[0]['anio_exp'] = $ifExisted[0]['anio_expediente'];
            $this->newsBy[$userIndex]['expedients'][] = $ifExisted[0];
          }
          //-----
        }

        // si existe (dependencia != null) - obtengo el id del expediente
        $idExpediente = $this->getIdExpediente($expedient['numero_exp'], $expedient['anio_exp'], $expedient['dependencia']);
        // echo "el id del expediente en la tabla expediente es: " . $idExpediente . " : ";

        // controlo que $idExpediente no sea null es decir existe en la tabla expedientes
        if (!$idExpediente) {
          echo "El expediente del usuario " . $user['id_user'] . " no se encontró en la tabla expedientes.";
        }

        // obtengo los movimientos del expediente
        $ExpMoving = $this->getMovimientos($idExpediente);
        // controlo que $ExpMoving no sea null es decir que tenemos al menos 1 movimiento
        if (!$ExpMoving) {
          echo "El expediente del usuario " . $user['id_user'] . " no tiene movimientos.";
        }
        // var_dump($ExpMoving);

        // guardo la lista de movimientos
        $userExpMoving = $this->getLastDateMovimientos($expedient['movimientos']);
        $expMoving = $this->getLastDateMovimientos($ExpMoving);

        //valido que expMoving no sea null
        if ($expMoving) {
          $fechaExpMoving = $expMoving[0]['fecha_movimiento'];
        } else {
          $fechaExpMoving = 0;
        }
        //valido que UserExpMoving no sea null
        if ($userExpMoving) {
          $fechaUserExpMoving = $userExpMoving[0]['fecha_movimiento'];
        } else {
          $fechaUserExpMoving = 0;
        }
        // echo "El contenido del array \$UserExpMoving es: <pre>" . print_r($fechaUserExpMoving, true) . "</pre>";
        // echo "El contenido del array \$expMoving es: <pre>" . print_r($fechaExpMoving, true) . "</pre>";

        // si la fecha de $expMoving es mayor a la fecha de $UserExpMoving, llamar a la funcion newMove()
        if ($fechaExpMoving > $fechaUserExpMoving) {
          $news = $this->newMove($expMoving, $expedient['id_exp']);

          //---- array constructor
          // Verificamos si el usuario ya está en $this->newsBy
          $userIndex = $this->findUserIndex($user['id_user']);
          if ($userIndex === null) {
            $this->newsBy[] = [
              'id_user' => $user['id_user'],
              'name' => isset($user['name']) ? $user['name'] : '',
              'email' => isset($user['email']) ? $user['email'] : '',
              'expedients' => [] // Inicializamos el array de expedientes
            ];
            $userIndex = count($this->newsBy) - 1; // El índice del usuario que acabamos de agregar
          }

          $expedientIndex = $this->findExpedientIndex($this->newsBy[$userIndex]['expedients'], $expedient['id_exp']);
          // verifico que el expediente no este en el array expedients del usuario si no esta cargo sus datos
          if ($expedientIndex === null) {
            $this->newsBy[$userIndex]['expedients'][] = [
              'id_exp' => $expedient['id_exp'],
              'numero_exp' => $expedient['numero_exp'],
              'anio_exp' => $expedient['anio_exp'],
              'caratula' => $expedient['caratula'],
              'reservado' => $expedient['reservado'],
              'dependencia' => $expedient['dependencia'],
              'tipo_lista' => $expedient['tipo_lista'],
              'movimientos' => [],
            ];
            // cargo el expediente en el indice del usuario
            $expedientIndex = count($this->newsBy[$userIndex]['expedients']) - 1;
          }

          //-----
          // Verificamos si el expediente ya está en $this->newsBy y obtenemos el indice
          $expedientIndex = $this->findExpedientIndex($this->newsBy[$userIndex]['expedients'], $expedient['id_exp']);

          // si $news contiene datos, los guardamos en el indice movimientos del array expdients dentro del $newBy
          if ($news) {
            if ($expedientIndex === null) {
              // Si el expediente no existe, agregamos todo el expediente con el nuevo movimiento
              $expedientData = $expedient; // Obtenemos los datos del expediente
              $newExpedient = [
                'id_exp' => $expedientData['id_exp'],
                'numero_exp' => $expedientData['numero_exp'],
                'anio_exp' => $expedientData['anio_exp'],
                'caratula' => $expedientData['caratula'],
                'reservado' => $expedientData['reservado'],
                'dependencia' => $expedientData['dependencia'],
                'tipo_lista' => $expedientData['tipo_lista'],
                'movimientos' => $news
              ];
              $this->newsBy[$userIndex]['expedients'][] = $newExpedient; // Agregamos el expediente al usuario
            } else {
              $this->newsBy[$userIndex]['expedients'][$expedientIndex]['movimientos'] = $news; // Actualizamos los movimientos del expediente
            }
          }
          //-----

          // return $this->users;
        } else {
          // si la fecha de $expMoving es menor a la fecha de $UserExpMoving envia mensaje "no hay nuevos movimientos"
          echo "no hay nuevos movimientos para el expediente";
          // return $this->users;
        }
      }
    }
    return $this->newsBy;
  }

  // crea la funcion firstUp() que recibe el numero_exp y anio_exp y en la tabla user_expedients busca el expendiente que coincida con numero_exp y anio_exp y llama a la funcion expedientUp que devuelve la informacion del expediente para actualizarlo en la tabla user_expedients
  public function firstUp($numero_exp, $anio_exp, $id_user, $id_exp, $dependencia)
  {
    // consulto en la tabla expedientes, por un expediente que tenga numero_expediente == numero_exp y anio_expediente == anio_exp
    $query = $this->conexion->prepare('SELECT * FROM expedientes WHERE numero_expediente = :numero_expediente AND anio_expediente = :anio_expediente AND dependencia = :dependencia');
    $query->execute([':numero_expediente' => $numero_exp, ':anio_expediente' => $anio_exp, ':dependencia' => $dependencia]);
    $expedient = $query->fetchAll(PDO::FETCH_ASSOC);

    // si no existe, devuelve mensaje " expediente no encontrado"
    if (!$expedient) {
      echo json_encode('expediente no encontrado');
      return;
    }

    // si existe (dependencia != null)
    // llamar a la funcion expedientUp que recibe el numero_exp, anio_exp, id_user y $expedient
    $this->expedientUp($id_exp, $id_user, $expedient);

    // si existe, devuelve los datos obtenidos
    // echo json_encode($expedient);
    return $expedient;
  }

  // crea la funcion expedientUp() que recibe $id_exp, $id_user, $expedient y actualiza la tabla users_expedients con los datos de $expedient
  public function expedientUp($id_exp, $id_user, $expedient)
  {
    // en la tabla user_expedients, actualiza el expediente que coincida con id_exp y id_user con los datos que corresponden a la tabla expedientes
    $query = $this->conexion->prepare('UPDATE user_expedients SET numero_exp = :numero_exp, anio_exp = :anio_exp, dependencia = :dependencia, id_lista_despacho = :id_lista_despacho, caratula = :caratula, tipo_lista = :tipo_lista,  reservado = :reservado, id_user = :id_user WHERE id_exp = :id_exp');

    $query->execute([
      ':numero_exp' => $expedient[0]['numero_expediente'],
      ':anio_exp' => $expedient[0]['anio_expediente'],
      ':dependencia' => $expedient[0]['dependencia'],
      ':id_lista_despacho' => $expedient[0]['id_lista_despacho'],
      ':caratula' => $expedient[0]['caratula'],
      ':reservado' => $expedient[0]['reservado'],
      ':tipo_lista' => $expedient[0]['tipo_lista'],
      ':id_user' => $id_user,
      ':id_exp' => $id_exp
    ]);

    // si se actualizo correctamente, devuelve mensaje "expediente actualizado"
    if ($query->rowCount()) {
      echo json_encode('expediente actualizado');
      return;
    } else {
      echo json_encode('expediente no actualizado');
      return;
    }
  }

  //obtengo el id_expediente de la tabla expedientes que coincida con numero_exp y anio_exp
  public function getIdExpediente($numero_exp, $anio_exp, $dependencia)
  {
    try {
      $query = $this->conexion->prepare('SELECT id_expediente FROM expedientes WHERE numero_expediente = :numero_exp AND anio_expediente = :anio_exp AND dependencia = :dependencia');
      $query->bindParam(':numero_exp', $numero_exp, PDO::PARAM_STR);
      $query->bindParam(':anio_exp', $anio_exp, PDO::PARAM_STR);
      $query->bindParam(':dependencia', $dependencia, PDO::PARAM_STR);
      $query->execute();

      $resultado = $query->fetch(PDO::FETCH_ASSOC);

      if ($resultado) {
        $id_expediente = $resultado['id_expediente'];
        return $id_expediente;
      } else {
        return null; // El expediente no se encontró en la base de datos
      }
    } catch (PDOException $e) {
      return null; // Error en la consulta
    }
  }

  //-- dependencia != null
  // Obtener los movimientos que concidan con el id_expediente en la tabla movimientos
  public function getMovimientos($id_expediente)
  {
    try {
      // Obtener los movimientos que tengan el id_expediente en la tabla movimientos
      $query = $this->conexion->prepare('SELECT * FROM movimientos WHERE id_expediente = :id_expediente');
      $query->execute([':id_expediente' => $id_expediente]);
      $movimientos = $query->fetchAll(PDO::FETCH_ASSOC);
      // si no existe, devuelve mensaje " expediente sin movimientos"
      if (!$movimientos) {
        echo json_encode('expediente sin movimientos');
        return;
      }

      // si existe, devuelve los datos obtenidos
      // echo "tenemos al menos 1 movimiento" . json_encode($movimientos);
      return $movimientos;
    } catch (PDOException $e) {
      return null; // Error en la consulta
    }
  }

  // funcion para compara las fechas de los movimientos y obtener la mas reciente
  public function getLastDateMovimientos($movimientos)
  {
    $lastDateMovimientos = array();

    // Verificar si $movimientos es NULL o está vacío
    if (!is_null($movimientos) && !empty($movimientos)) {
      $lastDateMovimientos[0] = $movimientos[0];

      for ($i = 1; $i < count($movimientos); $i++) {
        if ($movimientos[$i]['fecha_movimiento'] > $lastDateMovimientos[0]['fecha_movimiento']) {
          $lastDateMovimientos[0] = $movimientos[$i];
        }
      }
    } else {
      echo json_encode('expediente sin movimientos');
    }

    return $lastDateMovimientos;
  }

  // funcion newMove, actualiza el movimiento en la tabla user_exp_move asociandolo al $id_exp que devuelve getIdUserExpedient
  public function newMove($expMoving, $idExp)
  {

    $query = $this->conexion->prepare('INSERT INTO user_exp_move (id_exp, fecha_movimiento, estado, texto, titulo, despacho) VALUES (:id_exp, :fecha_movimiento, :estado, :texto, :titulo, :despacho)');

    $query->execute([
      ':fecha_movimiento' => $expMoving[0]['fecha_movimiento'],
      ':estado' => $expMoving[0]['estado'],
      ':texto' => $expMoving[0]['texto'],
      ':titulo' => $expMoving[0]['titulo'],
      ':despacho' => $expMoving[0]['despacho'],
      ':id_exp' => $idExp,
    ]);

    // retorno los datos que actualice a la tabla user_exp_move

    $query = $this->conexion->prepare('SELECT * FROM user_exp_move WHERE id_exp = :id_exp');
    $query->execute([':id_exp' => $idExp]);
    $expMoving = $query->fetchAll(PDO::FETCH_ASSOC);

    // si se ejecuta correctamente, devuelve mensaje "movimiento actualizado"
    echo json_encode('movimiento actualizado');
    return $expMoving;
  }

  //--- array constructor  ---
  // Función para buscar al usuario en $this->newsBy
  private function findUserIndex($userId)
  {
    // busco en el array $this->newsBy el indice del usuario que coincida con el id_user
    foreach ($this->newsBy as $index => $item) {
      if ($item['id_user'] == $userId) {
        return $index;
      }
    }
  }

  // Función para buscar el expediente en los expedientes del usuario
  private function findExpedientIndex($expedients, $expedientId)
  {
    foreach ($expedients as $index => $item) {
      if ($item['id_exp'] == $expedientId) {
        return $index;
      }
    }
    return null;
  }
}
