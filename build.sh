#!/bin/bash

# Script para construir y ejecutar la imagen Docker

echo "🐳 Construyendo imagen Docker para Chessistem..."

# Construir la imagen
docker build -t chessistem-app .

if [ $? -eq 0 ]; then
    echo "✅ Imagen construida exitosamente"
    echo "🚀 Para ejecutar la aplicación:"
    echo "   docker run -p 8000:80 chessistem-app"
    echo ""
    echo "🔧 Para desarrollo con docker-compose:"
    echo "   docker-compose up"
else
    echo "❌ Error al construir la imagen"
    exit 1
fi
