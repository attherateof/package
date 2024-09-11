<?php

namespace Easy\Eav\Models\Service;

use Easy\Eav\Models\Attribute;
use Easy\Eav\Models\EntityType;
use Easy\Eav\Models\AttributeSet;
use Easy\Eav\Models\AttributeGroup;

class AttributeService
{
    /**
     * Populate attributes.
     *
     * @param array $attributes
     * @param EntityType $entityType
     * @return void
     */
    public function populateAttributes(array $attributes, EntityType $entityType)
    {
        foreach ($attributes as $attributeData) {
            $attribute = Attribute::firstOrCreate([
                'code' => $attributeData['code'],
                'entity_type_id' => $entityType->id
            ], [
                'label' => $attributeData['label'],
                'storage' => $attributeData['storage'],
                'input' => $attributeData['input'],
                'is_static' => (int) $attributeData['is_static'],
                'is_unique' => (int) $attributeData['is_unique'],
                'is_required' => (int) $attributeData['is_required'],
                'is_filterable' => (int) $attributeData['is_filterable'],
                'is_searchable' => (int) $attributeData['is_searchable'],
                'default_value' => isset($attributeData['default_value']) ? (int) $attributeData['default_value'] : null
            ]);

            // Attach attribute to set and group
            if (isset($attributeData['belongs_to'])) {
                foreach ($attributeData['belongs_to'] as $relation) {
                    $this->attachAttributeToSetAndGroup($relation['set'], $relation['group'], $attribute);
                }
            }
        }
    }

    /**
     * Attach an attribute to a set and group.
     *
     * @param string $setCode
     * @param string $groupCode
     * @param Attribute $attribute
     * @return void
     */
    protected function attachAttributeToSetAndGroup(string $setCode, string $groupCode, Attribute $attribute)
    {
        $attributeSet = AttributeSet::where('code', $setCode)->first();
        $attributeGroup = AttributeGroup::where('code', $groupCode)->first();

        if ($attributeSet && $attributeGroup) {
            if (!$attribute->attributeSets()->wherePivot('attribute_group_id', $attributeGroup->id)->wherePivot('attribute_set_id', $attributeSet->id)->exists()) {
                $attribute->attributeSets()->attach($attributeSet->id, ['attribute_group_id' => $attributeGroup->id]);
            }
        }
    }
}
