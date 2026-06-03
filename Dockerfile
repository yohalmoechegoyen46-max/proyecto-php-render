# FROM: Descarga una imagen oficial y optimizada desde Docker Hub.
# En este caso, un sistema operativo equipado con PHP versión 8.2 y el servidor web Apache preinstalado.
FROM php:8.2-apache

# COPY: Toma absolutamente todo lo que esté en nuestra carpeta raíz local (origen: '.')
# y lo clona/copia dentro de la ruta pública interna del contenedor de Linux (destino: '/var/www/html/').
# Ahí es donde Apache busca los archivos para servirlos en internet.
COPY . /var/www/html/

# EXPOSE: Comando meramente informativo que documenta que el contenedor estará escuchando
# peticiones en el puerto estándar HTTP número 80. Render utiliza este valor para redirigir el tráfico.
EXPOSE 80