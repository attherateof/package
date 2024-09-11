<?php

namespace Easy\Eav\Models\Service;

use Easy\Eav\Models\AttributeSet;
use Easy\Eav\Models\EntityType;

class AttributeSetService
{
    /**
     * Populate attribute sets.
     *
     * @param array $sets
     * @param EntityType $entityType
     * @return void
     */
    public function populateSets(array $sets, EntityType $entityType)
    {
        foreach ($sets as $set) {
            AttributeSet::firstOrCreate([
                'code' => $set['code'],
                'entity_type_id' => $entityType->id
            ], [
                'label' => $set['name']
            ]);
        }
    }
}
