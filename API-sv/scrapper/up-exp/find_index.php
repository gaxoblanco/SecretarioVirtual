<?php

//--- array constructor  ---
// Función para buscar al usuario en $this->newsBy
function findUserIndex($userId, $newsBy)
{
    // busco en el array $this->newsBy el indice del usuario que coincida con el id_user
    foreach ($newsBy as $index => $item) {
        if ($item['id_user'] == $userId) {
            return $index;
        }
    }
}

// Función para buscar el expediente en los expedientes del usuario
function findExpedientIndex($expedients, $expedientId)
{
    foreach ($expedients as $index => $item) {
        if ($item['id_exp'] == $expedientId) {
            return $index;
        }
    }
    return null;
}
