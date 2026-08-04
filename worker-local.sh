#!/bin/bash
PROJECT_DIR="./"
LOCKFILE="$PROJECT_DIR/storage/framework/queue-worker.lock"
# Local development: try 'php' from PATH first, then common local paths.
if command -v php &> /dev/null; then
    PHP_BIN="php"
elif command -v /usr/local/bin/php &> /dev/null; then
    PHP_BIN="/usr/local/bin/php"
elif command -v /opt/homebrew/bin/php &> /dev/null; then
    PHP_BIN="/opt/homebrew/bin/php"
else
    PHP_BIN="/usr/bin/php"
fi
ARTISAN="$PROJECT_DIR/artisan"
LOG="$PROJECT_DIR/storage/logs/queue-worker.log"

log() { echo "$(date '+%Y-%m-%d %H:%M:%S') - $*" >> "$LOG"; }

(
    flock -w 2 -n 200 || { log "Another worker is running. Exiting."; exit 0; }
    log "Starting queue worker (max 55 seconds)"
    cd "$PROJECT_DIR"
    $PHP_BIN "$ARTISAN" queue:work \
        --queue=default \
        --max-time=55 \
        --timeout=300 \
        --sleep=3 \
        --tries=3 \
        --quiet
    log "Worker finished (time limit reached or no jobs)"
) 200>"$LOCKFILE"

###### Add cron jobs ######
### running worker ###
# * * * * * /home/quickerf/quick_hr/worker.sh >> /dev/null 2>&1
### cpanel's 2 minutes process restriction limit  test ###
# * * * * * /usr/local/bin/php -r "sleep(120); echo 'done';" >> ~/sleep-test.log 2>&1
