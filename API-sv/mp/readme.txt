######################################
# Mercado Pago Payment Gateway
######################################

This README file explains the functionality of the Mercado Pago payment gateway integration within the Secretario Virtual application.

## Overview

The Mercado Pago payment gateway integration allows users to subscribe to Secretario Virtual services and make payments securely through Mercado Pago. The integration involves several steps, including user creation, subscription plan selection, payment processing, and user authentication.

## --------------------------------------

## update_id_mp
  - La API de mercado pago solo me otroga el preaprobal_id, y el id llega solo si el usuario termina el recorrido volviendo a mi web.
  - Para no depender del usuario este script se encarga de obtener el id

1. **updating_id_mp:**
    - Obtengo todos los usuarios que aun no tienen la suscripcion activa (id_mp == null)
    - Itero sobre la lista para pasarle el preapproval_id a la clase searchData

2. **search_data:**
    - Hago una solicitud para a api.mercadopago.com/preapproval/search? para saber cuanto ususario tengo suscriptos y asi paginar el llamado.
    - Por cada paginado itero buscando el ['preapproval_plan_id'] == $preapproval_id en la resputa
    - Si conciden llamo a updateIdMp enviando el preapproval_id para obtener el usuario a actualizar y el id_mp obtenido.
    - Si el usuario no tiene una suscripcion no se encuentra id y no se actualiza nada.

## --------------------------------------

## payment_status
1. **updating_status:**
    - inicializa el script para actualizar el status de pago de los usuarios
    - con esto mantenemos actualizado si el usuario pago o no su suscripción

2. **searchStatus:**
    - Hace un paginado y solicita todos los suscriptos en la API de Mercado Pago

3. **updateStatus:**
    - Obtiene el nuevo status de la suscripcion y la actualiza en el usuario correspondiente.

4. **payment_filter:**
    - Se lo llama en cada funcionalidad que se dese limitar el ingreso si no a pagado la suscripcion.
