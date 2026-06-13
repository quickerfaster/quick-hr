#!/bin/bash
PROJECT_DIR="/home/quickerf/quick_hr"
LOCKFILE="$PROJECT_DIR/storage/framework/queue-worker.lock"
PHP_BIN="/usr/local/bin/php"
ARTISAN="$PROJECT_DIR/artisan"
LOG="$PROJECT_DIR/storage/logs/queue-worker.log"

log() { echo "$(date '+%Y-%m-%d %H:%M:%S') - $*" >> "$LOG"; }

(
    flock -w 2 -n 200 || { log "Another worker is running. Exiting."; exit 0; }
    log "Starting queue worker (max 55 seconds)"
    cd "$PROJECT_DIR"
    $PHP_BIN "$ARTISAN" queue:work --max-time=55 --sleep=3 --tries=3 --quiet
    log "Worker finished (time limit reached or no jobs)"
) 200>"$LOCKFILE"
