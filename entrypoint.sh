#!/bin/bash
set -e

# Render จะส่งค่า $PORT มา
if [ -n "$PORT" ]; then
  echo "Listen $PORT" > /etc/apache2/ports.conf
fi

# ส่งต่อไปยังคำสั่ง CMD (apache2-foreground)
exec "$@"
