<?php

namespace Easy\Eav\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $table = 'attributes';

    protected $fillable = [
        'code', 'label', 'storage', 'input', 'is_unique', 'is_required', 'is_filterable', 'is_searchable', 'entity_type_id'
    ];

    // Relationship with EntityType
    public function entityType()
    {
        return $this->belongsTo(EntityType::class);
    }

    // Many-to-Many Relationship with AttributeSet and AttributeGroup via pivot
    public function attributeSets()
    {
        return $this->belongsToMany(AttributeSet::class, 'attribute_group_set_attribute')
                    ->withPivot('attribute_group_id')
                    ->withTimestamps();
    }

    // Many-to-Many Relationship with AttributeGroup via pivot
    public function attributeGroups()
    {
        return $this->belongsToMany(AttributeGroup::class, 'attribute_group_set_attribute')
                    ->withPivot('attribute_set_id')
                    ->withTimestamps();
    }
}
