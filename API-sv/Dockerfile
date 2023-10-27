# Usa una imagen base de Apache
FROM php:7.4-apache


# Copia todo el proyecto en /usr/src
COPY [".", ""]
# Deja abierto el puerto 8080 para las solicitudes hacia el index.php
EXPOSE 8080
# Habilita el módulo headers de Apache
RUN a2enmod headers

