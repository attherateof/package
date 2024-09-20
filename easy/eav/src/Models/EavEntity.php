<?php

namespace Easy\Eav\Models;

class EavEntity
{
    /**
     * EAV type.
     *
     * @var string
     */
    protected $type = '';

    public function get(int $id): array
    {
        return [];
    }

    public function create(array $data): array
    {
        return [];
    }

    public function update(array $data, int $id): array
    {
        return [];
    }

    public function delete(int $id): array
    {
        return [];
    }

    public function search(array $params): array
    {
        return [];
    }
}
