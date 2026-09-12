#!/bin/sh
# Container entrypoint: cache configuration from the runtime environment,
# apply pending migrations, then run Apache in the foreground.
set -e

cd /var/www/html

# Render mounts secret files (e.g. the database CA certificate) readable by root
# only, but Apache's PHP runs as www-data. Copy the certificate to a location
# www-data can read (outside public/) before the config is cached.
if [ -n "${MYSQL_ATTR_SSL_CA:-}" ] && [ -f "$MYSQL_ATTR_SSL_CA" ]; then
    cp "$MYSQL_ATTR_SSL_CA" storage/mysql-ca.pem
    chmod 644 storage/mysql-ca.pem
    export MYSQL_ATTR_SSL_CA=/var/www/html/storage/mysql-ca.pem
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

# Applies only migrations that have not run yet — never drops or resets data.
# A database outage should not stop the web server from starting, so failures
# are logged loudly instead of aborting the container.
php artisan migrate --force || echo "WARNING: php artisan migrate failed — check the database connection." >&2

# First deploy only (opt-in with SEED_DEMO_DATA=true): load the demo dataset,
# and only when the users table is empty — an existing database is never re-seeded.
if [ "${SEED_DEMO_DATA:-false}" = "true" ]; then
    users=$(php artisan tinker --execute='echo \App\Models\User::count();' 2>/dev/null | tail -n 1)
    if [ "$users" = "0" ]; then
        echo "Empty database: seeding demo data (DemoSeeder)."
        php artisan db:seed --class=DemoSeeder --force
    else
        echo "Demo seed skipped: database already has users (count: ${users:-unknown})."
    fi
fi

# Caches above were written as root; Apache runs as www-data.
chown -R www-data:www-data storage bootstrap/cache

exec apache2-foreground
