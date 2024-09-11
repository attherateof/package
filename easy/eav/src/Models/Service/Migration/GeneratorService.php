<?php
namespace Easy\Eav\Models\Service\Migration;

use Illuminate\Support\Facades\Artisan;

class GeneratorService
{
    public function __construct(private readonly FileUpdater $migrationFileUpdater)
    {
    }

    /**
     * Create a new migration for a new entity table.
     *
     * @param string $tableName
     * @param array $attributes
     * @return void
     */
    public function createNewTableMigration(string $tableName, array $attributes)
    {
        $migrationName = 'create_' . $tableName . '_table';
        $this->createSchema($migrationName);

        $filePath = $this->getLatestMigrationFilePath($migrationName);

        $this->migrationFileUpdater->populateNewTableMigration($filePath, $attributes);
    }

    /**
     * Create a migration for altering an existing table.
     *
     * @param string $tableName
     * @param array $attributes
     * @return void
     */
    public function createAlterMigration(string $tableName, array $attributes)
    {
        $migrationName = 'alter_' . $tableName . '_table_add_missing_columns';
        $this->createSchema($migrationName);

        $filePath = $this->getLatestMigrationFilePath($migrationName);

        $this->migrationFileUpdater->populateAlterTableMigration($filePath, $tableName, $attributes);
    }

    /**
     * Get the latest migration file path based on the migration name.
     *
     * @param string $migrationName
     * @return string|null
     */
    protected function getLatestMigrationFilePath(string $migrationName): ?string
    {
        $migrationFiles = glob(database_path('migrations') . '/*_' . $migrationName . '.php');
        return array_pop($migrationFiles);
    }

    public function createEntityTypeValueTables(string $entityTypeCode)
    {
        $types = [
            'int' => 'integer',
            'string' => 'string',
            'text' => 'mediumText',
            'datetime' => 'dateTime',
            'decimal' => 'decimal'
        ];

        foreach ($types as $type => $columnType) {
            $migrationName = 'create_' . $entityTypeCode . '_entity_' . $type . '_table';
            $this->createSchema($migrationName);

            $filePath = $this->getLatestMigrationFilePath($migrationName);
            $this->migrationFileUpdater->populateEntityTypeValueMigration($filePath, $columnType);
        }
    }

    public function migrateEav()
    {
        Artisan::call('migrate');
    }

    protected function createSchema(string $migrationName) {
        Artisan::call('make:migration', ['name' => $migrationName]);
    }
}
