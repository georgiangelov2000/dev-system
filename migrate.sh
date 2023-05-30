#!/bin/bash
docker-compose exec app php artisan db:create
docker-compose exec app php artisan migrate
