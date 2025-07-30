#!/bin/bash
mv .env .env.locale
cp .env.docker.safe .env
docker compose exec app php artisan db:seed
mv .env .env.docker.safe
mv .env.locale .env
