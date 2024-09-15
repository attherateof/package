<?php

namespace Easy\Eav\Models\Service\Migration;

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
        $this->createMainEntityTable($entityTypeCode,  $attributes);
        sleep(2);
        $this->migrationGeneratorService->createEntityTypeValueTables($entityTypeCode);
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
            $this->migrationGeneratorService->createAlterMigration($tableName, $attributes);
        } else {
            $this->migrationGeneratorService->createNewTableMigration($tableName, $attributes);
        }
    }
}
