<?php

namespace Easy\Eav\Models\Service\Repository\Eav;

use Easy\Eav\Models\EntityType;
use Illuminate\Support\Facades\DB;

class Get
{
    public function __construct(
        private readonly EntityType $entityType
    ) {
    }

    /**
     * Populate EAV data (entity types, sets, groups, and attributes)
     *
     * @param array $data
     * @return void
     */
    public function execute(string $entityTypeCode, string $attributeSetCode): ?EntityType
    {
        return EntityType::with([
            'attributeSets' => function ($query) use ($attributeSetCode) {
                $query->where('code', $attributeSetCode)
                    ->with([
                        'attributeGroups' => function ($query) {
                            $query->with('attributes');
                        }
                    ]);
            }
        ])->where('code', $entityTypeCode)->first();
    }
}