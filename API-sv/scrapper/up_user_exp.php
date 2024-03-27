<?php
//  teniendo los datos de todos los usuarios  // es decir toda la db de los expedientes que desean los usuarios
// 1 - compare the tables and update the expedients and movements
// 2 - Save the expedients data in the newsBy index
// 3 - clear and prepare the data to be saved
// 4 - Take the id_expediente and save the movements
// 5 - Take the discrepancies in the movements tables and save in $newMoves array
// 6 - Update the user_exp_move table
// 7 - Add the array newsMoves to the array newsBy

require_once './up-exp/find_index.php';
require_once './up-exp/exists_exp.php';

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

  public function getExpedient($offset, $limit)
  {
    // 1 - Compare the tables
    foreach ($this->users as &$user) {
      foreach ($user['expedients'] as &$expedient) {
        //---- array constructor
        // Verificamos si el usuario ya está en $this->newsBy y obtengo la posicion en el array
        $userIndex = findUserIndex($user['id_user'], $this->newsBy);


        // echo "userIndex: " . $userIndex . "<br>";
        echo "expedient: " . var_dump($expedient['tipo_lista']) . "<br->";
        // si tipo_lista == null
        if ($expedient['tipo_lista'] == null) {
          // echo "el expediente del usuario: " . $user['id_user'] . " no tiene tipo_lista <br>";

          // Valido si el expediente existe en la tabla expedientes
          $ifExisted = existsExp($expedient['numero_exp'], $expedient['anio_exp'], $user['id_user'], $expedient['id_exp'], $expedient['dependencia'], $this->conexion);
          // echo "existsExp data: " . var_dump($ifExisted) . " : ";

          // 2 - Save the expedients data
          // Si $ifExisted devuelve datos los guardamos en el indice expdients del newsBy
          if ($ifExisted) {

            if ($userIndex === null) {
              // el usuario aun no esta en la lista de newsBy, lo cargo junto con el expediente
              $this->newsBy[] = [
                'id_user' => $user['id_user'], // Agregamos el id del usuario
                'name' => isset($user['name']) ? $user['name'] : '',
                'email' => isset($user['email']) ? $user['email'] : '',
                'expedients' => [] // Inicializamos el array de expedientes
              ];
              $userIndex = count($this->newsBy) - 1; // El índice del usuario que acabamos de agregar
            }
            // 3 - clear and prepare the data to be saved
            // borro el "id_expediente" del newsBy->expedients // id_expediente viene de la tabla expedients
            unset($ifExisted[0]['id_expediente']);
            // inserta el id_exp = $expedient['id_exp']
            $ifExisted[0]['id_exp'] = $expedient['id_exp'];
            // cambio el nombre de numero_expediente por numero_exp y conservo el valor
            $ifExisted[0]['numero_exp'] = $ifExisted[0]['numero_expediente'];
            // cambio el nombre de anio_expediente por anio_exp
            $ifExisted[0]['anio_exp'] = $ifExisted[0]['anio_expediente'];
            $this->newsBy[$userIndex]['expedients'][] = $ifExisted[0];

            // alamaceno el indice del expediente en el array newsBy
            $expedientIndex = count($this->newsBy[$userIndex]['expedients']) - 1; // El índice del expediente que acabamos de agregar

          } else {
            // si no existe, devuelve mensaje " expediente no encontrado"
            // echo "El expediente del usuario " . $user['id_user'] . " no se encontró en la tabla expedientes. <br>";
          }
        }

        // 4 - Take the id_expediente
        // si existe el expediente (tipo_lista != null) - obtengo el id del expediente
        require_once './up-exp/id_expedient.php';
        $idExpediente = getIdExpediente($expedient['numero_exp'], $expedient['anio_exp'], $expedient['dependencia'], $this->conexion);
        // echo "idExpediente" . $idExpediente;

        // controlo que $idExpediente existe en la tabla expedientes
        if (!$idExpediente) {
          // echo "El expediente del usuario " . $user['id_user'] . " no se encontró en la tabla expedientes. <br>";
        }

        // 4 - Take the de movements
        //---- Movimientos del Expediente ----
        // obtengo los movimientos del expediente en la tabla movimientos
        require_once './up-exp/exists_move.php';
        $ExpMoving = getExpedientsMoves($idExpediente, $this->conexion);
        // controlo que $ExpMoving no sea null es decir que tenemos al menos 1 movimiento
        if (!$ExpMoving) {
          // echo "El ExpMoving " . $idExpediente . " no tiene movimientos.<br>";
        }

        // obtengo los movimientos del expediente en la tabla user_exp_move, buscando por el $expedient['id_exp']
        require_once './up-exp/get_user_exp_move.php';
        $userExpMoving = getMovimientos($expedient['id_exp'], $this->conexion);
        // controlo que $userExpMoving que tenga al menos 1 movimiento
        if (!$userExpMoving) {
          echo "El expediente " . $expedient['id_exp'] . " no tiene movimientos. <br>";
        }

        // 5 - Take the discrepancies in the movements tables
        // comparo el numero de elementos de $ExpMoving con $userExpMoving, si $ExpMoving tiene mas elementos que $userExpMoving, obtengo los movimientos que no estan en $userExpMoving
        if (is_array($ExpMoving) && is_array($userExpMoving) && count($ExpMoving) > count($userExpMoving)) {
          // Obtén los nuevos elementos que están en $ExpMoving pero no en $userExpMoving
          $newMoves = array_diff_assoc($ExpMoving, $userExpMoving);
          // echo "ExpMoving-->: <pre>" . print_r($newMoves, true) . "</pre>";


          // 6 Update the user_exp_move table
          // actualiza la tabla user_exp_move con los movimientos que no estan en $userExpMoving y el id_exp = $expedient['id_exp']
          require_once './up-exp/new_move.php';
          newMove($newMoves, $expedient['id_exp'], $this->conexion);

          // 7 - Add the array newsMoves to the array newsBy
          // en el array newsBy, consulto si el usuario ya esta en el array
          if ($userIndex === null) {
            // el usuario aun no esta en la lista de newsBy, lo cargo junto con el expediente
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
            // el expediente aun no esta en la lista de newsBy, lo cargo junto con el usuario
            $this->newsBy[$userIndex]['expedients'][] = [
              'id_exp' => $expedient['id_exp'], // Agregamos el id del expediente
              'numero_exp' => $expedient['numero_exp'],
              'anio_exp' => $expedient['anio_exp'],
              'caratula' => $expedient['caratula'],
              'reservado' => $expedient['reservado'],
              'dependencia' => $expedient['dependencia'],
              'tipo_lista' => $expedient['tipo_lista'],
              'movimientos' => [], // Inicializamos el array de movimientos
            ];
            $expedientIndex = count($this->newsBy[$userIndex]['expedients']) - 1; // El índice del expediente que acabamos de agregar
          }

          // en el array newsBy, en el indice del user, en el indice expedients, agrego el array de movimientos
          // $this->newsBy[$userIndex]['expedients'][$expedientIndex]['movimientos'] = $newMoves;

          //----- Service upgrade in progress -----
          // En el array newsBy, en el indice del user, en el indice expedients, agrego el ultimo movimiento
          $this->newsBy[$userIndex]['expedients'][$expedientIndex]['movimientos'][] = end($newMoves);
        }
      }
    }
    // echo json_encode($this->newsBy);
    return $this->newsBy;
  }
}
