<?php

namespace Easy\Eav\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    use HasFactory;
    
    /**
    * The table associated with the model.
    *
    * @var string
    */
   protected $table = 'attribute_groups';

   /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['code', 'label', 'sort_order', 'entity_type_id'];

    // Relationship with EntityType
    public function entityType()
    {
        return $this->belongsTo(EntityType::class);
    }

    // Many-to-Many Relationship with AttributeSet
    public function attributeSets()
    {
        return $this->belongsToMany(AttributeSet::class, 'attribute_set_group')
                    ->withTimestamps();
    }

    // Many-to-Many Relationship with Attributes via pivot
    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'attribute_group_set_attribute')
                    ->withPivot('attribute_set_id')
                    ->withTimestamps();
    }
}
