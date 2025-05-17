#!/bin/bash

# Get the absolute path of the backup_cron.php file
SCRIPT_PATH="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)/backup_cron.php"

# Create a temporary file for the crontab
TEMP_CRON=$(mktemp)

# Export current crontab
crontab -l > $TEMP_CRON 2>/dev/null || echo "# Backup System Cron Jobs" > $TEMP_CRON

# Add the backup cron job (runs every hour)
echo "0 * * * * php $SCRIPT_PATH >> $(dirname "$SCRIPT_PATH")/backup_cron.log 2>&1" >> $TEMP_CRON

# Install the new crontab
crontab $TEMP_CRON

# Clean up
rm $TEMP_CRON

echo "Backup cron job has been set up successfully!"
echo "The backup script will run every hour."
echo "Logs will be written to: $(dirname "$SCRIPT_PATH")/backup_cron.log" 