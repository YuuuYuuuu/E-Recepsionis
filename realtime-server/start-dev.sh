#!/usr/bin/env bash
# Jalankan dari Terminal; biarkan jendela ini terbuka selama uji live chat.
cd "$(dirname "$0")"
if [[ ! -d node_modules ]]; then
  npm install
fi
exec npm start
