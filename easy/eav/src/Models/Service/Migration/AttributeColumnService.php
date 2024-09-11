<?php

namespace Easy\Eav\Models\Service\Migration;

use Illuminate\Database\Schema\Blueprint;

class AttributeColumnService
{
    /**
     * Add columns to the table schema based on attribute type.
     *
     * @param Blueprint $table
     * @param array $attribute
     * @return string
     */
    public function addColumnBasedOnType(array $attribute)
    {
        $column = $attribute['code'];
        $columnDefinition = '';

        switch ($attribute['storage']) {
            case 'text':
                $columnDefinition .= '$table->mediumText(\'' . $column . '\')';
                break;
            case 'date':
                $columnDefinition .=' $table->date(\'' . $column . '\')';
                break;
            case 'decimal':
                $columnDefinition .= '$table->decimal(\'' . $column . '\', 8, 4)';
                break;
            case 'int':
                $columnDefinition .= '$table->integer(\'' . $column . '\')';
                break;
            default:
                $columnDefinition .= '$table->string(\'' . $column . '\')';
                break;
        }

        // Handle nullable or required constraint
        if ($attribute['is_required'] !== true) {
            $columnDefinition .= '->nullable()';
        }

        // Handle unique constraint
        if ($attribute['is_unique']) {
            $columnDefinition .= '->unique()';
        }

        return "\t \t \t" . $columnDefinition . ';';
    }
}
