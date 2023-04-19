#!/bin/bash

git pull

php artisan config:cache
php artisan route:clear
