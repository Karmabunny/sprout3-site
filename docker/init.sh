#!/usr/bin/env sh
set -xe
envsubst < /docker/00_docker.ini > /etc/php/8.4/fpm/conf.d/00_docker.ini
envsubst < /docker/pool.conf > /etc/php/8.4/fpm/pool.d/www.conf
exec $FPM_BIN_PATH -F
