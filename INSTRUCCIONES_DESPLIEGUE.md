# 🎯 Instrucciones Específicas para Desplegar Chessistem

## ✅ Archivos Creados

He creado todos los archivos necesarios para el despliegue:

- ✅ `Dockerfile` - Configuración del contenedor
- ✅ `docker/nginx.conf` - Configuración de Nginx
- ✅ `docker/supervisord.conf` - Gestión de procesos
- ✅ `docker/deploy.sh` - Script de despliegue automático
- ✅ `render.env.example` - Variables de entorno de ejemplo
- ✅ `DEPLOYMENT.md` - Guía completa de despliegue

## 🔑 Tu Clave de Aplicación

**IMPORTANTE**: Usa esta clave como valor de `APP_KEY` en Render:

```
base64:wyc2nBX4l8yzhZMu6fmgsfkYCgAnxekI3bCCy/Ch15o=
```

## 🚀 Pasos Inmediatos

### 1. Subir Código a GitHub
```bash
git add .
git commit -m "Add Docker configuration for Render deployment"
git push origin main
```

### 2. Crear Base de Datos en Render
1. Ve a [Render Dashboard](https://dashboard.render.com)
2. **New +** → **PostgreSQL**
3. Configura:
   - **Name**: `chessistem-db`
   - **Database**: `chessistem`
   - **User**: `chessistem_user`
   - **Region**: `Oregon (US West)` o la más cercana
4. **¡COPIA LA URL INTERNA!** (no la externa)

### 3. Crear Web Service
1. **New +** → **Web Service**
2. Conecta tu repositorio de GitHub
3. Configura:
   - **Name**: `chessistem-app`
   - **Runtime**: `Docker`
   - **Region**: La misma que la base de datos
   - **Branch**: `main`

### 4. Variables de Entorno en Render

En **Advanced** → **Environment Variables**, agrega:

```bash
APP_NAME=Chessistem
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com
APP_KEY=base64:wyc2nBX4l8yzhZMu6fmgsfkYCgAnxekI3bCCy/Ch15o=

DATABASE_URL=postgresql://usuario:password@host:puerto/database
DB_CONNECTION=pgsql

MAIL_MAILER=brevo
MAIL_HOST=smtp-relay.brevo.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario_brevo
MAIL_PASSWORD=tu_password_brevo
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tu-dominio.com
MAIL_FROM_NAME=Chessistem

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### 5. Configurar Dominio
1. En tu Web Service → **Settings** → **Custom Domains**
2. Agrega tu dominio
3. En tu proveedor DNS, crea un registro CNAME apuntando a la URL de Render

## ⏱️ Tiempo Estimado
- **Configuración**: 15-20 minutos
- **Primer despliegue**: 5-10 minutos
- **Total**: ~30 minutos

## 🔍 Verificación
1. Visita tu dominio
2. Deberías ver la aplicación Laravel funcionando
3. Prueba crear una cuenta de usuario
4. Verifica que las funcionalidades principales funcionen

## 🆘 Si Algo Sale Mal
1. **Revisa los logs** en Render Dashboard → Logs
2. **Verifica las variables** de entorno
3. **Asegúrate** de que la base de datos esté configurada correctamente

## 📞 ¿Necesitas Ayuda?
Si tienes algún problema durante el proceso, compárteme:
- El error específico que ves
- Los logs de Render
- Qué paso estás intentando

¡Tu aplicación Chessistem estará en línea muy pronto! 🎉
