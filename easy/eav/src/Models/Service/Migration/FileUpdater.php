<?php

namespace Easy\Eav\Models\Service\Migration;

use Illuminate\Support\Facades\Schema;

class FileUpdater
{
    public function __construct(private readonly AttributeColumnService $attributeColumnService)
    {
    }

    /**
     * Populate the new table migration file with schema definitions.
     *
     * @param string $filePath
     * @param array $attributes
     * @return void
     */
    public function populateNewTableMigration(string $filePath, array $attributes)
    {
        $columns = "\t \t \t" . '$table->string(\'attribute_set_code\');' . "\n";
        foreach ($attributes as $key => $attribute) {
            if ($attribute['is_static'] === true) {
                $columns .= $this->attributeColumnService->addColumnBasedOnType($attribute) . "\n";
            }
        }
        $columns .= "\t \t \t" . '$table->foreign(\'attribute_set_code\')->references(\'code\')->on(\'attribute_sets\')->onDelete(\'cascade\');';
        $this->populateMigrationFile($filePath, $columns);
    }

    /**
     * Populate the alter table migration file with missing columns.
     *
     * @param string $filePath
     * @param string $tableName
     * @param array $attributes
     * @return void
     */
    public function populateAlterTableMigration(string $filePath, string $tableName, array $attributes)
    {
        $columns = '';
        $attributeCount = count($attributes);
        foreach ($attributes as $key => $attribute) {
            if (!Schema::hasColumn($tableName, $attribute['code']) && $attribute['is_static'] === true) {
                $columns .= $this->attributeColumnService->addColumnBasedOnType($attribute);
                if ($key < ($attributeCount - 1)) {
                    $columns .= "\n";
                }
            }
        }

        $this->populateMigrationFile($filePath, $columns);
    }

    /**
     * Modify the migration file to add schema details.
     *
     * @param string $filePath
     * @param callable $schemaCallback
     * @return void
     */
    protected function populateMigrationFile(string $filePath, string $columns)
    {
        if (!file_exists($filePath)) {
            return;
        }

        $fileContent = file_get_contents($filePath);

        $fileContent = str_replace(
            '$table->id();',
            '$table->id();' . "\n" . $columns,
            $fileContent
        );

        file_put_contents($filePath, $fileContent);
    }

    /**
     * Populate the alter table migration file with missing columns.
     *
     * @param string $filePath
     * @param string $tableName
     * @param array $attributes
     * @return void
     */
    public function populateEntityTypeValueMigration(string $filePath, string $columnType, string $mainTable)
    {
        $columns = "\t \t \t" . '$table->foreignId(\'attribute_id\')->constrained(\'attributes\')->onDelete(\'cascade\');' . "\n";
        $columns .= "\t \t \t" . '$table->foreignId(\'main_table_id\')->constrained(\'' . $mainTable . '\')->onDelete(\'cascade\');' . "\n";
        $option = '';
        if ($columnType === 'decimal') {
            $option = ', 8, 4';
        }

        $columns .= "\t \t \t" . '$table->' . $columnType . '(\'value\'' . $option . ');';

        $this->populateMigrationFile($filePath, $columns);
    }
}
