#!/usr/bin/env bash
set -e

PORT="${PORT:-10000}"

# ปรับ Apache ให้ใช้พอร์ตที่ Render กำหนด
sed -i "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf || true
sed -i "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf || true

echo "Starting Apache on port ${PORT}"
exec apache2-foreground
