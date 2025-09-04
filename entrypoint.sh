#!/bin/bash
# render จะส่งค่า $PORT เข้ามา ใช้แก้ ports.conf

if [ -n "$PORT" ]; then
  echo "Listen $PORT" > /etc/apache2/ports.conf
fi

exec "$@"
