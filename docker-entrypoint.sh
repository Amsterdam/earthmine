#!/usr/bin/env bash

set -u
set -e

cat > /app/vars.inc.php <<EOF
\$config['secret'] = '${EARTHMINE_SECRET}';
\$config['key'] = '${EARTHMINE_KEY}';
EOF

echo Starting server
service php5-fpm start
nginx -g "daemon off;"
