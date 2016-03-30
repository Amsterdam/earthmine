#!/usr/bin/env bash

set -u
set -e

echo Starting server
service php5-fpm start
nginx -g "daemon off;"
