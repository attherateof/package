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
        // Step 1: Get the correct entity table from the entity_types table
        $entityType = DB::table('entity_types')
            ->where('code', $entityTypeCode)
            ->first();

        if (!$entityType) {
           throw new \Exception("Entity type does not exists");
        }

        // Step 2: Query static data from the main entity table
        $mainEntityTable = $entityType->entity_table;

        $query = DB::table($mainEntityTable . ' as e')
        ->select('e.*') // Select all static fields
        // Step 3: Join non-static attribute tables
        ->leftJoin($entityTypeCode ."_entity_text as t", 't.entity_id', '=', 'e.id')
        ->leftJoin('attributes as a_text', function($join) use ($entityType) {
            $join->on('t.attribute_id', '=', 'a_text.id')
                ->where('a_text.storage', 'text')
                ->where('a_text.entity_type_id', '=', $entityType->id);
        })
        ->leftJoin($entityTypeCode ."_entity_int as i", 'i.entity_id', '=', 'e.id')
        ->leftJoin('attributes as a_int', function($join) use ($entityType) {
            $join->on('i.attribute_id', '=', 'a_int.id')
                ->where('a_int.storage', 'int')
                ->where('a_int.entity_type_id', '=', $entityType->id);
        })
        ->leftJoin($entityTypeCode ."_entity_decimal as d", 'd.entity_id', '=', 'e.id')
        ->leftJoin('attributes as a_decimal', function($join) use ($entityType) {
            $join->on('d.attribute_id', '=', 'a_decimal.id')
                ->where('a_decimal.storage', 'decimal')
                ->where('a_decimal.entity_type_id', '=', $entityType->id);
        })
        ->leftJoin($entityTypeCode ."_entity_string as s", 's.entity_id', '=', 'e.id')
        ->leftJoin('attributes as a_string', function($join) use ($entityType) {
            $join->on('s.attribute_id', '=', 'a_string.id')
                ->where('a_string.storage', 'string')
                ->where('a_string.entity_type_id', '=', $entityType->id);
        })
        ->leftJoin($entityTypeCode ."_entity_datetime as dt", 'dt.entity_id', '=', 'e.id')
        ->leftJoin('attributes as a_datetime', function($join) use ($entityType) {
            $join->on('dt.attribute_id', '=', 'a_datetime.id')
                ->where('a_datetime.storage', 'datetime')
                ->where('a_datetime.entity_type_id', '=', $entityType->id);
        })
        // Step 4: Where clause to get the specific entity by ID
        ->where('e.id', $id)
        ->get();

        $entityData = [];
        foreach ($query as $row) {
            // Static fields are directly part of the row
            $entityData['static'] = (array) $row;
    
            // Non-static attributes can be mapped by their attribute code
            if ($row->text_value) {
                $entityData[$row->attribute_code_text] = $row->text_value;
            }
            if ($row->int_value) {
                $entityData[$row->attribute_code_int] = $row->int_value;
            }
            if ($row->decimal_value) {
                $entityData[$row->attribute_code_decimal] = $row->decimal_value;
            }
            if ($row->string_value) {
                $entityData[$row->attribute_code_string] = $row->string_value;
            }
            if ($row->datetime_value) {
                $entityData[$row->attribute_code_datetime] = $row->datetime_value;
            }
        }

        return $entityData;
    }
}