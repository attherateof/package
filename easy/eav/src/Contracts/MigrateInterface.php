<?php

namespace Easy\Eav\Contracs;

interface MigrateInterface
{
    public const STORAGE_TYPE_INT = 'int';
    public const STORAGE_TYPE_STRING = 'string';
    public const INPUT_TYPE_SWITCH = 'switch';
    public const INPUT_TYPE_TEXT = 'text';
    public const INPUT_TYPE_EMAIL = 'email';
    public const INPUT_TYPE_SELECT = 'select';
    public const INPUT_TYPE_DATE = 'date';
}