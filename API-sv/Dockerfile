# Usa una imagen base de PHP con Apache
FROM php:7.4-apache

# Establece el directorio de trabajo
WORKDIR /var/www/html

# Copia todos los archivos de la app al contenedor
COPY . .

# Configura Apache para servir la app
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# Añade configuración adicional para el sitio
RUN a2enmod rewrite
RUN service apache2 restart

# Instala las dependencias usando Composer
RUN apt-get update && \
  apt-get install -y git zip unzip && \
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && \
  composer install

# Expone el puerto 8080 para las solicitudes HTTP
EXPOSE 8080

# Comando de entrada para iniciar Apache
CMD ["apache2-foreground"]
