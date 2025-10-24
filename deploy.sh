#!/bin/bash

# Script para desplegar la aplicación Docker

echo "🚀 Desplegando Chessistem..."

# Variables de entorno requeridas
if [ -z "$APP_KEY" ]; then
    echo "❌ Error: APP_KEY no está definida"
    exit 1
fi

if [ -z "$DATABASE_URL" ]; then
    echo "❌ Error: DATABASE_URL no está definida"
    exit 1
fi

# Construir la imagen
echo "🔨 Construyendo imagen..."
docker build -t chessistem-app .

if [ $? -ne 0 ]; then
    echo "❌ Error al construir la imagen"
    exit 1
fi

# Ejecutar la aplicación
echo "🚀 Iniciando aplicación..."
docker run -d \
    --name chessistem-app \
    -p 80:80 \
    -e APP_KEY="$APP_KEY" \
    -e DATABASE_URL="$DATABASE_URL" \
    -e APP_ENV=production \
    -e APP_DEBUG=false \
    chessistem-app

if [ $? -eq 0 ]; then
    echo "✅ Aplicación desplegada exitosamente"
    echo "🌐 Disponible en: http://localhost"
else
    echo "❌ Error al desplegar la aplicación"
    exit 1
fi
