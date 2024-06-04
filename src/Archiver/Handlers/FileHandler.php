<?php

namespace Tatter\Schemas\Archiver\Handlers;

use Tatter\Schemas\Archiver\ArchiverInterface;
use Tatter\Schemas\Archiver\BaseArchiver;
use Tatter\Schemas\Config\Schemas as SchemasConfig;
use Tatter\Schemas\Structures\Mergeable;
use Tatter\Schemas\Structures\Schema;

class FileHandler extends BaseArchiver implements ArchiverInterface
{

    /**
     * Save the config and set up the cache
     *
     * @param SchemasConfig  $config The library config
     * @param CacheInterface $cache  The cache handler to use, null to load a new default
     */

    public $libraryPath = '../vendor/tatter/schemas/src/';
    public $cacheKey = 'schemas-'.ENVIRONMENT;
    public function __construct(?SchemasConfig $config = null, $cache = null)
    {
        parent::__construct($config);
    }

    /**
     * Store the scaffold and each individual table to cache
     *
     * @return bool Success or failure
     */
    public function archive(Schema $schema): bool
    {
        // Grab the tables to store separately
        $tables         = $schema->tables;
        $schema->tables = new Mergeable();

        // Save each individual table
        foreach ($tables as $table) {
            $schema->tables->{$table->name} = true;
            file_put_contents($this->libraryPath."/Files/".$this->cacheKey. '-' . $table->name, serialize($table));
        }

        // Save the scaffold version of the schema

        file_put_contents($this->libraryPath."/Files/".$this->cacheKey,serialize($schema));
        return is_file($this->libraryPath."/Files/".$this->cacheKey);
    }
}
