#!/bin/bash
PROJECT_DIR="/home/quickerf/quick_hr"
LOCKFILE="$PROJECT_DIR/storage/framework/queue-worker.lock"
# FIXED: Use ea-php84 (the cPanel EasyApache PHP 8.4 binary) instead of
# /usr/local/bin/php which is a different/older PHP version. This matches
# what .cpanel.yml uses for all Artisan and Composer commands.
PHP_BIN="ea-php84"
ARTISAN="$PROJECT_DIR/artisan"
LOG="$PROJECT_DIR/storage/logs/queue-worker.log"

log() { echo "$(date '+%Y-%m-%d %H:%M:%S') - $*" >> "$LOG"; }

# Clean up stale lock file if the PID that created it no longer exists.
# This prevents a crashed worker from blocking all subsequent cron runs.
cleanup_stale_lock() {
    if [ -f "$LOCKFILE" ]; then
        # flock doesn't store PID in the file, but we can check if it's truly locked.
        # If flock -n succeeds on a test, the lock is stale.
        if flock -n "$LOCKFILE" -c "true" 2>/dev/null; then
            log "Removing stale lock file (no process holds it)"
            rm -f "$LOCKFILE"
        fi
    fi
}

cleanup_stale_lock

(
    flock -w 5 -n 200 || {
        log "Another worker is running. Exiting."
        exit 0
    }
    log "Starting queue worker (max 55 seconds)"

    # Trap EXIT so the lock is always released, even on crash
    trap 'log "Worker exiting (trap)"; exit 0' EXIT

    cd "$PROJECT_DIR"

    # FIXED: --timeout=300 matches ProcessPayrollRun::$timeout so the
    # worker won't kill long-running payroll jobs prematurely.
    # --queue=default is now explicit (was previously implicit).
    # STDERR is captured to the log so PHP fatal errors are visible.
    $PHP_BIN "$ARTISAN" queue:work \
        --queue=default \
        --max-time=55 \
        --timeout=300 \
        --sleep=3 \
        --tries=3 \
        --quiet \
        2>>"$LOG"

    EXIT_CODE=$?
    log "Worker finished with exit code ${EXIT_CODE}"
) 200>"$LOCKFILE"

###### Add cron jobs ######
### running worker ###
# * * * * * /home/quickerf/quick_hr/worker.sh >> /dev/null 2>&1
### cpanel's 2 minutes process restriction limit  test ###
# * * * * * /usr/local/bin/php -r "sleep(120); echo 'done';" >> ~/sleep-test.log 2>&1
