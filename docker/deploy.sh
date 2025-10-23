#!/bin/bash

# Script de despliegue para Laravel en Render
echo "🚀 Iniciando despliegue de Laravel..."

# Esperar a que la base de datos esté disponible
echo "⏳ Esperando conexión a la base de datos..."
until php artisan migrate:status > /dev/null 2>&1; do
    echo "Esperando conexión a la base de datos..."
    sleep 2
done

# Ejecutar migraciones
echo "📊 Ejecutando migraciones..."
php artisan migrate --force

# Limpiar y optimizar cache
echo "🧹 Limpiando cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Optimizar para producción
echo "⚡ Optimizando para producción..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Crear enlace simbólico para storage si no existe
if [ ! -L public/storage ]; then
    echo "🔗 Creando enlace simbólico para storage..."
    php artisan storage:link
fi

# Establecer permisos correctos
echo "🔐 Estableciendo permisos..."
chown -R www-data:www-data /var/www/html/storage
chown -R www-data:www-data /var/www/html/bootstrap/cache
chmod -R 755 /var/www/html/storage
chmod -R 755 /var/www/html/bootstrap/cache

echo "✅ Despliegue completado exitosamente!"
