<?php

namespace Easy\Eav\Models\Service\Migration;

use Easy\Eav\Models\EntityType;

class EntityTypeService
{
    /**
     * Create or get an Entity Type.
     *
     * @param string $code
     * @return EntityType
     */
    public function firstOrCreate(string $code): EntityType
    {
        return EntityType::firstOrCreate(['code' => $code], [
            'entity_table' => $code . '_entity'
        ]);
    }
}
