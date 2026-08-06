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

# Rotate queue-worker.log if it exceeds 10MB
rotate_log() {
    local LOG_FILE="$1"
    local MAX_SIZE=$((10 * 1024 * 1024))  # 10MB

    if [ -f "$LOG_FILE" ]; then
        local SIZE=$(stat -f%z "$LOG_FILE" 2>/dev/null || stat -c%s "$LOG_FILE" 2>/dev/null || echo 0)
        if [ "$SIZE" -gt "$MAX_SIZE" ]; then
            mv "$LOG_FILE" "${LOG_FILE}.1"
            echo "$(date '+%Y-%m-%d %H:%M:%S') - Log rotated (previous size: $SIZE bytes)" > "$LOG_FILE"
        fi
    fi
}

# Delete rotated logs older than 30 days
find "$(dirname "$LOG")" -name "queue-worker.log.*" -mtime +30 -delete 2>/dev/null

log() { echo "$(date '+%Y-%m-%d %H:%M:%S') - $*" >> "$LOG"; }

# Rotate before writing
rotate_log "$LOG"

# Log which PHP binary and version is being used (diagnostic)
echo "$(date '+%Y-%m-%d %H:%M:%S') - Using PHP: $PHP_BIN ($($PHP_BIN -v 2>&1 | head -1))" >> "$LOG"

(
    flock -w 2 -n 200 || { log "Another worker is running. Exiting."; exit 0; }
    log "Starting queue worker (max 55 seconds)"
    cd "$PROJECT_DIR"
    $PHP_BIN "$ARTISAN" queue:work \
        --queue=default \
        --max-time=55 \
        --timeout=310 \
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
