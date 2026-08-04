#!/bin/bash
PROJECT_DIR="/home/quickerf/quick_hr"
LOCKFILE="$PROJECT_DIR/storage/framework/queue-worker.lock"
# PHP_BIN: find the correct PHP 8.4 binary for cPanel.
# .cpanel.yml uses 'ea-php84' as a bare command, but cron has a minimal PATH
# so the bare name fails with "command not found". We try full paths first,
# then fall back to the bare command (works in deployment context).
if command -v /opt/cpanel/ea-php84/root/usr/bin/php &> /dev/null; then
    PHP_BIN="/opt/cpanel/ea-php84/root/usr/bin/php"
elif command -v /opt/alt/php84/usr/bin/php &> /dev/null; then
    PHP_BIN="/opt/alt/php84/usr/bin/php"
elif command -v ea-php84 &> /dev/null; then
    PHP_BIN="ea-php84"
elif command -v /usr/local/bin/php &> /dev/null; then
    PHP_BIN="/usr/local/bin/php"
else
    PHP_BIN="/usr/bin/php"
fi
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
