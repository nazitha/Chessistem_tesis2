# Usar imagen oficial de PHP con Apache
FROM php:8.2-apache

# Instalar dependencias del sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    postgresql-client \
    libpq-dev \
    gnupg \
    && docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Establecer directorio de trabajo
WORKDIR /var/www/html

# Copiar archivos de la aplicación
COPY . .

# Instalar dependencias de PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Instalar dependencias de Node.js y compilar assets
RUN npm install \
    && npm run build \
    && npm cache clean --force

# Configurar Apache para Laravel
RUN a2enmod rewrite \
    && a2enmod headers \
    && echo "ServerTokens Prod" >> /etc/apache2/apache2.conf \
    && echo "ServerSignature Off" >> /etc/apache2/apache2.conf

# Crear configuración de Apache para Laravel
RUN echo '<VirtualHost *:80>\n\
    ServerName localhost\n\
    DocumentRoot /var/www/html/public\n\
    \n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    \n\
    # Headers de seguridad\n\
    Header always set X-Frame-Options "SAMEORIGIN"\n\
    Header always set X-XSS-Protection "1; mode=block"\n\
    Header always set X-Content-Type-Options "nosniff"\n\
    Header always set Referrer-Policy "no-referrer-when-downgrade"\n\
    \n\
    # Forzar HTTPS\n\
    RewriteEngine On\n\
    RewriteCond %{HTTP:X-Forwarded-Proto} !https\n\
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]\n\
    \n\
    # Logs\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

# Configurar permisos
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Crear script de inicialización
RUN echo '#!/bin/bash\n\
echo "🚀 Iniciando aplicación Laravel..."\n\
\n\
# Ejecutar migraciones si la base de datos está disponible\n\
php artisan migrate --force || echo "⚠️ No se pudo conectar a la base de datos"\n\
\n\
# Limpiar y optimizar cache\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
php artisan cache:clear\n\
\n\
# Optimizar para producción\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
\n\
# Crear enlace simbólico para storage\n\
php artisan storage:link || echo "⚠️ Storage link ya existe"\n\
\n\
echo "✅ Aplicación lista!"\n\
\n\
# Iniciar Apache\n\
exec apache2-foreground' > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# Exponer puerto 80
EXPOSE 80

# Comando por defecto
CMD ["/usr/local/bin/start.sh"]
