<?php

namespace App\Database;

use Illuminate\Database\Connectors\PostgresConnector as BaseConnector;
use PDO;

/**
 * Postgres connector for the remote pooler: persistent connections use PersistentPool's name, and
 * only web requests keep them (artisan, the queue worker and tests connect fresh each run).
 */
class PostgresConnector extends BaseConnector
{
    public function getOptions(array $config)
    {
        $options = parent::getOptions($config);

        if (! empty($options[PDO::ATTR_PERSISTENT])) {
            $options[PDO::ATTR_PERSISTENT] = PHP_SAPI === 'cli' ? false : PersistentPool::name();
        }

        return $options;
    }

    /**
     * "public" is already where the server looks, so setting it again would only cost a round trip
     * (~0.25s) on every request. Any other schema is still set.
     */
    protected function configureSearchPath($connection, $config)
    {
        if (($config['search_path'] ?? null) === 'public') {
            return;
        }

        parent::configureSearchPath($connection, $config);
    }
}
