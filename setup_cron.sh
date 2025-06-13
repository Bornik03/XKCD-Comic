#!/bin/bash

CRON_JOB="*/15 * * * * /usr/bin/php $(pwd)/cron.php >> $(pwd)/cron.log 2>&1"
# Remove previous cron line with cron.php to avoid duplicates
( crontab -l 2>/dev/null | grep -v 'cron.php' ; echo "$CRON_JOB" ) | crontab -
