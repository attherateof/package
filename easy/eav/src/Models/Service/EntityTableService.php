<?php

namespace Easy\Eav\Models\Service;

use Illuminate\Support\Facades\Schema;
use Easy\Eav\Models\Service\Migration\GeneratorService;
use Easy\Eav\Models\Service\Migration\AttributeColumnService;
use Easy\Eav\Models\EntityType;

class EntityTableService
{
    public function __construct(
        private readonly GeneratorService $migrationGeneratorService,
        private readonly AttributeColumnService $attributeColumnService
    ){
    }

    /**
     * Create entity type specific tables.
     *
     * @param EntityType $entityType
     * @return void
     */
    public function createEntityTypeTables(EntityType $entityType, array $attributes)
    {
        $entityTypeCode = $entityType->code;

        // Create main entity table
        $this->createMainEntityTable($entityTypeCode,  $attributes);

        // Create tables for different value types
        $this->migrationGeneratorService->createEntityTypeValueTables($entityTypeCode);

        // $this->migrationGeneratorService->migrateEav();
    }

    /**
     * Create main entity table.
     *
     * @param string $entityTypeCode
     * @return void
     */
    protected function createMainEntityTable(string $entityTypeCode, array $attributes)
    {
        $tableName = $entityTypeCode . '_entity';

        if (Schema::hasTable($tableName)) {
            // Table exists, create an alter migration if needed
            $this->migrationGeneratorService->createAlterMigration($tableName, $attributes);
        } else {
            // Table doesn't exist, create the table migration
            $this->migrationGeneratorService->createNewTableMigration($tableName, $attributes);
        }
    }
}
