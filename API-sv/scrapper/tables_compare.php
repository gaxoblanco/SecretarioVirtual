<?php
// Esta archivo vamos a encontrar la diferencia entre dos arrays. La solución debe devolver una arrays que contenga todos los elementos de la primera arrays que no están presentes en la segunda arrays.

class tables_compare
{
    private $oldTableUserExp; // El array de usuarios y expedientes en la tabla vieja.
    private $newTableUserExp; // El array de usuarios y expedientes en la tabla nueva.

    public function __construct($oldTableUserExp, $newTableUserExp)
    {
        $this->oldTableUserExp = $oldTableUserExp;
        $this->newTableUserExp = $newTableUserExp;
    }

    public function compareTables()
    {
        $result = []; // El resultado que contendrá los datos nuevos.

        // Itera a través de los usuarios y expedientes en la tabla nueva.
        foreach ($this->newTableUserExp as $newUser) {
            $userExistsInOld = false;

            // Verifica si el usuario existe en la tabla vieja.
            foreach ($this->oldTableUserExp as $oldUser) {
                if ($oldUser['id_user'] === $newUser['id_user']) {
                    $userExistsInOld = true;

                    // Compara los expedientes de este usuario.
                    $newExpedients = $this->compareExpedients($oldUser['expedients'], $newUser['expedients']);
                    if (!empty($newExpedients)) {
                        $oldUser['expedients'] = $newExpedients;
                        $result[] = $oldUser;
                    }

                    break;
                }
            }

            // Si el usuario no existe en la tabla vieja, agrega el usuario completo como nuevo.
            if (!$userExistsInOld) {
                $result[] = $newUser;
            }
        }

        return $result;
    }

    private function compareExpedients($oldExpedients, $newExpedients)
    {
        $result = [];

        // Itera a través de los expedientes en la tabla nueva.
        foreach ($newExpedients as $newExpedient) {
            $expedientExistsInOld = false;

            // Verifica si el expediente existe en la tabla vieja.
            foreach ($oldExpedients as $oldExpedient) {
                if ($oldExpedient['id_exp'] === $newExpedient['id_exp']) {
                    $expedientExistsInOld = true;

                    // Compara los movimientos de este expediente.
                    $newMovements = $this->compareMovements($oldExpedient['movimientos'], $newExpedient['movimientos']);
                    if (!empty($newMovements)) {
                        $oldExpedient['movimientos'] = $newMovements;
                        $result[] = $oldExpedient;
                    }

                    break;
                }
            }

            // Si el expediente no existe en la tabla vieja, agrega el expediente completo como nuevo.
            if (!$expedientExistsInOld) {
                $result[] = $newExpedient;
            }
        }

        return $result;
    }

    private function compareMovements($oldMovements, $newMovements)
    {
        $result = [];

        // Itera a través de los movimientos en la tabla nueva.
        foreach ($newMovements as $newMovement) {
            $movementExistsInOld = false;

            // Verifica si el movimiento existe en la tabla vieja.
            foreach ($oldMovements as $oldMovement) {
                if ($oldMovement['id_move'] === $newMovement['id_move']) {
                    $movementExistsInOld = true;
                    break;
                }
            }

            // Si el movimiento no existe en la tabla vieja, agrega el movimiento completo como nuevo.
            if (!$movementExistsInOld) {
                $result[] = $newMovement;
            }
        }
        // echo "compareMovements: " . var_dump($result);
        return $result;
    }
}
