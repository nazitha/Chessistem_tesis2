# 🚀 Guía de Despliegue a Producción - Chessistem

Esta guía te ayudará a desplegar tu aplicación Laravel Chessistem en Render usando Docker.

## 📋 Requisitos Previos

1. ✅ Dominio comprado
2. ✅ Cuenta en Render
3. ✅ Repositorio en GitHub/GitLab

## 🔧 Configuración en Render

### Paso 1: Crear Base de Datos PostgreSQL

1. Ve al [Dashboard de Render](https://dashboard.render.com)
2. Haz clic en **"New +"** → **"PostgreSQL"**
3. Configura:
   - **Name**: `chessistem-db`
   - **Database**: `chessistem`
   - **User**: `chessistem_user`
   - **Region**: Elige la más cercana a tus usuarios
4. **¡IMPORTANTE!** Copia la **URL interna** de la base de datos (no la externa)

### Paso 2: Crear Web Service

1. En el Dashboard, haz clic en **"New +"** → **"Web Service"**
2. Conecta tu repositorio de GitHub
3. Configura el servicio:
   - **Name**: `chessistem-app`
   - **Runtime**: `Docker`
   - **Region**: La misma que la base de datos
   - **Branch**: `main`

### Paso 3: Variables de Entorno

En la sección **"Advanced"** → **"Environment Variables"**, agrega:

```bash
# Configuración de la aplicación
APP_NAME=Chessistem
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Base de datos (usa la URL interna de PostgreSQL)
DATABASE_URL=postgresql://usuario:password@host:puerto/database
DB_CONNECTION=pgsql

# Clave de aplicación (genera una nueva)
APP_KEY=base64:tu_clave_generada_aqui

# Configuración de correo (Brevo)
MAIL_MAILER=brevo
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario_brevo
MAIL_PASSWORD=tu_password_brevo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tu-dominio.com
MAIL_FROM_NAME=Chessistem

# Configuración de sesiones
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## 🔑 Generar APP_KEY

Ejecuta este comando en tu máquina local:

```bash
php artisan key:generate --show
```

Copia el resultado y úsalo como valor de `APP_KEY` en Render.

## 🌐 Configuración del Dominio

### En Render:
1. Ve a tu Web Service
2. En **"Settings"** → **"Custom Domains"**
3. Agrega tu dominio: `tu-dominio.com`
4. Render te dará un registro CNAME

### En tu proveedor de DNS:
1. Crea un registro CNAME:
   - **Name**: `www` (o `@` si es el dominio raíz)
   - **Value**: El CNAME que te dio Render
   - **TTL**: 3600

## 📁 Archivos Creados

Los siguientes archivos han sido creados para el despliegue:

- `Dockerfile` - Configuración del contenedor Docker
- `docker/nginx.conf` - Configuración de Nginx
- `docker/supervisord.conf` - Gestión de procesos
- `docker/deploy.sh` - Script de despliegue automático
- `render.env.example` - Variables de entorno de ejemplo

## 🚀 Proceso de Despliegue

Una vez configurado todo:

1. **Render automáticamente**:
   - Construirá la imagen Docker
   - Ejecutará las migraciones
   - Optimizará la aplicación
   - Desplegará el servicio

2. **El script de despliegue** (`deploy.sh`) se ejecutará automáticamente y:
   - Ejecutará las migraciones
   - Limpiará y optimizará el cache
   - Configurará los permisos
   - Creará el enlace simbólico de storage

## 🔍 Verificación Post-Despliegue

1. Visita tu dominio
2. Verifica que la aplicación carga correctamente
3. Prueba el login/registro
4. Revisa los logs en Render Dashboard

## 🛠️ Comandos Útiles

### Ver logs en tiempo real:
```bash
# En Render Dashboard → Logs
```

### Ejecutar comandos Artisan:
```bash
# En Render Dashboard → Shell
php artisan migrate:status
php artisan config:cache
```

## 🚨 Solución de Problemas

### Error de conexión a base de datos:
- Verifica que `DATABASE_URL` esté correcta
- Asegúrate de usar la URL **interna** de PostgreSQL

### Error 500:
- Revisa los logs en Render Dashboard
- Verifica que `APP_KEY` esté configurada
- Asegúrate de que las migraciones se ejecutaron

### Assets no cargan:
- Verifica que `APP_URL` esté configurada correctamente
- Asegúrate de que el dominio esté configurado en Render

## 📞 Soporte

Si tienes problemas:
1. Revisa los logs en Render Dashboard
2. Verifica las variables de entorno
3. Asegúrate de que todos los pasos se completaron correctamente

¡Tu aplicación Chessistem estará en línea en unos minutos! 🎉
