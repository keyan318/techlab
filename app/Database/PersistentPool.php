<?php

namespace App\Database;

/**
 * Names the persistent database connection each PHP process reuses.
 *
 * The database is a remote Supabase pooler: opening a connection costs ~2s (about ten round trips
 * for TCP, TLS and login), so web requests keep theirs open between requests (DB_PERSISTENT).
 * The catch: PHP keeps a persistent connection in its pool even after the server has dropped it,
 * and Laravel's "lost connection, reconnect and retry" got that same dead connection back, so
 * the retry failed too (the 500s that turned DB_PERSISTENT off on 2026-10-11).
 *
 * So the pool name changes whenever a connection is found dead: a reconnect then always opens a
 * fresh connection, and every later request in that process uses the new one. The pool is per
 * process (the dev server runs PHP_CLI_SERVER_WORKERS of them), so the name is too.
 */
final class PersistentPool
{
    /** The pool name for this process's connection right now. */
    public static function name(): string
    {
        return 'techlab-'.getmypid().'-'.((int) @file_get_contents(self::file()));
    }

    /** This process's connection died: move it to a fresh one. */
    public static function rotate(): void
    {
        @file_put_contents(self::file(), (string) ((int) @file_get_contents(self::file()) + 1), LOCK_EX);
    }

    private static function file(): string
    {
        return storage_path('framework/cache/db-pool-'.getmypid());
    }
}
