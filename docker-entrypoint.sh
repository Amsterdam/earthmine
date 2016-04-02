#!/usr/bin/env bash

set -u
set -e

echo ${EARTHMINE_SECRET} > /app/em_secret
echo ${EARTHMINE_KEY} > /app/em_key

echo Starting server
service php5-fpm start
nginx -g "daemon off;"
