<?php

namespace Easy\Eav\Models\Service\Repository\Entity;

use Easy\Eav\Models\EntityType;
use Easy\Eav\Models\Service\Repository\Eav\Get as EavGetService;
use Illuminate\Support\Facades\DB;
use Exception;

class EntitySaveService
{
    public function __construct(
        private readonly EntityType $entityType,
        private readonly EavGetService $eavGet
    ) {
    }

    /**
     * Save or update attribute data
     *
     * @param array $data
     * @param string $entityTypeCode
     * @param string $attributeSetCode
     * @param int|null $entityId
     * @throws Exception
     * @return void
     */
    public function execute(array $data, string $entityTypeCode, string $attributeSetCode, ?int $entityId = null)
    {
        $entityType = $this->eavGet->execute($entityTypeCode, $attributeSetCode);

        if (!$entityType) {
            throw new Exception("Entity type not found.");
        }

        DB::transaction(function () use ($data, $entityType, $entityId) {
            // Step 1: Process static data
            $staticDataToInsert = $this->prepareStaticData($data, $entityType);
            if (!empty($staticDataToInsert)) {
                if ($entityId) {
                    $this->updateStaticData($staticDataToInsert, $entityType, $entityId);
                } else {
                    $entityId = $this->insertStaticData($staticDataToInsert, $entityType);
                }
            }

            // Step 2: Process non-static data
            $nonStaticData = $this->prepareNonStaticData($data, $entityType, $entityId);
            if (!empty($nonStaticData)) {
                $this->saveOrUpdateNonStaticData($nonStaticData, $entityType);
            }
        });
    }

    /**
     * Prepare static data to insert or update.
     *
     * @param array $data
     * @param EntityType $entityType
     * @return array
     */
    private function prepareStaticData(array $data, $entityType): array
    {
        $staticData = [];

        foreach ($data as $attributeCode => $value) {
            if ($this->isStaticAttribute($attributeCode, $entityType)) {
                $staticData[$attributeCode] = $value;
            }
        }

        return $staticData;
    }

    /**
     * Insert static data into the main entity table.
     *
     * @param array $staticData
     * @param EntityType $entityType
     * @return int Inserted entity ID
     */
    private function insertStaticData(array $staticData, $entityType): int
    {
        return DB::table($entityType->entity_table)->insertGetId($staticData);
    }

    /**
     * Update static data in the main entity table.
     *
     * @param array $staticData
     * @param EntityType $entityType
     * @param int $entityId
     * @return void
     */
    private function updateStaticData(array $staticData, $entityType, int $entityId): void
    {
        DB::table($entityType->entity_table)->where('id', $entityId)->update($staticData);
    }

    /**
     * Prepare non-static data to insert or update.
     *
     * @param array $data
     * @param EntityType $entityType
     * @return array
     */
    private function prepareNonStaticData(array $data, $entityType, int $mainTableId): array
    {
        $nonStaticData = [
            'text' => [],
            'int' => [],
            'string' => [],
            'decimal' => [],
            'datetime' => []
        ];

        foreach ($data as $attributeCode => $value) {
            $attribute = $this->getAttributeByCode($attributeCode, $entityType);

            if (!$attribute->is_static) {
                $nonStaticData[$attribute->storage][] = [
                    'attribute_id' => $attribute->id,
                    'main_table_id' => $mainTableId,
                    'value' => $value,
                ];
            }
        }

        return $nonStaticData;
    }

    /**
     * Save or update non-static attribute values.
     *
     * @param array $nonStaticData
     * @param EntityType $entityType
     * @param int $entityId
     * @return void
     */
    private function saveOrUpdateNonStaticData(array $nonStaticData, $entityType): void
    {
        foreach ($nonStaticData as $storageType => $data) {
            if (!empty($data)) {
                $tableSuffix = $this->getTableSuffix($storageType);
                $tableName = $entityType->entity_table . $tableSuffix;
                $updates = [];
                $inserts = [];
    
                foreach ($data as $entry) {
                    $existingRecord = DB::table($tableName)
                        ->where('attribut_id', $entry['attribut_id'])
                        ->where('main_table_id', $entry['main_table_id'])
                        ->where('id', $entry['id'])
                        ->first();
    
                    if ($existingRecord) {
                        // Prepare data for batch update
                        $updates[] = [
                            'id' => $existingRecord->id,
                            'value' => $entry['value'],
                        ];
                    } else {
                        // Prepare data for batch insert
                        $inserts[] = [
                            'main_table_id' => $entry['main_table_id'],
                            'attribut_id' => $entry['attribut_id'],
                            'value' => $entry['value'],
                        ];
                    }
                }

                foreach ($updates as $update) {
                    DB::table($tableName)
                        ->where('id', $update['id'])
                        ->update(['value' => $update['value']]);
                }
                if (!empty($inserts)) {
                    DB::table($tableName)->insert($inserts);
                }
            }
        }
    }


    /**
     * Determine if an attribute is static based on the attribute code.
     *
     * @param string $attributeCode
     * @return bool
     */
    private function isStaticAttribute(string $attributeCode, EntityType $entityType): bool
    {
        return ($attribute = $this->getAttributeByCode($attributeCode, $entityType)) && $attribute->is_static;
    }

    /**
     * Summary of getAttributeByCode
     * @param string $attributeCode
     * @param \Easy\Eav\Models\EntityType $entityType
     * @return mixed
     */
    private function getAttributeByCode(string $attributeCode, EntityType $entityType)
    {
        $attributeSet = $entityType->attributeSets->first();
        $attributeGroups = $attributeSet->attributeGroups();
        foreach ($attributeGroups as $group) {
            $attributes = $group->attributes();
            foreach ($attributes as $attribute) {
                if ($attribute->code === $attributeCode) {
                    return $attribute;
                }
            }
        }

        return null;
    }


    /**
     * Get table suffix for a given storage type.
     *
     * @param string $storageType
     * @return string
     */
    private function getTableSuffix(string $storageType): string
    {
        return match ($storageType) {
            'text' => '_entity_text',
            'int' => '_entity_int',
            'string' => '_entity_string',
            'decimal' => '_entity_decimal',
            'datetime' => '_entity_datetime',
            default => throw new Exception("Unknown storage type."),
        };
    }
}
