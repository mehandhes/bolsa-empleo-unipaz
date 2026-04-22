#!/bin/bash
set -e

echo "=== Bolsa de Empleo UNIPAZ — Iniciando deploy ==="

# Asegurar que el directorio de SQLite existe
mkdir -p database
touch database/database.sqlite

# Limpiar cachés previos (evita errores con config cacheada vieja)
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Ejecutar migraciones
php artisan migrate --force

# Crear enlace de almacenamiento público
php artisan storage:link || true

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar seeder solo si la tabla users está vacía
USER_COUNT=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1)
if [ "$USER_COUNT" = "0" ] || [ -z "$USER_COUNT" ]; then
    echo "Sembrando datos iniciales..."
    php artisan db:seed --force
fi

echo "=== ¡Listo! Iniciando servidor ==="

# Iniciar servi