Manejo de datos para el servicio de Secretario Virtual. Actualizado 08/2023

<!--  pjf-listas-despacho -->
Este directorio contiene toda la lógica necesaria para realizar el scraping de los expedientes. También cuenta con un repositorio separado.

Se trabaja toda la lógica del negocio con getListaDespachoPorExpediente($dependencia, $numero, $anio). Esta función obtiene los expedientes de forma individual para ahorrar recursos de memoria y evitar traer expedientes antiguos.
Dentro de la carpeta, se encuentran otras funciones que permiten obtener los expedientes utilizando diferentes enfoques.

<!-- user -->
En este directorio se encuentra la lógica para el manejo de los usuarios, incluidos los secretarios.

<!-- user-dispatch -->
Este directorio contiene la lógica para almacenar y actualizar los expedientes de los usuarios.

* process_dispatch.php: Consulta en la base de datos todos los expedientes con state = 0. Luego, filtra los expedientes que ya han sido resueltos en el juzgado (update_dispatch actualiza status) y guarda los datos relevantes en la tabla "records".

* update_dispatch.php: Consulta en la base de datos todos los expedientes con state = 0 y los compara con los registros almacenados en la tabla "records" utilizando los campos "year_number" y "caseNumber" (que representan el número de caso y el año). Si encuentra coincidencias, actualiza los expedientes y solicita el envío de los correos correspondientes y devuelve $groupedDispatches(array) con los expedientes resueltos y esta busqueda.

* new_dispatchs.php: toma el array $groupedDispatches que se compone de dispatchId, y los procesa agrupandolos por userId de tal modo que terminamos con: ({"2":[13,15,16],"userId":[dispatchId]})
y devuelve a $groupedDispatches actualizado.

<!-- si el usuario desea volver a recibir la informacion del expediente  -->
* updata_dispatch_status.php: Permite que el usuario solicite nuevamente la información de un expediente. Cambia el estado de 1 (verdadero) a 0 (falso) y lo agrega a la cola de solicitudes.

<!-- emails -->
* list_email.php: recibe $groupedDispatches actualizado ({"2":[13,15,16],"userId":[dispatchId]})
entonces itera por cada usuario, trayendo las tablas y trabajando con los campos:
    * users: email, firstname. 
    * secretarylist: Semail (solo los asociados al usuario)
lo guardamos en un array emailTo {to: email, copyTo: [Semail, Semail]} y lo devolvemos para que lo use send_email.php

* dispatch_email.php: recibe $groupedDispatches actualizado ({"2":[13,15,16],"userId":[dispatchId]})
entonces itera por cada dispatchId y solicita de la tabla records buscando por recordsId (relacion 1record muchos dispachtlist) entonces por cada usuario guarda un array llamado $bodyEmail con la informacion obtenida de dispatchId -> records.data.
Se gurda toda la informacion procesdada en el array $bodyEmailByUserId devuelve un obj: {"1":[{"medium_type":"Expediente Principal","cover":..}, "id""[{records.data...}]"]}

* send_email.php: trabaja con los array $emailTo y $bodyEmailByUserId
por un lado:
    * $emailTo: Este array contiene la información de los destinatarios de los correos electrónicos. Para cada entrada, se especifica la dirección de correo electrónico principal (to) y una lista de direcciones de correo electrónico a las que se desea enviar una copia (copyTo). Al iterar sobre cada entrada de este array, se configura el correo electrónico con la dirección principal como destinatario y se agrega una copia a cada una de las direcciones de correo electrónico especificadas en copyTo.

    * $bodyEmailByUserId: {"1":[{"medium_type":"Expediente Principal","cover":"OLIVA, Efren C\/ GODOY, Gilberto y\/o cualquier otro ocupante  S\/ Juicio Ordinario (Acci\u00f3n Reivindicatoria (Arts.2252 y sgtes. CCyCN))","unit":"Juzgado de 1\u00b0 Instancia en lo Civil y Comercial N\u00b0 1","year_number":"25\/22"}]...}, lo que vamos a hacer es cargar la informacion en el body del mail, siempre confirmando que el $bodyEmailByUserId.id y el $emailTo.id coinciden

## secretaries add
* add_secretary.php: recibe $secretaryData (email, firstname, lastname, password, userId) y lo guarda en la tabla secretarylist
- Consulta al archivo suscript.php para obtener el num_exp y num_secretary (son el numero maximo de expedientes y secretarios que puede tener un usuario con esa suscripcion)
- Consulta el numero de secreatarios que tiene el usuario en la tabla secretarylist
- Si el numero de secretarios es menor al numero de secretarios permitidos, se agrega el secretario a la tabla secretarylist y se devuelve un mensaje de exito.
- Si el numero de secretarios es igual al numero de secretarios permitidos, se devuelve un mensaje de error.

## dispatch add 
* add_dispatch.php: recibe $dispatchData (userId, year_number, caseNumber, medium_type, cover, unit) y lo guarda en la tabla dispatchlist
- Consulta al archivo suscript.php para obtener el num_exp y num_secretary (son el numero maximo de expedientes y secretarios que puede tener un usuario con esa suscripcion)
- Consulta el numero de expedientes que tiene el usuario en la tabla dispatchlist
- Si el numero de expedientes es menor al numero de expedientes permitidos, se agrega el expediente a la tabla dispatchlist y se devuelve un mensaje de exito.
- Si el numero de expedientes es igual al numero de expedientes permitidos, se devuelve un mensaje de error.

## get user
* get_user.php: recibe $userId y devuelve la informacion del usuario con el tipo de subscription que tiene
- Consulta si el usuario existe en la db
- Obtiene los datos del usuario
- Agrega los datos de la subscripcion
- Devuelve los datos del usuario + subscripcion



tokenControl.php: se encarga de controla que el token que recibimos en el head sea el correcto

## Docker
### Configuracion (archivo .env)
Copiar el archivo de ejemplo `.env.example` y renombrarlo como `.env`. Aqui se deben configurar las credenciales de MYSQL.

### Iniciar
Para iniciar los contenedores de Docker, ejecutar:

```
docker-compose up
```

### Database
Para importar la base de datos, archivo `despachos_dataBase.sql` 

### Accesos
Acceder al contenido de API-sv en http://localhost:8080

Para acceder a PhpMyAdmin utilizar el puerto 8081.

Las credenciales del PhpMyAdmin (como asi tambien para la conexion a MySQL desde PHP) son las configuradas en el `.env`.

Si PhpMyAdmin solicita un host, el mismo es `mysql`.

### Importar la base de datos despachos.sql

//--------
## Scrapping
corre en url/api-sv/scrapper
