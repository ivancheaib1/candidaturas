FROM php:8.2-apache

# Instalar extensiones necesarias (incluyendo mysqli)
RUN docker-php-ext-install \
    mysqli \
    pdo \
    pdo_mysql

# Habilitar mod_rewrite
RUN a2enmod rewrite

# Configurar php.ini para desarrollo
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php.ini && \
    echo "upload_max_filesize = 50M" >> /usr/local/etc/php/conf.d/docker-php.ini && \
    echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/docker-php.ini

# Exponer puerto
EXPOSE 80

# Comando por defecto
CMD ["apache2-foreground"]