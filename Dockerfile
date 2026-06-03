FROM php:8.2-apache

# Actualizar el gestor de paquetes e instalar extensiones nativas de PostgreSQL para PHP
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Trasladar el código fuente al directorio raíz del servidor HTTP Apache
COPY . /var/www/html/

# Configurar Apache para que otorgue prioridad absoluta a index.html sobre index.php
RUN echo "DirectoryIndex index.html index.php" > /var/www/html/.htaccess

# Habilitar el motor de reescritura de URLs de Apache
RUN a2enmod rewrite

EXPOSE 80