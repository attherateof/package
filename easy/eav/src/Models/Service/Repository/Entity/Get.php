<?php

namespace Easy\Eav\Models\Service\Repository\Entity;

use Easy\Eav\Models\EntityType;
use Illuminate\Support\Facades\DB;
use Easy\Eav\Models\Service\Repository\Eav\Get as EavGet;

class Get
{
    public function __construct(
        private readonly EntityType $entityType,
        private readonly EavGet $eavGet
    ) {
    }

    /**
     * Summary of execute
     * @param string $entityTypeCode
     * @param int $id
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function execute(string $entityTypeCode, int $id)
    {
        $entity = $this->loadEntity($id, $entityTypeCode);

        if (!$entity) {
            throw new \Exception("Entity not found.");
        }

        $attributeGroups = $this->eavGet
            ->execute(
                $entityTypeCode,
                $entity->attribute_set_code
                )
            ->attributeSets
            ->first()
            ->attributeGroups;
        
        $entityArray = [];
        foreach ($attributeGroups as $attributeGroup) {
            $attrbutes = $attributeGroup->attributes;
            foreach ($attributeGroup->attributes as $attribute) {
                if (condition) {
                    # code...
                }
                // $entityArray
            }
        }
    }


    private function loadEntity(int $id, string $entityTypeCode)  {

        return DB::table($entityTypeCode . '_entity as e')
            ->select(
                'e.*',
                't.attribute_id as text_attribute_id', 't.value as text_value',
                'i.attribute_id as int_attribute_id', 'i.value as int_value',
                'd.attribute_id as decimal_attribute_id', 'd.value as decimal_value',
                's.attribute_id as string_attribute_id', 's.value as string_value',
                'dt.attribute_id as datetime_attribute_id', 'dt.value as datetime_value'
            )
            ->leftJoin($entityTypeCode . "_entity_text as t", 't.main_table_id', '=', 'e.id')
            ->leftJoin($entityTypeCode . "_entity_int as i", 'i.main_table_id', '=', 'e.id')
            ->leftJoin($entityTypeCode . "_entity_decimal as d", 'd.main_table_id', '=', 'e.id')
            ->leftJoin($entityTypeCode . "_entity_string as s", 's.main_table_id', '=', 'e.id')
            ->leftJoin($entityTypeCode . "_entity_datetime as dt", 'dt.main_table_id', '=', 'e.id')
            ->where('e.id', $id)
            ->get();

    }
}