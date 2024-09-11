<?php

namespace Easy\Eav\Models\Service;

use Easy\Eav\Models\Service\EntityTypeService;
use Easy\Eav\Models\Service\AttributeSetService;
use Easy\Eav\Models\Service\AttributeGroupService;
use Easy\Eav\Models\Service\AttributeService;
use Easy\Eav\Models\Service\EntityTableService;
use Illuminate\Support\Facades\DB;

class EavService
{
    public function __construct(
        private readonly EntityTypeService $entityTypeService,
        private readonly AttributeSetService $attributeSetService,
        private readonly AttributeGroupService $attributeGroupService,
        private readonly AttributeService $attributeService,
        private readonly EntityTableService $entityTableService
    ) {
    }

    /**
     * Populate EAV data (entity types, sets, groups, and attributes)
     *
     * @param array $data
     * @return void
     */
    public function populateEavData(array $data)
    {
        DB::transaction(function () use ($data) {
            // Populate Entity Type
            $entityType = $this->entityTypeService->firstOrCreate($data['entity_type_code']);

            // Populate Attribute Sets
            if (isset($data['sets'])) {
                $this->attributeSetService->populateSets($data['sets'], $entityType);
            }

            // Populate Attribute Groups
            if (isset($data['groups'])) {
                $this->attributeGroupService->populateGroups($data['groups'], $entityType);
            }

            // Populate Attributes
            if (isset($data['attributes'])) {
                $this->attributeService->populateAttributes($data['attributes'], $entityType);
                // Create entity type specific tables
                $this->entityTableService->createEntityTypeTables($entityType, $data['attributes']);
            }
        });
    }
}
