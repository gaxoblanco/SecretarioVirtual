<?php
//  teniendo los datos de todos los usuarios  // es decir toda la db de los expedientes que desean los usuarios
//  tablas:
//  1- user_expedients: id_exp 	id_lista_despacho 	numero_exp 	id_user 	anio_exp 	caratula 	reservado 	dependencia 	tipo_lista
//  2- user_exp_move:  id_move 	fecha_movimiento 	estado 	titulo 	texto 	despacho 	id_exp
//  3- expedientes:  id_expediente 	id_lista_despacho 	numero_expediente 	anio_expediente 	caratula 	reservado 	dependencia 	tipo_lista
//  4- movimientos:  id_expediente 	id_lista_despacho 	numero_expediente 	anio_expediente

require_once './up-exp/find_index.php';
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
  public function getExpedient($offset, $limit)
  {
    // Itera por el array users y por cada user itera por el array expedients
    for ($i = $offset; $i < min($offset + $limit, count($this->users)); $i++) {
      $user = $this->users[$i];

      // Divide los expedientes en bloques
      $expedientChunks = array_chunk($user['expedients'], $limit);

      // Itera por cada bloque de expedientes
      foreach ($expedientChunks as $expedientBlock) {

        // Itera por el array expedients
        foreach ($expedientBlock as &$expedient) {

          //---- array constructor
          // Verificamos si el usuario ya está en $this->newsBy
          $userIndex = findUserIndex($user['id_user'], $this->newsBy);

          // si dependencia == null
          if ($expedient['tipo_lista'] == null) {
            // echo "el expediente del usuario: " . $user['id_user'] . " no TENIA dependencia: " . $expedient['numero_exp'] . '/' . $expedient['anio_exp'] . " - id_exp " . $expedient['id_exp'] . " : ";

            require_once './up-exp/exists_exp.php';
            // llamar a la funcion existsExp que recibe el numero_exp, anio_exp y dependencia
            $ifExisted = existsExp($expedient['numero_exp'], $expedient['anio_exp'], $user['id_user'], $expedient['id_exp'], $expedient['dependencia'], $this->conexion);
            // echo "existsExp data: " . var_dump($ifExisted) . " : ";

            // Si $ifExisted devuelve datos los guardamos en el indice expdients del newsBy
            if ($ifExisted) {

              if ($userIndex === null) {
                // el usuario aun no esta en la lista de newsBy, lo cargo juinto con el expediente
                $this->newsBy[] = [
                  'id_user' => $user['id_user'], // Agregamos el id del usuario
                  'name' => isset($user['name']) ? $user['name'] : '',
                  'email' => isset($user['email']) ? $user['email'] : '',
                  'expedients' => [] // Inicializamos el array de expedientes
                ];
                $userIndex = count($this->newsBy) - 1; // El índice del usuario que acabamos de agregar
              }
              // de actualizar la tabla para que concidan los nombres se resuelven las lineas de codigos de abajo.
              // borro el "id_expediente" del newsBy->expedients // id_expediente viene de la tabla expedients
              unset($ifExisted[0]['id_expediente']);
              // inserta el id_exp = $expedient['id_exp']
              $ifExisted[0]['id_exp'] = $expedient['id_exp'];
              // cambio el nombre de numero_expediente por numero_exp y conservo el valor
              $ifExisted[0]['numero_exp'] = $ifExisted[0]['numero_expediente'];
              // cambio el nombre de anio_expediente por anio_exp
              $ifExisted[0]['anio_exp'] = $ifExisted[0]['anio_expediente'];
              $this->newsBy[$userIndex]['expedients'][] = $ifExisted[0];
            } else {
              // si no existe, devuelve mensaje " expediente no encontrado"
              echo "El expediente del usuario " . $user['id_user'] . " no se encontró en la tabla expedientes. <br>";
            }
          }

          // si existe el expediente (dependencia != null) - obtengo el id del expediente
          require_once './up-exp/id_expedient.php';
          $idExpediente = getIdExpediente($expedient['numero_exp'], $expedient['anio_exp'], $expedient['dependencia'], $this->conexion);
          // echo "idExpediente" . $idExpediente;

          // controlo que $idExpediente existe en la tabla expedientes
          if (!$idExpediente) {
            echo "El expediente del usuario " . $user['id_user'] . " no se encontró en la tabla expedientes. <br>";
          }

          //---- Movimientos del Expediente ----
          // obtengo los movimientos del expediente en la tabla movimientos
          require_once './up-exp/exists_move.php';
          $ExpMoving = getExpedientsMoves($idExpediente, $this->conexion);
          // controlo que $ExpMoving no sea null es decir que tenemos al menos 1 movimiento
          if (!$ExpMoving) {
            echo "El expediente " . $idExpediente . " no tiene movimientos.<br>";
          }

          // obtengo los movimientos del expediente en la tabla user_exp_move, buscando por el $expedient['id_exp']
          require_once './up-exp/get_user_exp_move.php';
          $userExpMoving = getMovimientos($expedient['id_exp'], $this->conexion);
          // controlo que $userExpMoving que tenga al menos 1 movimiento
          if (!$userExpMoving) {
            echo "El expediente del usuario " . $user['id_user'] . " no tiene movimientos. <br>";
          }

          // echo "UserExpMoving: <pre>" . print_r($userExpMoving, true) . "</pre>";
          // echo " ---------- ";
          // echo "ExpMoving: <pre>" . print_r($ExpMoving, true) . "</pre>";

          // comparo el numero de elementos de $ExpMoving con $userExpMoving, si $ExpMoving tiene mas elementos que $userExpMoving, obtengo los movimientos que no estan en $userExpMoving
          if (is_array($ExpMoving) && is_array($userExpMoving) && count($ExpMoving) > count($userExpMoving)) {
            // obtengo los movimientos que no estan en $userExpMoving
            $newMoves = array_diff($ExpMoving, $userExpMoving);
            var_dump($newMoves);

            // actualiza la tabla user_exp_move con los movimientos que no estan en $userExpMoving y el id_exp = $expedient['id_exp']
            require_once './up-exp/new_move.php';
            newMove($newMoves, $expedient['id_exp'], $this->conexion);

            // en el array newsBy, consulto si el usuario ya esta en el array
            if ($userIndex === null) {
              // el usuario aun no esta en la lista de newsBy, lo cargo juinto con el expediente
              $this->newsBy[] = [
                'id_user' => $user['id_user'], // Agregamos el id del usuario
                'name' => isset($user['name']) ? $user['name'] : '',
                'email' => isset($user['email']) ? $user['email'] : '',
                'expedients' => [] // Inicializamos el array de expedientes
              ];
              $userIndex = count($this->newsBy) - 1; // El índice del usuario que acabamos de agregar
            }
            // en el array newsBy, consulto si el expediente ya esta en el array
            $expedientIndex = findExpedientIndex($this->newsBy[$userIndex]['expedients'], $expedient['id_exp']);
            if ($expedientIndex === null) {
              // el expediente aun no esta en la lista de newsBy, lo cargo juinto con el usuario
              $this->newsBy[$userIndex]['expedients'][] = [
                'id_exp' => $expedient['id_exp'], // Agregamos el id del expediente
                'numero_exp' => $expedient['numero_exp'],
                'anio_exp' => $expedient['anio_exp'],
                'caratula' => $expedient['caratula'],
                'reservado' => $expedient['reservado'],
                'dependencia' => $expedient['dependencia'],
                'tipo_lista' => $expedient['tipo_lista'],
                'movimientos' => [] // Inicializamos el array de movimientos
              ];
              $expedientIndex = count($this->newsBy[$userIndex]['expedients']) - 1; // El índice del expediente que acabamos de agregar
            }

            // en el array newsBy, en el indice del user, en el indice expedients, agrego el array de movimientos
            $this->newsBy[$userIndex]['expedients'][$expedientIndex]['movimientos'] = $newMoves;
          }
        }
      }
    }
    // echo json_encode($this->newsBy);
    return $this->newsBy;
  }
}
