<?php

namespace Easy\Eav\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeSet extends Model
{
    use HasFactory;

    /**
    * The table associated with the model.
    *
    * @var string
    */
   protected $table = 'attribute_sets';

   /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    protected $fillable = ['code', 'label', 'entity_type_id'];

    // Relationship with EntityType
    public function entityType()
    {
        return $this->belongsTo(EntityType::class);
    }

    // Many-to-Many Relationship with AttributeGroup
    public function attributeGroups()
    {
        return $this->belongsToMany(AttributeGroup::class, 'attribute_set_group')
                    ->withTimestamps();
    }

    // Many-to-Many Relationship with Attributes via pivot
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_group_set_attribute')
                    ->withPivot('attribute_group_id')
                    ->withTimestamps();
    }
}
