# Guía de Despliegue — Bolsa de Empleo UNIPAZ en Railway

Última actualización: 21 de abril de 2026

---

## 1. Descripción General del Proyecto

La **Bolsa de Empleo UNIPAZ** es una aplicación web desarrollada con Laravel 12 que conecta estudiantes de la Universidad de la Paz (Barrancabermeja, Colombia) con oportunidades laborales publicadas por empresas de la región.

### Stack tecnológico

- **Backend:** PHP 8.2+ con Laravel 12
- **Frontend:** Tailwind CSS 4.0, Vite 7.0.7
- **Base de datos:** SQLite (producción en Railway)
- **Autenticación:** Laravel Socialite 5.15 (Google OAuth para estudiantes), autenticación tradicional para empresas y administradores
- **Correo:** SMTP vía Gmail con contraseñas de aplicación

### Roles del sistema

- **Admin** — gestiona empresas, usuarios y aprobaciones
- **Empresa** — publica ofertas de empleo y revisa postulaciones
- **Estudiante** — navega ofertas y se postula usando Google OAuth

---

## 2. Requisitos Previos

Antes de iniciar el despliegue se necesita:

1. Una cuenta en [Railway](https://railway.app) (plan gratuito o de pago).
2. El repositorio del proyecto alojado en GitHub (Railway se conecta directamente al repo).
3. Credenciales de **Google OAuth** configuradas en [Google Cloud Console](https://console.cloud.google.com/):
   - `GOOGLE_CLIENT_ID`
   - `GOOGLE_CLIENT_SECRET`
   - URI de redirección autorizada: `https://<tu-dominio>.up.railway.app/auth/google/callback`
4. Una **contraseña de aplicación de Gmail** para el envío de correos (se genera desde la configuración de seguridad de la cuenta de Google).
5. Node.js 20.19+ y PHP 8.2+ instalados localmente (para compilar assets antes del deploy, si es necesario).

---

## 3. Estructura de Archivos Clave para el Despliegue

El proyecto no utiliza Dockerfile, Procfile ni `railway.toml`. Railway detecta automáticamente el proyecto como una aplicación PHP/Laravel a través de **Nixpacks** y utiliza el script `start.sh` como punto de entrada.

### Archivos relevantes

| Archivo | Propósito |
|---|---|
| `start.sh` | Script de arranque ejecutado por Railway en cada despliegue |
| `.env.example` | Plantilla de variables de entorno para producción en Railway |
| `composer.json` | Dependencias PHP (Laravel 12, Socialite, Sanctum) |
| `package.json` | Dependencias Node (Vite, Tailwind CSS) |
| `vite.config.js` | Configuración de compilación de assets frontend |
| `config/database.php` | Configuración de base de datos (SQLite por defecto) |
| `config/services.php` | Configuración de Google OAuth |
| `database/seeders/DatabaseSeeder.php` | Datos iniciales (admin, empresas de prueba, vacantes) |

---

## 4. Script de Arranque (`start.sh`)

Este es el corazón del despliegue. Railway ejecuta este script cada vez que se despliega una nueva versión:

```bash
#!/bin/bash
set -e

echo "=== Bolsa de Empleo UNIPAZ — Iniciando deploy ==="

# 1. Limpiar cachés previos (evita errores con config cacheada vieja)
php artisan config:clear
php artisan route:clear
php artisan view:clear

# 2. Ejecutar migraciones de base de datos
php artisan migrate --force

# 3. Crear enlace simbólico de almacenamiento público
php artisan storage:link || true

# 4. Optimizar para producción (cachear configuración, rutas y vistas)
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 5. Ejecutar seeder solo si la tabla users está vacía (primer deploy)
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "Sembrando datos iniciales..."
    php artisan db:seed --force
fi

echo "=== ¡Listo! Iniciando servidor ==="

# 6. Iniciar servidor PHP en el puerto asignado por Railway
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
```

### ¿Qué hace cada paso?

1. **Limpieza de cachés** — elimina configuración, rutas y vistas cacheadas de despliegues anteriores para evitar inconsistencias.
2. **Migraciones** — aplica automáticamente cualquier migración nueva. El flag `--force` permite ejecutarlas en entorno de producción sin confirmación interactiva.
3. **Storage link** — crea el enlace simbólico `public/storage → storage/app/public` para servir archivos subidos (CVs, logos). El `|| true` evita que falle si el enlace ya existe.
4. **Optimización** — cachea la configuración, rutas y vistas compiladas de Blade para mejorar el rendimiento.
5. **Seeding condicional** — solo siembra datos iniciales si la base de datos está vacía (primer despliegue). Esto crea el usuario administrador, empresas de prueba y vacantes de ejemplo.
6. **Servidor** — inicia el servidor integrado de Laravel escuchando en `0.0.0.0` (todas las interfaces) en el puerto que Railway asigna dinámicamente mediante la variable `$PORT`.

---

## 5. Base de Datos

### Decisión: SQLite

Se eligió SQLite como motor de base de datos por las siguientes razones:

- No requiere un servicio externo de base de datos (ahorro de costos en Railway).
- Ideal para aplicaciones de bajo a mediano tráfico.
- El archivo de base de datos se almacena en `/app/database/database.sqlite` dentro del contenedor.

### Configuración (`config/database.php`)

```php
'default' => env('DB_CONNECTION', 'sqlite'),

'sqlite' => [
    'driver'                  => 'sqlite',
    'database'                => env('DB_DATABASE', database_path('database.sqlite')),
    'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
],
```

### Variable de entorno en Railway

```
DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite
```

### Migraciones incluidas

La aplicación crea las siguientes tablas automáticamente:

| Migración | Tabla | Descripción |
|---|---|---|
| `create_users_table` | `users` | Usuarios del sistema (admin, empresa, estudiante) |
| `create_student_profiles_table` | `student_profiles` | Perfil académico del estudiante |
| `create_companies_table` | `companies` | Datos de las empresas registradas |
| `create_job_postings_table` | `job_postings` | Ofertas de empleo publicadas |
| `create_applications_table` | `applications` | Postulaciones de estudiantes |
| `create_notifications_table` | `notifications` | Notificaciones del sistema |
| `create_cache_table` | `cache` | Almacenamiento de caché |
| `create_jobs_table` | `jobs` / `failed_jobs` / `job_batches` | Sistema de colas |

### Datos iniciales (Seeder)

En el primer despliegue se crean automáticamente:

| Rol | Email | Contraseña |
|---|---|---|
| Admin | `admin@unipaz.edu.co` | `Admin2024*` |
| Empresa (aprobada) | `info@tecnosoluciones.com` | `Empresa2024*` |
| Empresa (pendiente) | `contacto@distribuidoraregional.com` | `Empresa2024*` |
| Estudiante | `carlos.lopez@unipaz.edu.co` | `Student2024*` |

Además se crean 3 vacantes de ejemplo: Desarrollador Web Junior, Asistente Administrativo y Analista de Datos.

> **Importante:** Los estudiantes normalmente ingresan solo con Google OAuth, no con contraseña.

---

## 6. Variables de Entorno en Railway

Desde el dashboard de Railway, en la pestaña **Variables** del servicio, se deben configurar las siguientes variables:

### Aplicación

```
APP_NAME="Bolsa de Empleo UNIPAZ"
APP_ENV=production
APP_KEY=base64:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
APP_DEBUG=false
APP_URL=https://<tu-dominio>.up.railway.app
```

> **Generar APP_KEY:** Ejecutar `php artisan key:generate --show` localmente y copiar el resultado.

### Base de datos

```
DB_CONNECTION=sqlite
DB_DATABASE=/app/database/database.sqlite
```

### Correo electrónico (Gmail SMTP)

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-correo@unipaz.edu.co
MAIL_PASSWORD=tu-app-password-de-16-caracteres
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=bolsaempleo@unipaz.edu.co
MAIL_FROM_NAME="Bolsa de Empleo UNIPAZ"
```

> **Nota:** La contraseña de aplicación se genera en: Google → Seguridad → Verificación en 2 pasos → Contraseñas de aplicación.

### Google OAuth (Socialite)

```
GOOGLE_CLIENT_ID=xxxxxxxxxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-xxxxxxxxxxxxxxx
GOOGLE_REDIRECT_URI=https://<tu-dominio>.up.railway.app/auth/google/callback
```

> Configurar en [Google Cloud Console](https://console.cloud.google.com/) → APIs y servicios → Credenciales → OAuth 2.0.

### Sistema interno

```
LOG_CHANNEL=stack
LOG_LEVEL=error
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120
CACHE_STORE=file
FILESYSTEM_DISK=public
```

---

## 7. Proceso de Despliegue Paso a Paso

### 7.1 Preparar el repositorio

1. Asegurarse de que el código está en un repositorio de GitHub.
2. Verificar que `start.sh` tiene permisos de ejecución:
   ```bash
   git update-index --chmod=+x start.sh
   git commit -m "Dar permisos de ejecución a start.sh"
   git push
   ```
3. Compilar los assets de frontend localmente antes del push (si no se compilan en Railway):
   ```bash
   npm install
   npm run build
   git add public/build
   git commit -m "Compilar assets para producción"
   git push
   ```

### 7.2 Crear el proyecto en Railway

1. Ir a [railway.app](https://railway.app) e iniciar sesión.
2. Click en **"New Project"**.
3. Seleccionar **"Deploy from GitHub repo"**.
4. Autorizar Railway a acceder al repositorio y seleccionar `bolsa-empleo-unipaz-base`.
5. Railway detectará automáticamente que es un proyecto PHP/Laravel usando **Nixpacks**.

### 7.3 Configurar variables de entorno

1. En el dashboard del proyecto, hacer click en el servicio creado.
2. Ir a la pestaña **Variables**.
3. Agregar todas las variables listadas en la sección 6 de este documento.
4. Railway redesplegará automáticamente al guardar las variables.

### 7.4 Configurar el comando de inicio

Railway necesita saber que debe ejecutar `start.sh`. En la configuración del servicio:

1. Ir a **Settings** → **Deploy** → **Custom Start Command**.
2. Establecer: `bash start.sh`

Alternativamente, Nixpacks puede detectar `start.sh` automáticamente si está en la raíz del proyecto.

### 7.5 Configurar el dominio

1. En el dashboard del servicio, ir a **Settings** → **Networking**.
2. Click en **"Generate Domain"** para obtener un subdominio `*.up.railway.app`.
3. Copiar la URL generada y actualizar:
   - La variable `APP_URL` en Railway.
   - La variable `GOOGLE_REDIRECT_URI` con la URL completa del callback.
   - Las URIs autorizadas en Google Cloud Console.

### 7.6 Verificar el despliegue

1. Ir a la pestaña **Deployments** para ver los logs en tiempo real.
2. Buscar los mensajes:
   ```
   === Bolsa de Empleo UNIPAZ — Iniciando deploy ===
   === ¡Listo! Iniciando servidor ===
   ```
3. Acceder a la URL del dominio asignado y verificar que la aplicación carga correctamente.

---

## 8. Compilación de Assets (Frontend)

La aplicación usa **Vite 7.0.7** con **Tailwind CSS 4.0** para compilar los assets del frontend.

### Configuración (`vite.config.js`)

```javascript
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
});
```

### Estrategia de compilación

Los assets compilados (`public/build/`) deben estar incluidos en el repositorio para que Railway los sirva directamente. Si no se incluyen, se debe configurar un paso de build en Railway:

```bash
npm install && npm run build
```

Esto se puede agregar como **Build Command** en Railway Settings → Build, o incluir los assets compilados directamente en el repositorio con `git add public/build`.

---

## 9. Autenticación con Google OAuth

El flujo de autenticación para estudiantes funciona así:

1. El estudiante hace click en "Iniciar sesión con Google" en la página de login.
2. Se redirige a Google para autorización (`/auth/google`).
3. Google redirige de vuelta al callback (`/auth/google/callback`).
4. La aplicación crea o actualiza el perfil del estudiante.

### Configuración requerida en Google Cloud Console

1. Ir a [console.cloud.google.com](https://console.cloud.google.com/).
2. Crear un proyecto (o usar uno existente).
3. Ir a **APIs y servicios** → **Credenciales**.
4. Crear credenciales **OAuth 2.0 Client ID** de tipo "Aplicación web".
5. En **URIs de redirección autorizados**, agregar:
   ```
   https://<tu-dominio>.up.railway.app/auth/google/callback
   ```
6. Copiar el Client ID y Client Secret a las variables de entorno de Railway.

### Configuración en el código (`config/services.php`)

```php
'google' => [
    'client_id'     => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect'      => env('GOOGLE_REDIRECT_URI', '/auth/google/callback'),
],
```

---

## 10. Correo Electrónico

La aplicación envía notificaciones por correo en estos casos:

- Cuando un estudiante se postula a una vacante.
- Cuando cambia el estado de una postulación.
- Cuando se aprueba el registro de una empresa.

### Configuración de Gmail como servidor SMTP

1. Activar la **verificación en 2 pasos** en la cuenta de Gmail.
2. Generar una **contraseña de aplicación** en: Google → Seguridad → Contraseñas de aplicación.
3. Usar esa contraseña de 16 caracteres como valor de `MAIL_PASSWORD` en las variables de entorno.

---

## 11. Consideraciones Importantes

### Persistencia de datos con SQLite

SQLite almacena toda la base de datos en un archivo dentro del contenedor. En Railway, si el servicio se reinicia o se redespliega, **los datos pueden perderse** si Railway no persiste el volumen. Para mitigar esto:

- Considerar agregar un **Volume** en Railway (Settings → Volumes) montado en `/app/database/` para persistir el archivo SQLite entre despliegues.
- Para una solución más robusta a largo plazo, migrar a **PostgreSQL** (Railway lo ofrece como servicio adicional).

### Seguridad

- `APP_DEBUG` debe estar en `false` en producción para no exponer información sensible.
- `APP_KEY` debe ser única y secreta — nunca compartirla ni incluirla en el repositorio.
- Las contraseñas del seeder (`Admin2024*`, `Empresa2024*`) deben cambiarse después del primer despliegue.

### Rendimiento

- Las cachés de configuración, rutas y vistas se regeneran en cada despliegue gracias a `start.sh`.
- El `LOG_LEVEL=error` en producción reduce la escritura a disco.
- La sesión y el caché usan almacenamiento en archivos, lo cual es adecuado para un solo servidor.

---

## 12. Solución de Problemas

### La aplicación no arranca

- Revisar los logs de despliegue en Railway → Deployments.
- Verificar que `start.sh` tiene permisos de ejecución.
- Confirmar que todas las variables de entorno requeridas están configuradas.

### Error 500 al acceder

- Verificar que `APP_KEY` está configurada correctamente.
- Confirmar que las migraciones se ejecutaron (buscar "migrate" en los logs).
- Verificar que `APP_DEBUG=true` temporalmente para ver el error detallado.

### Google OAuth no funciona

- Verificar que `GOOGLE_REDIRECT_URI` coincide exactamente con la URI configurada en Google Cloud Console (incluyendo https y sin barra final).
- Confirmar que las credenciales de Google están correctas en las variables de entorno.

### No se envían correos

- Verificar que la contraseña de aplicación de Gmail es válida y tiene 16 caracteres.
- Confirmar que la verificación en 2 pasos está activa en la cuenta de Gmail.
- Revisar los logs de Laravel para errores de SMTP.

### Los assets CSS/JS no cargan

- Verificar que `public/build/` contiene los archivos compilados (resultado de `npm run build`).
- Confirmar que `APP_URL` coincide con el dominio real asignado por Railway.

---

## 13. Comandos Útiles (Referencia Rápida)

```bash
# Generar APP_KEY localmente
php artisan key:generate --show

# Compilar assets para producción
npm install && npm run build

# Ejecutar migraciones manualmente
php artisan migrate --force

# Limpiar todas las cachés
php artisan optimize:clear

# Ejecutar seeder manualmente
php artisan db:seed --force

# Ver rutas registradas
php artisan route:list
```

---

## 14. Resumen de la Arquitectura de Despliegue

```
GitHub Repository
       │
       ▼
   Railway (Nixpacks auto-detect PHP/Laravel)
       │
       ├── composer install     (dependencias PHP)
       ├── npm install + build  (assets frontend, si configurado)
       │
       ▼
   bash start.sh
       │
       ├── Limpiar cachés
       ├── Ejecutar migraciones
       ├── Crear storage link
       ├── Optimizar (cache config/routes/views)
       ├── Seed condicional (si DB vacía)
       │
       ▼
   php artisan serve --host=0.0.0.0 --port=$PORT
       │
       ▼
   Aplicación disponible en https://<dominio>.up.railway.app
```
