<?php

namespace Easy\Eav\Models\Service;

use Easy\Eav\Models\AttributeGroup;
use Easy\Eav\Models\AttributeSet;
use Easy\Eav\Models\EntityType;

class AttributeGroupService
{
    /**
     * Populate attribute groups.
     *
     * @param array $groups
     * @param EntityType $entityType
     * @return void
     */
    public function populateGroups(array $groups, EntityType $entityType)
    {
        foreach ($groups as $group) {
            $attributeGroup = AttributeGroup::firstOrCreate([
                'code' => $group['code'],
                'entity_type_id' => $entityType->id
            ], [
                'label' => $group['name'],
                'sort_order' => $group['sort_order']
            ]);

            // Attach groups to sets
            if (isset($group['sets'])) {
                $this->attachGroupToSets($group['sets'], $attributeGroup);
            }
        }
    }

    /**
     * Attach an attribute group to multiple sets.
     *
     * @param array $setCodes
     * @param AttributeGroup $attributeGroup
     * @return void
     */
    protected function attachGroupToSets(array $setCodes, AttributeGroup $attributeGroup)
    {
        $attributeSets = AttributeSet::whereIn('code', $setCodes)->get();

        foreach ($attributeSets as $set) {
            if (!$attributeGroup->attributeSets()->where('attribute_set_id', $set->id)->exists()) {
                $attributeGroup->attributeSets()->attach($set->id);
            }
        }
    }
}
