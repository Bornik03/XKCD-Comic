#!/bin/bash

CRON_JOB="0 0 * * * /usr/bin/php $(pwd)/cron.php"

( crontab -l 2>/dev/null | grep -v 'cron.php' ; echo "$CRON_JOB" ) | crontab -