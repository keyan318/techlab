<?php

namespace App\Database;

use Illuminate\Database\PostgresConnection as BaseConnection;
use PDO;

/** Postgres connection whose reconnect after a lost connection never gets the dead one back (see PersistentPool). */
class PostgresConnection extends BaseConnection
{
    public function reconnect()
    {
        // The PDO is still set when the connection was lost (it is null after a plain disconnect()).
        if ($this->pdo !== null && ! empty($this->getConfig('options')[PDO::ATTR_PERSISTENT] ?? null)) {
            PersistentPool::rotate();
        }

        return parent::reconnect();
    }
}
