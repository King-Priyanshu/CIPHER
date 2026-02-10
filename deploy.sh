#!/bin/bash
set -e

echo "Deploying CIPHER..."

# Enter maintenance mode
(php artisan down) || true

# Update codebase
git pull origin main

# Install dependencies
composer install --no-dev --optimize-autoloader

# Install npm dependencies
npm ci

# Build frontend assets
npm run build

# Migrate database
php artisan migrate --force

# Clear caches
php artisan optimize
php artisan view:cache

# Exit maintenance mode
php artisan up

echo "Deployment finished!"
