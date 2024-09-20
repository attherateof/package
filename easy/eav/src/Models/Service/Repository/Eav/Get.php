<?php

namespace Easy\Eav\Models\Service\Repository\Eav;

use Easy\Eav\Models\EntityType;
// use Illuminate\Support\Facades\DB;

class Get
{
    public function __construct(
        private readonly EntityType $entityType
    ) {
    }

    // /**
    //  * Populate EAV data (entity types, sets, groups, and attributes)
    //  *
    //  * @param array $data
    //  * @return void
    //  */
    // public function execute(string $entityTypeCode, string $attributeSetCode): ?EntityType
    // {
    //     return EntityType::with([
    //         'attributeSets' => function ($query) use ($attributeSetCode) {
    //             $query->where('code', $attributeSetCode)
    //                 ->with([
    //                     'attributeGroups' => function ($query) {
    //                         $query->with('attributes');
    //                     }
    //                 ]);
    //         }
    //     ])->where('code', $entityTypeCode)->first();
    // }

    /**
     * Get Entity information
     * 
     * @param string $entityTypeCode
     * @param string|null $attributeSetCode
     * 
     * @return EntityType|null
     */
    public function execute(string $entityTypeCode, ?string $attributeSetCode = null): ?EntityType
    {
        // Initialize the query without executing it
        $query = $this->entityType::query();

        // If attributeSetCode is provided, include the attributeSets and related relationships
        if ($attributeSetCode !== null) {
            $query->with([
                'attributeSets' => function ($query) use ($attributeSetCode) {
                    $query->where('code', $attributeSetCode)
                        ->with([
                            'attributeGroups' => function ($query) {
                                $query->with('attributes');
                            }
                        ]);
                }
            ]);
        }

        // Add the condition for entityTypeCode
        $query->where('code', $entityTypeCode);

        // Execute and return the first result
        return $query->first();
    }

    public function attributes(string $entityTypeCode, string $attributeSetCode) {
        
    }
}