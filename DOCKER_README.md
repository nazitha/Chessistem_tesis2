# 🐳 Chessistem - Docker Setup

## 📋 Requisitos

- Docker
- Docker Compose (para desarrollo)

## 🚀 Despliegue Rápido

### **Opción 1: Docker Compose (Desarrollo)**
```bash
# Clonar el repositorio
git clone <tu-repositorio>
cd Chessistem_tesis2

# Ejecutar con docker-compose
docker-compose up
```

**Acceso:**
- **Aplicación**: http://localhost:8000
- **Base de datos**: localhost:5432
- **Redis**: localhost:6379

### **Opción 2: Docker Build (Producción)**
```bash
# Construir la imagen
docker build -t chessistem-app .

# Ejecutar la aplicación
docker run -p 8000:80 \
  -e APP_KEY="tu_app_key" \
  -e DATABASE_URL="tu_database_url" \
  chessistem-app
```

## 🔧 Configuración

### **Variables de Entorno Requeridas:**
```bash
APP_KEY=base64:tu_app_key_aqui
DATABASE_URL=postgresql://usuario:password@host:puerto/database
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
```

### **Variables Opcionales:**
```bash
MAIL_MAILER=brevo
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_password
MAIL_ENCRYPTION=tls
```

## 🛠️ Comandos Útiles

### **Construir Imagen:**
```bash
docker build -t chessistem-app .
```

### **Ejecutar Contenedor:**
```bash
docker run -p 8000:80 chessistem-app
```

### **Ver Logs:**
```bash
docker logs chessistem-app
```

### **Entrar al Contenedor:**
```bash
docker exec -it chessistem-app bash
```

### **Ejecutar Comandos Artisan:**
```bash
docker exec chessistem-app php artisan migrate
docker exec chessistem-app php artisan cache:clear
```

## 🌐 Despliegue en Producción

### **Render:**
1. Conecta tu repositorio
2. Selecciona "Docker" como runtime
3. Configura las variables de entorno
4. Deploy automático

### **Railway:**
1. Conecta tu repositorio
2. Railway detectará el Dockerfile automáticamente
3. Configura las variables de entorno
4. Deploy automático

### **DigitalOcean App Platform:**
1. Conecta tu repositorio
2. Selecciona "Docker" como tipo de app
3. Configura las variables de entorno
4. Deploy automático

## 🔍 Troubleshooting

### **Error de Permisos:**
```bash
sudo chown -R $USER:$USER storage bootstrap/cache
```

### **Error de Base de Datos:**
- Verifica que DATABASE_URL esté correcta
- Asegúrate de que la base de datos esté accesible

### **Error de Assets:**
```bash
docker exec chessistem-app npm run build
```

## 📊 Monitoreo

### **Ver Estado del Contenedor:**
```bash
docker ps
```

### **Ver Uso de Recursos:**
```bash
docker stats chessistem-app
```

### **Reiniciar Aplicación:**
```bash
docker restart chessistem-app
```

## 🎯 Características

- ✅ **PHP 8.2** con Apache
- ✅ **PostgreSQL** incluido en docker-compose
- ✅ **Redis** para cache
- ✅ **Node.js 18** para assets
- ✅ **SSL** configurado
- ✅ **Optimizado** para producción
- ✅ **Health checks** incluidos

¡Tu aplicación Chessistem está lista para Docker! 🚀
