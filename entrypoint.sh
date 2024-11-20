#!/bin/bash

#
# Copyright (c) 2024.
# Talha Fakhar
#
# https://github.com/talhafakhar
#

# Path to the marker file in Laravel's storage directory
INIT_MARKER="/var/www/html/storage/first_run_complete"

# Check if the initialization has already been done
if [ ! -f "$INIT_MARKER" ]; then
    echo "Running first-time setup..."

    # Wait for PostgreSQL to be ready, connecting to the 'postgres' maintenance database
    until PGPASSWORD=$DB_PASSWORD psql -h "$DB_HOST" -U "$DB_USERNAME" -d "postgres" -c '\q'; do
      >&2 echo "Postgres is unavailable - sleeping"
      sleep 1
    done

    # Wait for Redis to be ready
    until redis-cli -h "$REDIS_HOST" ping; do
      >&2 echo "Redis is unavailable - sleeping"
      sleep 1
    done

    # Generate new application key
    php artisan key:generate

    # Check if PostgreSQL database exists, and create it if it doesn't
    # Assumes you have environment variables set for DB connection
    DB_EXISTS=$(PGPASSWORD=$DB_PASSWORD psql -h $DB_HOST -U $DB_USERNAME -lqt | cut -d \| -f 1 | grep -w $DB_DATABASE)
    if [ -z "$DB_EXISTS" ]; then
        echo "Database $DB_DATABASE does not exist. Creating..."
        PGPASSWORD=$DB_PASSWORD createdb -h $DB_HOST -U $DB_USERNAME $DB_DATABASE
    fi

    # Run Laravel migrations
    php artisan migrate

    # Create a marker file to indicate that setup is complete
    touch "$INIT_MARKER"
else
    echo "Initialization already done, skipping..."
fi

# Continue to main command
exec apache2-foreground
