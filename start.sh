#!/bin/bash

echo "=== Active User ==="
id

echo "=== Checking Nginx Config Files ==="
# Find and modify any Nginx config file in standard locations if they exist and are writable
for cfg in /etc/nginx/nginx.conf /etc/nginx/sites-enabled/default /nginx.conf /etc/nginx/sites-available/default; do
  if [ -f "$cfg" ]; then
    echo "Found config at $cfg"
    if grep -q "client_max_body_size" "$cfg"; then
      echo "Updating client_max_body_size in $cfg"
      sed -i 's/client_max_body_size [^;]*/client_max_body_size 50M/g' "$cfg" || echo "Failed to modify $cfg"
    else
      echo "Adding client_max_body_size to $cfg"
      # Try to add it inside http or server block
      if grep -q "http {" "$cfg"; then
        sed -i '/http {/a \    client_max_body_size 50M;' "$cfg" || echo "Failed to add to http in $cfg"
      elif grep -q "server {" "$cfg"; then
        sed -i '/server {/a \        client_max_body_size 50M;' "$cfg" || echo "Failed to add to server in $cfg"
      else
        echo "client_max_body_size 50M;" >> "$cfg" || echo "Failed to append to $cfg"
      fi
    fi
  fi
done

echo "=== Checking PHP Configuration ==="
# Set PHP upload limit to 50M as well
PHP_INI_DIR=$(php -r 'echo ini_get("cfg_file_path");' | xargs dirname 2>/dev/null || echo "")
if [ -n "$PHP_INI_DIR" ] && [ -d "$PHP_INI_DIR" ]; then
  echo "PHP ini directory: $PHP_INI_DIR"
  mkdir -p "$PHP_INI_DIR/conf.d" || true
  echo "upload_max_filesize = 50M" > "$PHP_INI_DIR/conf.d/99-uploads.ini" || echo "Failed to write PHP upload_max_filesize"
  echo "post_max_size = 50M" >> "$PHP_INI_DIR/conf.d/99-uploads.ini" || echo "Failed to write PHP post_max_size"
else
  echo "PHP ini directory not found. Creating .user.ini in public/ directory"
  echo "upload_max_filesize = 50M" > public/.user.ini
  echo "post_max_size = 50M" >> public/.user.ini
fi

echo "=== Executing Default Zeabur Startup ==="
exec _startup
