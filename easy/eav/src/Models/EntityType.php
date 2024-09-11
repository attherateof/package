<?php

namespace Easy\Eav\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntityType extends Model
{
    use HasFactory;

    /**
    * The table associated with the model.
    *
    * @var string
    */
   protected $table = 'entity_types';

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
    protected $fillable = ['code', 'entity_table'];

    
    /**
     * The attributes that are mass assignable.
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributeSets()
    {
        return $this->hasMany(AttributeSet::class);
    }

    /**
     * Relationship with Attribute Groups
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attributeGroups()
    {
        return $this->hasMany(AttributeGroup::class);
    }

    // Relationship with Attribute
    public function attributes()
    {
        return $this->hasMany(Attribute::class);
    }
}
